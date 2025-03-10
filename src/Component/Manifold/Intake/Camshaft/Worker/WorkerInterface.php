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

use Nytris\Turbo\Http\RequestInterface;
use React\Promise\PromiseInterface;

/**
 * Interface WorkerInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WorkerInterface
{
    /**
     * Appends a received chunk of data to this worker's current request's body stream.
     */
    public function appendRequestBodyChunk(string $chunk): void;

    /**
     * Handles the beginning of a new request for this worker, once its headers have been fully received.
     */
    public function beginRequest(RequestInterface $request): void;

    /**
     * Fetches the unique index of the cylinder that this worker is associated with.
     */
    public function getCylinderIndex(): int;

    /**
     * Starts the processing of this worker's current request, once its request body has been fully received.
     */
    public function handleRequest(): void;

    /**
     * Starts the worker.
     *
     * @return PromiseInterface<static>
     */
    public function start(): PromiseInterface;
}
