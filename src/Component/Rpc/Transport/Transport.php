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

namespace Nytris\Turbo\Component\Rpc\Transport;

use Nytris\Turbo\Component\Rpc\Message\MessageType;
use Nytris\Turbo\Component\Rpc\Transport\Listener\ListenerInterface;
use Nytris\Turbo\Component\Rpc\Transport\Receiver\ReceiverInterface;
use Nytris\Turbo\Component\Rpc\Transport\Transmitter\TransmitterInterface;

/**
 * Class Transport.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Transport implements TransportInterface
{
    public function __construct(
        private readonly TransmitterInterface $transmitter,
        private readonly ListenerInterface $listener,
        private readonly ReceiverInterface $receiver
    ) {
    }

    /**
     * @inheritDoc
     */
    public function listen(): void
    {
        $this->listener->listen($this->receiver);
    }

    /**
     * @inheritDoc
     */
    public function pause(): void
    {
        $this->transmitter->pause();
        $this->receiver->pause();
    }

    /**
     * @inheritDoc
     */
    public function resume(): void
    {
        $this->transmitter->resume();
        $this->receiver->resume();
    }

    /**
     * @inheritDoc
     */
    public function send(MessageType $type, array $payload): void
    {
        $this->transmitter->transmit($type, $payload);
    }

    /**
     * @inheritDoc
     */
    public function stop(): void
    {
        $this->listener->stop();
    }
}
