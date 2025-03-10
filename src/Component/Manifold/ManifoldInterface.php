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

namespace Nytris\Turbo\Component\Manifold;

/**
 * Interface ManifoldInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ManifoldInterface
{
    /**
     * Starts the Manifold component, handling HTTP or FastCGI requests
     * by handing them off to a downstream processor such as PHP-FPM.
     */
    public function start(?string $configPath): void;
}
