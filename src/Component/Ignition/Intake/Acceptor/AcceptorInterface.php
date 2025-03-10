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

namespace Nytris\Turbo\Component\Ignition\Intake\Acceptor;

use React\Socket\ServerInterface;

/**
 * Interface AcceptorInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface AcceptorInterface
{
    /**
     * Starts listening on the socket server, for either HTTP or FastCGI connections.
     */
    public function listen(ServerInterface $server): void;
}
