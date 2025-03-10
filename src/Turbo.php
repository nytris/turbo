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

use InvalidArgumentException;
use LogicException;
use Nytris\Core\Package\PackageContextInterface;
use Nytris\Core\Package\PackageInterface;
use Nytris\Turbo\Component\Cylinder\CylinderInterface;
use Nytris\Turbo\Component\Ignition\IgnitionInterface;
use Nytris\Turbo\Component\Manifold\ManifoldInterface;
use Nytris\Turbo\Library\Library;
use Nytris\Turbo\Library\LibraryInterface;

/**
 * Class Turbo.
 *
 * Defines the public facade API for the library.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Turbo implements TurboInterface
{
    private static ?LibraryInterface $library = null;

    /**
     * @inheritDoc
     */
    public static function getCylinder(): CylinderInterface
    {
        return self::getLibrary()->getCylinder();
    }

    /**
     * @inheritDoc
     */
    public static function getCylinderCount(): int
    {
        return self::getLibrary()->getCylinderCount();
    }

    /**
     * @inheritDoc
     */
    public static function getIgnition(): IgnitionInterface
    {
        return self::getLibrary()->getIgnition();
    }

    /**
     * @inheritDoc
     */
    public static function getLibrary(): LibraryInterface
    {
        if (!self::$library) {
            throw new LogicException(
                'Library is not installed - did you forget to install this package in nytris.config.php?'
            );
        }

        return self::$library;
    }

    /**
     * @inheritDoc
     */
    public static function getManifold(): ManifoldInterface
    {
        return self::getLibrary()->getManifold();
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'turbo';
    }

    /**
     * @inheritDoc
     */
    public static function getVendor(): string
    {
        return 'nytris';
    }

    /**
     * @inheritDoc
     */
    public static function install(PackageContextInterface $packageContext, PackageInterface $package): void
    {
        if (!$package instanceof TurboPackageInterface) {
            throw new InvalidArgumentException(
                sprintf(
                    'Package config must be a %s but it was a %s',
                    TurboPackageInterface::class,
                    $package::class
                )
            );
        }

        self::$library = new Library(
            $package->getIgnitionComponentFactory(),
            $package->getManifoldComponentFactory(),
            $package->getCylinderComponentFactory(),
            $package->getCylinderCount()
        );
    }

    /**
     * @inheritDoc
     */
    public static function isInstalled(): bool
    {
        return self::$library !== null;
    }

    /**
     * @inheritDoc
     */
    public static function setLibrary(LibraryInterface $library): void
    {
        if (self::$library !== null) {
            self::$library->uninstall();
        }

        self::$library = $library;
    }

    /**
     * @inheritDoc
     */
    public static function uninstall(): void
    {
        if (self::$library === null) {
            // Not yet installed anyway; nothing to do.
            return;
        }

        self::$library->uninstall();
        self::$library = null;
    }
}
