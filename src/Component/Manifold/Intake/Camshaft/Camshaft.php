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

namespace Nytris\Turbo\Component\Manifold\Intake\Camshaft;

use LogicException;
use Nytris\Turbo\Component\Manifold\Exhaust\Response\ResponseHandlerInterface;
use Nytris\Turbo\Component\Manifold\Intake\Camshaft\Launcher\WorkerLauncherInterface;
use Nytris\Turbo\Component\Manifold\Intake\Camshaft\Request\RequestTableInterface;
use Nytris\Turbo\Component\Manifold\Intake\Camshaft\Worker\LaunchingWorkerCollectionInterface;
use Nytris\Turbo\Component\Manifold\Intake\Camshaft\Worker\WorkerFactoryInterface;
use Nytris\Turbo\Component\Manifold\Intake\Camshaft\Worker\WorkerInterface;
use Nytris\Turbo\Http\RequestInterface;
use React\Promise\PromiseInterface;
use function React\Promise\all;

/**
 * Class Camshaft.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Camshaft implements CamshaftInterface
{
    public function __construct(
        private readonly RequestTableInterface $requestTable,
        private readonly WorkerLauncherInterface $workerLauncher,
        private readonly LaunchingWorkerCollectionInterface $launchingWorkerCollection,
        private readonly WorkerFactoryInterface $workerFactory,
        private readonly ResponseHandlerInterface $responseHandler,
        private readonly int $cylinderCount
    ) {
    }

    /**
     * @inheritDoc
     */
    public function appendRequestBodyChunk(int $connectionId, int $requestId, string $chunk): void
    {
        $worker = $this->requestTable->getWorker($connectionId, $requestId);

        $worker->appendRequestBodyChunk($chunk);
    }

    /**
     * @inheritDoc
     */
    public function beginRequest(RequestInterface $request): void
    {
        $worker = $this->requestTable->getFreeWorker($request->getConnectionId(), $request->getRequestId());

        if ($worker === null) {
            // TODO: Return appropriate error response.
            throw new LogicException('No free workers available');
        }

        $worker->beginRequest($request);
    }

    /**
     * @inheritDoc
     */
    public function handleRequest(int $connectionId, int $requestId): void
    {
        $worker = $this->requestTable->getWorker($connectionId, $requestId);

        $worker->handleRequest();
    }

    /**
     * @inheritDoc
     */
    public function start(?string $configPath): PromiseInterface
    {
        /** @var PromiseInterface<WorkerInterface>[] $promises */
        $promises = [];

        // Start the specified number of FastCGI workers (e.g. long-running PHP-FPM requests).
        for ($cylinderIndex = 0; $cylinderIndex < $this->cylinderCount; $cylinderIndex++) {
            $promises[$cylinderIndex] = $this->workerLauncher->launchWorker(
                $cylinderIndex,
                $this->launchingWorkerCollection,
                $this->workerFactory,
                $this->responseHandler,
                $configPath
            );
        }

        return all($promises)
            ->then(function (array $workers) {
                // All workers have connected now.
                $this->requestTable->loadWorkers($workers);

                return $workers;
            });
    }
}
