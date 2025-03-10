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

namespace Nytris\Turbo\Component\Manifold\Intake\Rpc\Handler;

use Nytris\Turbo\Component\Manifold\Intake\Protocol\ProtocolRequestHandlerInterface;
use Nytris\Turbo\Component\Rpc\Handler\HandlerInterface;
use Nytris\Turbo\Component\Rpc\Handler\HandlerTrait;

/**
 * Class ConnectionCommandHandler.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConnectionCommandHandler implements HandlerInterface
{
    use HandlerTrait;

    public function __construct(
        private readonly ProtocolRequestHandlerInterface $protocolRequestHandler
    ) {
    }

    public function closeConnection(int $connectionId): void
    {
        $this->protocolRequestHandler->closeConnection($connectionId);
    }

    public function openConnection(int $connectionId): void
    {
        $this->protocolRequestHandler->openConnection($connectionId);
    }

    public function receive(int $connectionId, string $chunk): void
    {
        $this->protocolRequestHandler->receive($connectionId, $chunk);
    }
}
