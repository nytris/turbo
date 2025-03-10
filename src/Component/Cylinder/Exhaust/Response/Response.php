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

namespace Nytris\Turbo\Component\Cylinder\Exhaust\Response;

use Closure;
use Nytris\Turbo\Http\ResponseInterface;

/**
 * Class Response.
 *
 * Represents the response for a HTTP request received over either HTTP or FastCGI.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Response implements ResponseInterface
{
    /**
     * @param array<string, string[]> $headers Associative array of headers,
     *                                       where keys are header names
     *                                       and values are arrays of header values.
     */
    public function __construct(
        private readonly int $statusCode,
        private readonly string $reasonPhrase,
        private readonly array $headers,
        private readonly Closure $bodyCallback
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @inheritDoc
     */
    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @inheritDoc
     */
    public function send(): void
    {
        ($this->bodyCallback)();
    }
}
