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

use Nytris\Turbo\Component\Manifold\Exhaust\Protocol\ProtocolResponseHandlerInterface;

/**
 * Class ResponseHandler.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ResponseHandler implements ResponseHandlerInterface
{
    public function __construct(
        private readonly ProtocolResponseHandlerInterface $protocolResponseHandler
    ) {
    }

    /**
     * @inheritDoc
     */
    public function appendResponseBodyChunk(int $connectionId, int $requestId, string $chunk): void
    {
        $this->protocolResponseHandler->appendResponseBodyChunk($connectionId, $requestId, $chunk);
    }

    /**
     * @inheritDoc
     */
    public function beginResponse(
        int $connectionId,
        int $requestId,
        string $httpVersion,
        int $statusCode,
        string $statusText,
        array $headers
    ): void {
        $headersString = 'Status: ' . $statusCode . ' ' . $statusText . "\r\n";

        foreach ($headers as $name => $values) {
            foreach ($values as $value) {
                $headersString .= $name . ': ' . $value . "\r\n";
            }
        }

        $headersString .= "\r\n";

        $this->protocolResponseHandler->appendResponseBodyChunk($connectionId, $requestId, $headersString);
    }

    /**
     * @inheritDoc
     */
    public function endResponse(int $connectionId, int $requestId): void
    {
        $this->protocolResponseHandler->endRequest($connectionId, $requestId);
    }
}
