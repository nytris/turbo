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

namespace Nytris\Turbo\Component\Manifold\Exhaust\Response;

/**
 * Interface ResponseHandlerInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ResponseHandlerInterface
{
    /**
     * Appends a chunk of data to be sent as part of the response's body stream.
     */
    public function appendResponseBodyChunk(int $connectionId, int $requestId, string $chunk): void;

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
    ): void;

    /**
     * Handles the end of a response, once its body has been fully sent.
     */
    public function endResponse(int $connectionId, int $requestId): void;
}
