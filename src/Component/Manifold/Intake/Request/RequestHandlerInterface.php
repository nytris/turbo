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

namespace Nytris\Turbo\Component\Manifold\Intake\Request;

use Nytris\Turbo\Http\RequestInterface;

/**
 * Interface RequestHandlerInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface RequestHandlerInterface
{
    /**
     * Appends a received chunk of data to the request's body stream.
     */
    public function appendRequestBodyChunk(int $connectionId, int $requestId, string $chunk): void;

    /**
     * Handles the beginning of a new request, once its headers have been fully received.
     */
    public function beginRequest(RequestInterface $request): void;

    /**
     * Starts the processing of a request, once its request body has been fully received.
     */
    public function handleRequest(int $connectionId, int $requestId): void;
}
