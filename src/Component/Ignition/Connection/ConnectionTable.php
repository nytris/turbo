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
 * Class ConnectionTable.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConnectionTable implements ConnectionTableInterface
{
    /**
     * @var array<int, ConnectionInterface>
     */
    private array $connections = [];

    /**
     * @inheritDoc
     */
    public function addConnection(int $connectionId, ConnectionInterface $connection): void
    {
        $this->connections[$connectionId] = $connection;
    }

    /**
     * @inheritDoc
     */
    public function getConnection(int $connectionId): ConnectionInterface
    {
        return $this->connections[$connectionId];
    }

    /**
     * @inheritDoc
     */
    public function removeConnection(int $connectionId): void
    {
        unset($this->connections[$connectionId]);
    }
}
