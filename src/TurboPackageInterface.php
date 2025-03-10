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

use Closure;
use Nytris\Core\Package\PackageInterface;
use Nytris\Turbo\Component\Cylinder\CylinderInterface;
use Nytris\Turbo\Component\Ignition\IgnitionInterface;
use Nytris\Turbo\Component\Manifold\ManifoldInterface;

/**
 * Interface TurboPackageInterface.
 *
 * Configures the installation of Nytris Turbo.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface TurboPackageInterface extends PackageInterface
{
    public const DEFAULT_CYLINDER_COUNT = 2;

    /**
     * Fetches the factory to use to create the Cylinder component.
     *
     * Allows the consuming application to swap out the entire Cylinder implementation.
     *
     * @return Closure(): CylinderInterface
     */
    public function getCylinderComponentFactory(): Closure;

    /**
     * Fetches the number of cylinders to allocate,
     * which is the number of php-fpm worker processes that will be reserved,
     * and therefore defines the maximum number of requests that may be handled in parallel.
     */
    public function getCylinderCount(): int;

    /**
     * Fetches the `react/socket` URI that Cylinder should connect to Tappet for RPC via.
     */
    public function getCylinderValveConnectUri(): string;

    /**
     * Fetches the factory to use to create the Ignition component.
     *
     * Allows the consuming application to swap out the entire Ignition implementation.
     *
     * @return Closure(): IgnitionInterface
     */
    public function getIgnitionComponentFactory(): Closure;

    /**
     * Fetches the `react/socket` URI to listen on.
     */
    public function getListenUri(): string;

    /**
     * Fetches the factory to use to create the Manifold component.
     *
     * Allows the consuming application to swap out the entire Manifold implementation.
     *
     * @return Closure(): ManifoldInterface
     */
    public function getManifoldComponentFactory(): Closure;

    /**
     * Fetches the `react/socket` URI that Tappet should listen for RPC connections from Cylinder on.
     */
    public function getTappetListenUri(): string;
}
