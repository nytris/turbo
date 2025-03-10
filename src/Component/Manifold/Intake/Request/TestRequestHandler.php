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

use Nytris\Turbo\Component\Manifold\Exhaust\Response\ResponseHandlerInterface;
use Nytris\Turbo\Http\RequestInterface;

/**
 * Class TestRequestHandler.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TestRequestHandler implements RequestHandlerInterface
{
    /**
     * @var array<string, string>
     */
    private array $bodyByRequestId = [];
    /**
     * @var array<string, RequestInterface>
     */
    private array $requestsById = [];

    public function __construct(
        private readonly ResponseHandlerInterface $responseHandler
    ) {
    }

    /**
     * @inheritDoc
     */
    public function appendRequestBodyChunk(int $connectionId, int $requestId, string $chunk): void
    {
        $this->bodyByRequestId[$connectionId . ':' . $requestId] .= $chunk;
    }

    /**
     * @inheritDoc
     */
    public function beginRequest(RequestInterface $request): void
    {
        $id = $request->getConnectionId() . ':' . $request->getGlobalId();

        $this->requestsById[$id] = $request;
        $this->bodyByRequestId[$id] = '';
    }

    /**
     * @inheritDoc
     */
    public function handleRequest(int $connectionId, int $requestId): void
    {
        $request = $this->requestsById[$connectionId . ':' . $requestId];

        $responseBody = 'Hello world from Nytris Turbo!';
        $bodySize = strlen($responseBody);

        $this->responseHandler->beginResponse(
            connectionId: $connectionId,
            requestId: $requestId,
            httpVersion: '1.1',
            statusCode: 200,
            statusText: 'OK',
            headers: [
                'Content-Type' => ['text/plain'],
                'Content-Length' => [$bodySize],
            ]
        );
        $this->responseHandler->appendResponseBodyChunk(
            $connectionId,
            $requestId,
            $responseBody
        );
        $this->responseHandler->endResponse($connectionId, $requestId);
    }
}
