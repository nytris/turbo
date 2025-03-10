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

namespace Nytris\Turbo\Http;

/**
 * Interface ResponseInterface.
 *
 * Represents the response for a HTTP request received over either HTTP or FastCGI.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ResponseInterface
{
    /**
     * Fetches the HTTP headers for the response.
     *
     * @return array<string, string[]> Associative array of headers,
     *                                 where keys are header names
     *                                 and values are arrays of header values.
     */
    public function getHeaders(): array;

    /**
     * Fetches the HTTP reason phrase (status text) for the response.
     */
    public function getReasonPhrase(): string;

    /**
     * Fetches the HTTP status code for the response.
     */
    public function getStatusCode(): int;

    /**
     * Sends the response from Cylinder to Manifold by simply writing to stdout.
     */
    public function send(): void;
}
