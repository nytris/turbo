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

use LogicException;
use Nytris\Turbo\Component\Manifold\Intake\Camshaft\Worker\WorkerInterface;

/**
 * Class RequestTable.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RequestTable implements RequestTableInterface
{
    /**
     * @var WorkerInterface[]
     */
    private array $freeWorkers = [];
    /**
     * @var WorkerInterface[]
     */
    private array $workers = [];
    /**
     * @var array<string, WorkerInterface>
     */
    private array $workersByRequestId = [];

    /**
     * @inheritDoc
     */
    public function freeWorker(int $connectionId, int $requestId): void
    {
        $id = $connectionId . ':' . $requestId;

        $worker = $this->workersByRequestId[$id] ?? null;

        if ($worker === null) {
            throw new LogicException("No worker found for request ID {$id}");
        }

        // Return the worker to the pool of free workers.
        unset($this->workersByRequestId[$id]);
        $this->freeWorkers[] = $worker;
    }

    /**
     * @inheritDoc
     */
    public function getFreeWorker(int $connectionId, int $requestId): ?WorkerInterface
    {
        $id = $connectionId . ':' . $requestId;

        if (empty($this->freeWorkers)) {
            return null; // No free workers available.
        }

        $worker = array_shift($this->freeWorkers);
        $this->workersByRequestId[$id] = $worker;

        return $worker;
    }

    /**
     * @inheritDoc
     */
    public function getWorker(int $connectionId, int $requestId): WorkerInterface
    {
        $id = $connectionId . ':' . $requestId;
        $worker = $this->workersByRequestId[$id] ?? null;

        if ($worker === null) {
            throw new LogicException("No worker found for request ID {$id}");
        }

        return $worker;
    }

    /**
     * @inheritDoc
     */
    public function getWorkers(): array
    {
        return $this->workers;
    }

    /**
     * @inheritDoc
     */
    public function loadWorkers(array $workers): void
    {
        $this->workers = $workers;
        $this->freeWorkers = $workers;
    }
}
