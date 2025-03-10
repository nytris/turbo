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

use Evenement\EventEmitter;
use React\Socket\ServerInterface;

/**
 * Class SocketServer.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SocketServer extends EventEmitter implements ServerInterface
{
    public function __construct(
        private readonly string $address
    ) {
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        // TODO: Implement close() method.
    }

    /**
     * @inheritDoc
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @inheritDoc
     */
    public function pause(): void
    {
        // TODO: Implement pause() method.
    }

    /**
     * @inheritDoc
     */
    public function resume(): void
    {
        // TODO: Implement resume() method.
    }
}
