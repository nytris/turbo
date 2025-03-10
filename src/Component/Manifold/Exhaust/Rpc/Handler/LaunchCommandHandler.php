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

namespace Nytris\Turbo\Component\Manifold\Exhaust\Rpc\Handler;

use Nytris\Turbo\Component\Manifold\Intake\Camshaft\Worker\LaunchingWorkerCollectionInterface;
use Nytris\Turbo\Component\Rpc\Handler\HandlerInterface;
use Nytris\Turbo\Component\Rpc\Handler\HandlerTrait;
use Nytris\Turbo\Component\Rpc\Transport\TransportInterface;
use React\Socket\ConnectionInterface;

/**
 * Class LaunchCommandHandler.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class LaunchCommandHandler implements HandlerInterface
{
    use HandlerTrait;

    public function __construct(
        private readonly LaunchingWorkerCollectionInterface $launchingWorkerCollection,
        private readonly TransportInterface $transport,
        private readonly ConnectionInterface $rpcConnection
    ) {
    }

    /**
     * Links the RPC connection for the worker that has launched to its cylinder index.
     */
    public function ready(int $cylinderIndex): void
    {
        // No more launch-related commands are expected,
        // so for efficiency we can stop this special launch transport now.
        $this->transport->stop();

        $this->launchingWorkerCollection->finishWorkerLaunch($cylinderIndex, $this->rpcConnection);
    }
}
