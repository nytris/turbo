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

use Closure;
use Nytris\Turbo\Component\Cylinder\CylinderInterface;
use Nytris\Turbo\Component\Ignition\IgnitionInterface;
use Nytris\Turbo\Component\Manifold\ManifoldInterface;

/**
 * Class Library.
 *
 * Encapsulates an installation of the library.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Library implements LibraryInterface
{
    /**
     * @param Closure(): IgnitionInterface $ignitionComponentFactory
     * @param Closure(): ManifoldInterface $manifoldComponentFactory
     * @param Closure(): CylinderInterface $cylinderComponentFactory
     * @param int $cylinderCount
     */
    public function __construct(
        private readonly Closure $ignitionComponentFactory,
        private readonly Closure $manifoldComponentFactory,
        private readonly Closure $cylinderComponentFactory,
        private readonly int $cylinderCount
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getCylinder(): CylinderInterface
    {
        return ($this->cylinderComponentFactory)();
    }

    /**
     * @inheritDoc
     */
    public function getCylinderCount(): int
    {
        return $this->cylinderCount;
    }

    /**
     * @inheritDoc
     */
    public function getIgnition(): IgnitionInterface
    {
        return ($this->ignitionComponentFactory)();
    }

    /**
     * @inheritDoc
     */
    public function getManifold(): ManifoldInterface
    {
        return ($this->manifoldComponentFactory)();
    }

    /**
     * @inheritDoc
     */
    public function uninstall(): void
    {
    }
}
