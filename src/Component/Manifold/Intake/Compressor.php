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

use Nytris\Turbo\Component\Manifold\Intake\Camshaft\CamshaftInterface;
use Nytris\Turbo\Component\Manifold\Intake\Tappet\TappetInterface;
use React\Promise\PromiseInterface;

/**
 * Class Compressor.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Compressor implements CompressorInterface
{
    public function __construct(
        private readonly CamshaftInterface $camshaft,
        private readonly TappetInterface $tappet
    ) {
    }

    /**
     * @inheritDoc
     */
    public function start(?string $configPath): PromiseInterface
    {
        return $this->tappet->start()
            ->then(fn () => $this->camshaft->start($configPath))
            ->then(fn () => $this);
    }
}
