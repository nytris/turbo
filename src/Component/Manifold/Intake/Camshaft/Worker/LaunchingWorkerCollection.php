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

use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use React\Socket\ConnectionInterface;

/**
 * Class LaunchingWorkerCollection.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class LaunchingWorkerCollection implements LaunchingWorkerCollectionInterface
{
    /**
     * @var array<int, Deferred<ConnectionInterface>>
     */
    private array $cylinderIndexToDeferred = [];

    /**
     * @inheritDoc
     */
    public function awaitLaunchingWorker(int $cylinderIndex): PromiseInterface
    {
        $deferred = new Deferred();

        $this->cylinderIndexToDeferred[$cylinderIndex] = $deferred;

        return $deferred->promise();
    }

    /**
     * @inheritDoc
     */
    public function finishWorkerLaunch(int $cylinderIndex, ConnectionInterface $rpcConnection): void
    {
        $deferred = $this->cylinderIndexToDeferred[$cylinderIndex];
        unset($this->cylinderIndexToDeferred[$cylinderIndex]);

        $deferred->resolve($rpcConnection);
    }
}
