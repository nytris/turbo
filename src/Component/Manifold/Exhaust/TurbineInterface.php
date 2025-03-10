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

namespace Nytris\Turbo\Component\Manifold\Exhaust;

use React\Promise\PromiseInterface;

/**
 * Interface TurbineInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface TurbineInterface
{
    /**
     * Starts the Turbine, sending HTTP or FastCGI responses.
     *
     * @return PromiseInterface<static>
     */
    public function start(): PromiseInterface;
}
