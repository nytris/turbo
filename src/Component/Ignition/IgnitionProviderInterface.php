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
 * Interface IgnitionProviderInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface IgnitionProviderInterface
{
    /**
     * Provides the Ignition component.
     */
    public function provideIgnition(): IgnitionInterface;
}
