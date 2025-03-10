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

namespace Nytris\Turbo\Component\Ignition\Intake\Acceptor;

use Nytris\Turbo\Component\Ignition\Connection\ConnectionTableInterface;
use Nytris\Turbo\Component\Manifold\Intake\Rpc\Handler\ConnectionCommandHandler;
use Nytris\Turbo\Component\Rpc\RpcInterface;
use React\Socket\ConnectionInterface;
use React\Socket\ServerInterface;

/**
 * Class Acceptor.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Acceptor implements AcceptorInterface
{
    public function __construct(
        private readonly RpcInterface $manifoldRpc,
        private readonly ConnectionTableInterface $connectionTable
    ) {
    }

    /**
     * @inheritDoc
     */
    public function listen(ServerInterface $server): void
    {
        $nextConnectionId = 0;

        $server->on('connection', function (ConnectionInterface $connection) use (
            &$nextConnectionId
        ) {
            // TODO: Handle rollover?
            $connectionId = $nextConnectionId++;
            $connectionOpen = true;
            $connectionOpenAtManifold = false;
            /** @var string[] $earlyChunks */
            $earlyChunks = [];

            $this->connectionTable->addConnection($connectionId, $connection);

            // Handle data received from the client by the server.
            // See ConnectionCommandHandler for handling of data returned to the client from the server.
            $connection->on('data', function (string $chunk) use (
                $connectionId,
                &$connectionOpenAtManifold,
                &$earlyChunks
            ) {
                if ($connectionOpenAtManifold) {
                    $this->manifoldRpc->call(
                        ConnectionCommandHandler::class,
                        'receive',
                        [$connectionId, $chunk]
                    );
                } else {
                    // Manifold has not yet acknowledged that the connection is open,
                    // so buffer the data until it has.
                    $earlyChunks[] = $chunk;
                }
            });

            $connection->on('close', function () use (
                $connectionId,
                &$connectionOpen
            ) {
                $connectionOpen = false;
                $this->connectionTable->removeConnection($connectionId);

                $this->manifoldRpc->call(
                    ConnectionCommandHandler::class,
                    'closeConnection',
                    [$connectionId]
                );
            });

            $this->manifoldRpc->call(
                ConnectionCommandHandler::class,
                'openConnection',
                [$connectionId]
            )->then(function () use (
                $connectionId,
                &$connectionOpen,
                &$connectionOpenAtManifold,
                &$earlyChunks
            ) {
                if (!$connectionOpen) {
                    return;
                }

                $connectionOpenAtManifold = true;

                foreach ($earlyChunks as $chunk) {
                    $this->manifoldRpc->call(
                        ConnectionCommandHandler::class,
                        'receive',
                        [$connectionId, $chunk]
                    );
                }

                $earlyChunks = []; // Free memory.
            });
        });
    }
}
