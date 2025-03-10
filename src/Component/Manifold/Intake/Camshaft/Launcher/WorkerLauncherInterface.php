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

namespace Nytris\Turbo\Component\Manifold\Intake\Camshaft\Launcher;

use Nytris\Turbo\Component\Manifold\Exhaust\Response\ResponseHandlerInterface;
use Nytris\Turbo\Component\Manifold\Intake\Camshaft\Worker\LaunchingWorkerCollectionInterface;
use Nytris\Turbo\Component\Manifold\Intake\Camshaft\Worker\WorkerFactoryInterface;
use Nytris\Turbo\Component\Manifold\Intake\Camshaft\Worker\WorkerInterface;
use React\Promise\PromiseInterface;

/**
 * Interface WorkerLauncherInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WorkerLauncherInterface
{
    /**
     * Starts the Cylinder worker.
     *
     * @return PromiseInterface<WorkerInterface>
     */
    public function launchWorker(
        int $cylinderIndex,
        LaunchingWorkerCollectionInterface $launchingWorkerCollection,
        WorkerFactoryInterface $workerFactory,
        ResponseHandlerInterface $responseHandler,
        ?string $configPath
    ): PromiseInterface;
}
