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

namespace Nytris\Turbo\Component\Cylinder\Rpc\Handler;

use Nytris\Turbo\Component\Cylinder\Piston\PistonInterface;
use Nytris\Turbo\Component\Manifold\Exhaust\Rpc\Handler\ResponseCommandHandler;
use Nytris\Turbo\Component\Rpc\Handler\HandlerInterface;
use Nytris\Turbo\Component\Rpc\Handler\HandlerTrait;
use Nytris\Turbo\Component\Rpc\RpcInterface;
use Nytris\Turbo\Http\RequestInterface;
use Nytris\Turbo\Http\ResponseInterface;

/**
 * Class RequestCommandHandler.
 *
 * Handles requests received over RPC to a Cylinder instance.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RequestCommandHandler implements HandlerInterface
{
    use HandlerTrait;

    private ?RequestInterface $currentRequest = null;
    private string $currentRequestBody = '';

    public function __construct(
        private readonly RpcInterface $manifoldRpc,
        private readonly PistonInterface $piston
    ) {
    }

    /**
     * Appends a received chunk of data to this worker's current request's body stream.
     */
    public function appendRequestBodyChunk(string $chunk): void
    {
        $this->currentRequestBody .= $chunk;
    }

    /**
     * Handles the beginning of a new request for this worker, once its headers have been fully received.
     */
    public function beginRequest(RequestInterface $request): void
    {
        $this->currentRequest = $request;
    }

    /**
     * Starts the processing of this worker's current request, once its request body has been fully received.
     */
    public function handleRequest(): void
    {
        $requestBody = $this->currentRequestBody;
        $this->currentRequestBody = '';

        // TODO: Extract from SERVER_PROTOCOL FastCGI param of request
        //       via a generic RequestInterface->getHttpVersion() method.
        $httpVersion = '1.1';

        $this->piston->handleRequest($this->currentRequest)
            ->then(function (ResponseInterface $response) use ($httpVersion) {
                // Send headers and status etc. in a separate RPC call.
                // Must be separate to support streaming responses.
                $this->manifoldRpc->call(
                    handlerFqcn: ResponseCommandHandler::class,
                    method: 'beginResponse',
                    args: [
                        $this->currentRequest->getConnectionId(),
                        $this->currentRequest->getRequestId(),
                        $httpVersion,
                        $response->getStatusCode(),
                        $response->getReasonPhrase(),
                        $response->getHeaders(),
                    ]
                )->then(function () use ($response) {
                    // Write the response to stdout, which will be captured by Manifold.
                    $response->send();

                    $this->manifoldRpc->call(
                        handlerFqcn: ResponseCommandHandler::class,
                        method: 'endResponse',
                        args: [$this->currentRequest->getConnectionId(), $this->currentRequest->getRequestId()]
                    );
                });
            });
    }
}
