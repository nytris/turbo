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

use Nytris\Turbo\Component\Manifold\Intake\Camshaft\CamshaftInterface;
use Nytris\Turbo\Http\RequestInterface;

/**
 * Class CylinderRequestHandler.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CylinderRequestHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly CamshaftInterface $camshaft
    ) {
    }

    /**
     * @inheritDoc
     */
    public function appendRequestBodyChunk(int $connectionId, int $requestId, string $chunk): void
    {
        $this->camshaft->appendRequestBodyChunk($connectionId, $requestId, $chunk);
    }

    /**
     * @inheritDoc
     */
    public function beginRequest(RequestInterface $request): void
    {
        $this->camshaft->beginRequest($request);
    }

    /**
     * @inheritDoc
     */
    public function handleRequest(int $connectionId, int $requestId): void
    {
        $this->camshaft->handleRequest($connectionId, $requestId);
    }
}
