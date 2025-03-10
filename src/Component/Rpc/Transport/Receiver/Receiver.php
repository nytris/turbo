<?php

/*
 * Nytris Turbo - Turbocharged HTTP/FastCGI for PHP.
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/nytris/turbo/
 *
 * Released under the MIT license.
 * https://github.com/nytris/turbo/raw/main/MIT-LICENSE.txt
 */

declare(strict_types=1);

namespace Nytris\Turbo\Component\Rpc\Transport\Receiver;

use Exception;
use Nytris\Turbo\Component\Rpc\Call\CallTableInterface;
use Nytris\Turbo\Component\Rpc\Dispatcher\DispatcherInterface;
use Nytris\Turbo\Component\Rpc\Message\MessageType;
use Nytris\Turbo\Component\Rpc\Transport\Transmitter\TransmitterInterface;
use React\Promise\PromiseInterface;

/**
 * Class Receiver.
 *
 * Handles messages received from a remote endpoint.
 *
 * If the incoming message is a return value or error for an RPC call originating
 * from this end, then the promise for the call will be resolved/rejected accordingly.
 *
 * If the incoming message is for an RPC call originating from the remote end,
 * then the dispatcher will be used to dispatch the call to the appropriate handler.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Receiver implements ReceiverInterface
{
    /**
     * @var array{type: MessageType, payload: array<mixed>}[]
     */
    private array $messageQueue = [];
    private bool $paused = true;

    public function __construct(
        private readonly CallTableInterface $callTable,
        private readonly DispatcherInterface $dispatcher,
        private readonly TransmitterInterface $transmitter
    ) {
    }

    /**
     * @inheritDoc
     */
    public function pause(): void
    {
        $this->paused = true;
    }

    /**
     * @inheritDoc
     */
    public function receive(MessageType $type, array $payload): void
    {
        if ($this->paused) {
            $this->messageQueue[] = ['type' => $type, 'payload' => $payload];
            return;
        }

        switch ($type) {
            case MessageType::CALL:
                // A call was received from the remote endpoint, that should be dispatched locally.
                try {
                    $returnValue = $this->dispatcher->dispatch(
                        $payload['handlerFqcn'],
                        $payload['method'],
                        $payload['args']
                    );
                } catch (Exception $exception) {
                    $this->transmitter->transmit(type: MessageType::ERROR, payload: [
                        'callId' => $payload['callId'],
                        'exception' => $exception,
                    ]);
                    return;
                }

                $transmit = function (mixed $returnValue) use ($payload) {
                    $this->transmitter->transmit(type: MessageType::RETURN, payload: [
                        'callId' => $payload['callId'],
                        'returnValue' => $returnValue,
                    ]);
                };

                if ($returnValue instanceof PromiseInterface) {
                    // $returnValue is a promise, so await the final result.
                    $returnValue->then(fn ($returnValue) => $transmit($returnValue));
                } else {
                    $transmit($returnValue);
                }
                break;
            case MessageType::RETURN:
                /*
                 * A return value for a call to the remote end was received from the remote endpoint,
                 * that should be fulfilled locally to provide it back to the local caller.
                 *
                 * Note that if a promise is returned, it will be chained onto the original call promise.
                 */
                $this->callTable->return(callId: $payload['callId'], value: $payload['returnValue']);
                break;
            case MessageType::ERROR:
                /*
                 * An error for a call to the remote end was received from the remote endpoint,
                 * that should be rejected locally to provide it back to the local caller.
                 */
                $this->callTable->throw(callId: $payload['callId'], exception: $payload['exception']);
                break;
        }
    }

    /**
     * @inheritDoc
     */
    public function resume(): void
    {
        $this->paused = false;

        foreach ($this->messageQueue as ['type' => $type, 'payload' => $payload]) {
            $this->receive($type, $payload);
        }
    }
}
