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

namespace Nytris\Turbo\Component\Manifold\Intake\Tappet;

use Nytris\Turbo\Component\Manifold\Exhaust\Rpc\Handler\LaunchCommandHandler;
use Nytris\Turbo\Component\Manifold\Intake\Camshaft\Worker\LaunchingWorkerCollectionInterface;
use Nytris\Turbo\Component\Rpc\Call\CallTable;
use Nytris\Turbo\Component\Rpc\Dispatcher\Dispatcher;
use Nytris\Turbo\Component\Rpc\Transport\Listener\StreamListener;
use Nytris\Turbo\Component\Rpc\Transport\Receiver\Receiver;
use Nytris\Turbo\Component\Rpc\Transport\Stream\StreamContext;
use Nytris\Turbo\Component\Rpc\Transport\Transmitter\StreamTransmitter;
use Nytris\Turbo\Component\Rpc\Transport\Transport;
use React\Promise\PromiseInterface;
use React\Socket\ConnectionInterface;
use React\Socket\SocketServer;
use function React\Promise\resolve;

/**
 * Class Tappet.
 *
 * Handles RPC between Manifold and the Cylinders.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Tappet implements TappetInterface
{
    public function __construct(
        private readonly LaunchingWorkerCollectionInterface $launchingWorkerCollection,
        private readonly string $listenUri
    ) {
    }

    /**
     * @inheritDoc
     */
    public function start(): PromiseInterface
    {
        $server = new SocketServer($this->listenUri);

        $server->on('connection', function (ConnectionInterface $connection) {
            // A newly launched worker has connected back to the Tappet RPC socket,
            // so we need to wait for a `ready` command on this RPC connection
            // in order to link it to its cylinder index.

            $streamContext = new StreamContext();
            $streamContext->useStreams($connection, $connection);
            $listener = new StreamListener($streamContext);
            $dispatcher = new Dispatcher();
            $transmitter = new StreamTransmitter($streamContext);
            $transport = new Transport(
                $transmitter,
                $listener,
                new Receiver(
                    new CallTable(),
                    $dispatcher,
                    $transmitter
                )
            );

            $dispatcher->registerHandler(
                new LaunchCommandHandler(
                    $this->launchingWorkerCollection,
                    $transport,
                    $connection
                )
            );

            $transport->listen();
            $transport->resume();
        });

        return resolve($this);
    }
}
