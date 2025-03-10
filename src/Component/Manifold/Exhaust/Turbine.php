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
use function React\Promise\resolve;

/**
 * Class Turbine.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Turbine implements TurbineInterface
{
    /**
     * @inheritDoc
     */
    public function start(): PromiseInterface
    {
        return resolve($this);
    }
}
