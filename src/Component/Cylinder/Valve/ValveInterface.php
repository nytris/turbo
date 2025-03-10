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

namespace Nytris\Turbo\Component\Cylinder\Valve;

use React\Promise\PromiseInterface;

/**
 * Interface ValveInterface.
 *
 * Connects back to Tappet in Manifold to provide a separate channel for RPC.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ValveInterface
{
    /**
     * Starts the Valve component.
     *
     * @return PromiseInterface<null>
     */
    public function start(): PromiseInterface;
}
