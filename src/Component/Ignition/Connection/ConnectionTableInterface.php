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

namespace Nytris\Turbo\Component\Ignition\Connection;

use React\Socket\ConnectionInterface;

/**
 * Interface ConnectionTableInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ConnectionTableInterface
{
    /**
     * Adds a connection to the table.
     */
    public function addConnection(int $connectionId, ConnectionInterface $connection): void;

    /**
     * Fetches a connection from the table.
     */
    public function getConnection(int $connectionId): ConnectionInterface;

    /**
     * Removes a connection from the table.
     */
    public function removeConnection(int $connectionId): void;
}
