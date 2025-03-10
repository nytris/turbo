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

use React\Promise\PromiseInterface;
use React\Socket\ConnectionInterface;

/**
 * Interface LaunchingWorkerCollectionInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface LaunchingWorkerCollectionInterface
{
    /**
     * Adds a cylinder worker to the set that are currently launching.
     *
     * Returns a promise to be resolved when the worker reports back that it is ready via RPC.
     *
     * @return PromiseInterface<ConnectionInterface>
     */
    public function awaitLaunchingWorker(int $cylinderIndex): PromiseInterface;

    /**
     * Notifies the collection that a worker has finished launching.
     */
    public function finishWorkerLaunch(int $cylinderIndex, ConnectionInterface $rpcConnection): void;
}
