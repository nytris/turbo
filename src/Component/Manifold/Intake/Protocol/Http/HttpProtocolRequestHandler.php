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

namespace Nytris\Turbo\Component\Manifold\Intake\Protocol\Http;

use Nytris\Turbo\Component\Manifold\Intake\Protocol\ProtocolRequestHandlerInterface;
use React\Socket\ConnectionInterface;

/**
 * Class HttpProtocolRequestHandler.
 *
 * TODO: May need to implement this, as React HTTP is only designed to work over an actual socket stream.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class HttpProtocolRequestHandler implements ProtocolRequestHandlerInterface
{
    /**
     * @var array<int, ConnectionInterface>
     */
    private array $connections = [];

    /**
     * @inheritDoc
     */
    public function closeConnection(int $connectionId): void
    {

    }

    /**
     * @inheritDoc
     */
    public function openConnection(int $connectionId): void
    {

    }

    /**
     * @inheritDoc
     */
    public function receive(int $connectionId, string $chunk): void
    {

    }
}
