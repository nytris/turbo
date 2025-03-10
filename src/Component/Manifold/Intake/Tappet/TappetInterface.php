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

namespace Nytris\Turbo\Component\Manifold\Intake\Tappet;

use React\Promise\PromiseInterface;

/**
 * Interface TappetInterface.
 *
 * Handles RPC between Manifold and the Cylinders.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface TappetInterface
{
    /**
     * Starts the Tappet, which handles RPC between Manifold and the Cylinders.
     *
     * @return PromiseInterface<static>
     */
    public function start(): PromiseInterface;
}
