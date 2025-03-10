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

namespace Nytris\Turbo\Component\Rpc\Transport\Listener;

use LogicException;
use Nytris\Turbo\Component\Rpc\Message\MessageType;
use Nytris\Turbo\Component\Rpc\Transport\Receiver\ReceiverInterface;
use Nytris\Turbo\Component\Rpc\Transport\Stream\StreamContextInterface;

/**
 * Class StreamListener.
 *
 * Receives messages from a remote receiver using a ReactPHP stream.
 * Useful for inter-process communication.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StreamListener implements ListenerInterface
{
    /**
     * @var callable|null
     */
    private $onData = null;

    public function __construct(
        private readonly StreamContextInterface $streamContext
    ) {
    }

    /**
     * @inheritDoc
     */
    public function listen(ReceiverInterface $receiver): void
    {
        $stream = $this->streamContext->getInputStream();

        $this->onData = function (string $data) use ($receiver) {
            // TODO: Error handling, e.g. unexpected data that does not deserialise.

            // TODO: Implement a more robust message framing protocol,
            //       keep the entire received data in a buffer until a complete message is received.

            do {
                if (preg_match('/^__nytris__\((\d+)\)/', $data, $matches) === 0) {
                    throw new LogicException('Unexpected data received from stream: ' . $data);
                }

                $prefix = $matches[0];
                $prefixLength = strlen($prefix);
                $dataLength = (int) $matches[1];
                $messageData = substr($data, $prefixLength, $dataLength);
                $data = substr($data, $prefixLength + $dataLength + 1);

                $message = unserialize($messageData);

                $receiver->receive(MessageType::from($message['type']), $message['payload']);
            } while ($data !== '');
        };

        $stream->on('data', $this->onData);
    }

    /**
     * @inheritDoc
     */
    public function stop(): void
    {
        $stream = $this->streamContext->getInputStream();

        $stream->removeListener('data', $this->onData);
    }
}
