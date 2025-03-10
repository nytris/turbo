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

namespace Nytris\Turbo\Component\Manifold\Intake\Protocol;

/**
 * Interface ProtocolRequestHandlerInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ProtocolRequestHandlerInterface
{
    /**
     * Handles a connection being closed, which marks the end of the request.
     */
    public function closeConnection(int $connectionId): void;

    /**
     * Handles a connection being opened, which marks the start of a new request.
     */
    public function openConnection(int $connectionId): void;

    /**
     * Handles a chunk of data for an open connection's request being received from the client.
     */
    public function receive(int $connectionId, string $chunk): void;
}
