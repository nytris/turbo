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

namespace Nytris\Turbo\Library;

use Nytris\Turbo\Component\Cylinder\CylinderInterface;
use Nytris\Turbo\Component\Ignition\IgnitionInterface;
use Nytris\Turbo\Component\Manifold\ManifoldInterface;

/**
 * Interface LibraryInterface.
 *
 * Encapsulates an installation of the library.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface LibraryInterface
{
    /**
     * Fetches the Cylinder component.
     */
    public function getCylinder(): CylinderInterface;

    /**
     * Fetches the number of cylinders to allocate,
     * which is the number of php-fpm worker processes that will be reserved,
     * and therefore defines the maximum number of requests that may be handled in parallel.
     */
    public function getCylinderCount(): int;

    /**
     * Fetches the Ignition component.
     */
    public function getIgnition(): IgnitionInterface;

    /**
     * Fetches the Manifold component.
     */
    public function getManifold(): ManifoldInterface;

    /**
     * Uninstalls this installation of the library.
     */
    public function uninstall(): void;
}
