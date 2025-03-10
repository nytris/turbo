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

namespace Nytris\Turbo\Component\Ignition\Exhaust\ManifoldSubProcess;

/**
 * Interface ManifoldCoordinatorInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ManifoldCoordinatorInterface
{
    /**
     * Starts the Manifold component's process.
     */
    public function launch(?string $configPath): void;
}
