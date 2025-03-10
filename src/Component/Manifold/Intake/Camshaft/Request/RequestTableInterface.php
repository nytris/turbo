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

namespace Nytris\Turbo\Component\Manifold\Intake\Camshaft\Request;

use Nytris\Turbo\Component\Manifold\Intake\Camshaft\Worker\WorkerInterface;

/**
 * Interface RequestTableInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface RequestTableInterface
{
    /**
     * Frees the assigned worker for the given connection and request ID.
     */
    public function freeWorker(int $connectionId, int $requestId): void;

    /**
     * Gets a free worker for the given connection and request ID, if one is available.
     */
    public function getFreeWorker(int $connectionId, int $requestId): ?WorkerInterface;

    /**
     * Gets the assigned worker for the given connection and request ID.
     */
    public function getWorker(int $connectionId, int $requestId): WorkerInterface;

    /**
     * Fetches the set of all workers.
     *
     * @return WorkerInterface[]
     */
    public function getWorkers(): array;

    /**
     * Loads the set of available workers.
     *
     * @param WorkerInterface[] $workers
     */
    public function loadWorkers(array $workers): void;
}
