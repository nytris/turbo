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

use LogicException;
use Nytris\Boot\BootConfigInterface;
use Nytris\Core\Includer\IncluderInterface;
use Nytris\Nytris;

/**
 * Class Booter.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Booter implements BooterInterface
{
    public function __construct(
        private readonly IncluderInterface $includer
    ) {
    }

    /**
     * @inheritDoc
     */
    public function boot(string $configPath): void
    {
        $bootConfig = $this->includer->isolatedInclude($configPath);

        if (!($bootConfig instanceof BootConfigInterface)) {
            throw new LogicException(
                sprintf(
                    'Return value of module %s is expected to be an instance of %s but was not',
                    $configPath,
                    BootConfigInterface::class
                )
            );
        }

        Nytris::boot($bootConfig);
    }
}
