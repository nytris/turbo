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

namespace Nytris\Turbo\Component\Manifold\Intake\Camshaft\Worker;

use Nytris\Turbo\Component\Manifold\Intake\Camshaft\Request\RequestContextInterface;
use Nytris\Turbo\Component\Rpc\Call\CallTable;
use Nytris\Turbo\Component\Rpc\Dispatcher\DispatcherInterface;
use Nytris\Turbo\Component\Rpc\Rpc;
use Nytris\Turbo\Component\Rpc\Transport\Listener\ListenerInterface;
use Nytris\Turbo\Component\Rpc\Transport\Receiver\Receiver;
use Nytris\Turbo\Component\Rpc\Transport\Stream\StreamContextInterface;
use Nytris\Turbo\Component\Rpc\Transport\Transmitter\TransmitterInterface;
use Nytris\Turbo\Component\Rpc\Transport\Transport;

/**
 * Class WorkerFactory.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WorkerFactory implements WorkerFactoryInterface
{
    public function __construct(
        private readonly DispatcherInterface $dispatcher
    ) {
    }

    /**
     * @inheritDoc
     */
    public function createWorker(
        int $cylinderIndex,
        StreamContextInterface $rpcStreamContext,
        RequestContextInterface $requestContext,
        ListenerInterface $listener,
        TransmitterInterface $transmitter
    ): WorkerInterface {
        $callTable = new CallTable();
        $transport = new Transport(
            $transmitter,
            $listener,
            new Receiver(
                $callTable,
                $this->dispatcher,
                $transmitter
            )
        );

        return new Worker(
            $requestContext,
            $transport,
            new Rpc($callTable, $transport),
            $cylinderIndex
        );
    }
}
