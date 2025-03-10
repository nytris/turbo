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

namespace Nytris\Turbo\Nytris;

/**
 * Interface BooterInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface BooterInterface
{
    /**
     * Boot Nytris platform from the given custom `nytris.config.php` path.
     */
    public function boot(string $configPath): void;
}
