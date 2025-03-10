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

namespace Nytris\Turbo\Component\Cylinder\Valve;

use Nytris\Turbo\Component\Rpc\Transport\Stream\StreamContextInterface;
use Nytris\Turbo\Component\Rpc\Transport\TransportInterface;
use React\Promise\PromiseInterface;
use React\Socket\ConnectionInterface;
use React\Socket\ConnectorInterface;

/**
 * Class Valve.
 *
 * Connects back to Tappet in Manifold to provide a separate channel for RPC.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Valve implements ValveInterface
{
    public function __construct(
        private readonly StreamContextInterface $streamContext,
        private readonly TransportInterface $transport,
        private readonly ConnectorInterface $connector,
        private readonly string $connectUri
    ) {
    }

    /**
     * @inheritDoc
     */
    public function start(): PromiseInterface
    {
        return $this->connector->connect($this->connectUri)
            ->then(function (ConnectionInterface $connection) {
                $this->streamContext->useStreams($connection, $connection);

                $this->transport->listen();
                $this->transport->resume();

                return null;
            });
    }
}
