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

namespace Nytris\Turbo\Component\Cylinder;

use React\Promise\PromiseInterface;

/**
 * Interface CylinderInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface CylinderInterface
{
    /**
     * Starts the Cylinder component, listening for requests forwarded by Manifold to handle.
     *
     * @return PromiseInterface<null>
     */
    public function start(int $cylinderIndex): PromiseInterface;
}
