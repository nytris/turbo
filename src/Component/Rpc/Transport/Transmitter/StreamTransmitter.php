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

namespace Nytris\Turbo\Component\Rpc\Transport\Transmitter;

use Nytris\Turbo\Component\Rpc\Message\MessageType;
use Nytris\Turbo\Component\Rpc\Transport\Stream\StreamContextInterface;

/**
 * Class StreamTransmitter.
 *
 * Transmits messages to a remote receiver using a ReactPHP stream.
 * Useful for inter-process communication.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StreamTransmitter implements TransmitterInterface
{
    /**
     * @var array{type: MessageType, payload: array<mixed>}[]
     */
    private array $messageQueue = [];
    private bool $paused = true;

    public function __construct(
        private readonly StreamContextInterface $streamContext
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
    public function resume(): void
    {
        $this->paused = false;

        foreach ($this->messageQueue as ['type' => $type, 'payload' => $payload]) {
            $this->transmit($type, $payload);
        }
    }

    /**
     * @inheritDoc
     */
    public function transmit(MessageType $type, array $payload): void
    {
        if ($this->paused) {
            $this->messageQueue[] = ['type' => $type, 'payload' => $payload];
            return;
        }

        $stream = $this->streamContext->getOutputStream();

        $rawData = serialize(
            value: [
                'type' => $type->value,
                'payload' => $payload,
            ]
        );

        $stream->write(
            data: '__nytris__(' . strlen($rawData) . ')' . $rawData . "\n"
        );
    }
}
