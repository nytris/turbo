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

use Nytris\Turbo\Component\Cylinder\Rpc\Handler\RequestCommandHandler;
use Nytris\Turbo\Component\Manifold\Intake\Camshaft\Request\RequestContextInterface;
use Nytris\Turbo\Component\Rpc\RpcInterface;
use Nytris\Turbo\Component\Rpc\Transport\TransportInterface;
use Nytris\Turbo\Http\RequestInterface;
use React\Promise\PromiseInterface;
use function React\Promise\resolve;

/**
 * Class Worker.
 *
 * Handles requests over RPC to a Cylinder instance.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Worker implements WorkerInterface
{
    public function __construct(
        private readonly RequestContextInterface $requestContext,
        private readonly TransportInterface $transport,
        private readonly RpcInterface $rpc,
        private readonly int $cylinderIndex
    ) {
    }

    /**
     * @inheritDoc
     */
    public function appendRequestBodyChunk(string $chunk): void
    {
        $this->rpc->call(
            handlerFqcn: RequestCommandHandler::class,
            method: 'appendRequestBodyChunk',
            args: [$chunk]
        );
    }

    /**
     * @inheritDoc
     */
    public function beginRequest(RequestInterface $request): void
    {
        $this->requestContext->beginRequest($request);

        $this->rpc->call(
            handlerFqcn: RequestCommandHandler::class,
            method: 'beginRequest',
            args: [$request]
        );
    }

    /**
     * @inheritDoc
     */
    public function getCylinderIndex(): int
    {
        return $this->cylinderIndex;
    }

    /**
     * @inheritDoc
     */
    public function handleRequest(): void
    {
        $this->rpc->call(
            handlerFqcn: RequestCommandHandler::class,
            method: 'handleRequest'
        );
    }

    /**
     * @inheritDoc
     */
    public function start(): PromiseInterface
    {
        $this->transport->listen();
        $this->transport->resume();

        return resolve($this);
    }
}
