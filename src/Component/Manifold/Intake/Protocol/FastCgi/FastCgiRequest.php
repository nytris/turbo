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

namespace Nytris\Turbo\Component\Manifold\Intake\Protocol\FastCgi;

use Nytris\Turbo\Http\RequestInterface;

/**
 * Class FastCgiRequest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FastCgiRequest implements RequestInterface
{
    private int $flags;
    /**
     * @var string[];
     */
    private array $params = [];
    private int $requestId;
    private int $role;

    public function __construct(
        private readonly int $connectionId
    ) {
    }

    /**
     * Handles a Params frame, adding FastCGI params to the request.
     */
    public function addParams(array $params): void
    {
        $this->params = array_merge($this->params, $params);
    }

    /**
     * Handles the BeginRequest frame.
     */
    public function begin(int $id, int $role, int $flags): void
    {
        $this->flags = $flags;
        $this->requestId = $id;
        $this->role = $role;
    }

    /**
     * @inheritDoc
     */
    public function getConnectionId(): int
    {
        return $this->connectionId;
    }

    /**
     * @inheritDoc
     */
    public function getGlobalId(): string
    {
        return $this->connectionId . ':' . $this->requestId;
    }

    /**
     * @inheritDoc
     */
    public function getRequestId(): int
    {
        return $this->requestId;
    }
}
