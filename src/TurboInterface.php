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

namespace Nytris\Turbo;

use Nytris\Core\Package\PackageFacadeInterface;
use Nytris\Turbo\Component\Cylinder\CylinderInterface;
use Nytris\Turbo\Component\Ignition\IgnitionInterface;
use Nytris\Turbo\Component\Manifold\ManifoldInterface;
use Nytris\Turbo\Library\LibraryInterface;

/**
 * Interface TurboInterface.
 *
 * Defines the public facade API for the library.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface TurboInterface extends PackageFacadeInterface
{
    /**
     * Fetches the Cylinder component.
     */
    public static function getCylinder(): CylinderInterface;

    /**
     * Fetches the number of cylinders to allocate,
     * which is the number of php-fpm worker processes that will be reserved,
     * and therefore defines the maximum number of requests that may be handled in parallel.
     */
    public static function getCylinderCount(): int;

    /**
     * Fetches the Ignition component.
     */
    public static function getIgnition(): IgnitionInterface;

    /**
     * Fetches the current library installation.
     */
    public static function getLibrary(): LibraryInterface;

    /**
     * Fetches the Manifold component.
     */
    public static function getManifold(): ManifoldInterface;

    /**
     * Overrides the current library installation with the given one.
     */
    public static function setLibrary(LibraryInterface $library): void;
}
