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

namespace Nytris\Turbo\Component\Manifold\Exhaust\Protocol;

/**
 * Interface ProtocolResponseHandlerInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ProtocolResponseHandlerInterface
{
    /**
     * Appends a chunk of data to be sent as part of the response's body stream.
     */
    public function appendResponseBodyChunk(int $connectionId, int $requestId, string $chunk): void;

    /**
     * Handles the end of a response, once its body has been fully sent.
     */
    public function endRequest(int $connectionId, int $requestId): void;
}
