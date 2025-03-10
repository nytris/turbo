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

namespace Nytris\Turbo\Component\Ignition;

/**
 * Interface IgnitionInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface IgnitionInterface
{
    /**
     * Starts the Ignition component, listening for HTTP or FastCGI requests.
     *
     * If the optional `$listenUri` is provided, it will override the listen URI
     * that was set in `nytris.config.php`.
     */
    public function start(?string $listenUri, ?string $configPath): void;
}
