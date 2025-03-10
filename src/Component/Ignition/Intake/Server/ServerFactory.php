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

namespace Nytris\Turbo\Component\Ignition\Intake\Server;

use React\Socket\ServerInterface;
use React\Socket\SocketServer;

/**
 * Class ServerFactory.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ServerFactory implements ServerFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createServer(string $uri): ServerInterface
    {
        return new SocketServer($uri);
    }
}
