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

use Nytris\Turbo\Component\Manifold\Exhaust\Response\ResponseHandlerInterface;
use Nytris\Turbo\Component\Manifold\Intake\Camshaft\Request\RequestTableInterface;
use Nytris\Turbo\Component\Rpc\Handler\HandlerInterface;
use Nytris\Turbo\Component\Rpc\Handler\HandlerTrait;

/**
 * Class ResponseCommandHandler.
 *
 * Note that response body chunks are not processed here, as they are streamed out via stdout
 * (e.g. as Stdout records for the FastCGI protocol), captured by the Launcher.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ResponseCommandHandler implements HandlerInterface
{
    use HandlerTrait;

    public function __construct(
        private readonly ResponseHandlerInterface $responseHandler,
        private readonly RequestTableInterface $requestTable
    ) {
    }

    /**
     * Sends the headers for a response.
     *
     * @param int $connectionId
     * @param int $requestId
     * @param string $httpVersion
     * @param int $statusCode
     * @param string $statusText
     * @param array<string, string[]> $headers
     */
    public function beginResponse(
        int $connectionId,
        int $requestId,
        string $httpVersion,
        int $statusCode,
        string $statusText,
        array $headers
    ): void {
        $this->responseHandler->beginResponse(
            connectionId: $connectionId,
            requestId: $requestId,
            httpVersion: $httpVersion,
            statusCode: $statusCode,
            statusText: $statusText,
            headers: $headers
        );
    }

    /**
     * Handles the end of a response, once its body has been fully sent.
     */
    public function endResponse(int $connectionId, int $requestId): void
    {
        // Free the worker so that it can be used for another request.
        $this->requestTable->freeWorker($connectionId, $requestId);

        // Send the FastCGI end request record back to the client.
        $this->responseHandler->endResponse($connectionId, $requestId);
    }
}
