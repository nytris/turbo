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

namespace Nytris\Turbo\Component\Manifold\Intake;

use React\Promise\PromiseInterface;

/**
 * Interface CompressorInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface CompressorInterface
{
    /**
     * Starts the Compressor, receiving HTTP or FastCGI requests.
     *
     * @param string|null $configPath
     * @return PromiseInterface<static>
     */
    public function start(?string $configPath): PromiseInterface;
}
