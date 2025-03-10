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

use Nytris\Turbo\Component\Manifold\Exhaust\TurbineInterface;
use Nytris\Turbo\Component\Manifold\Intake\CompressorInterface;
use Nytris\Turbo\Component\Rpc\Transport\Stream\StreamContextInterface;
use Nytris\Turbo\Component\Rpc\Transport\TransportInterface;
use React\Stream\ReadableResourceStream;
use React\Stream\WritableResourceStream;

/**
 * Class Manifold.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Manifold implements ManifoldInterface
{
    public function __construct(
        private readonly StreamContextInterface $streamContext,
        private readonly TransportInterface $ignitionTransport,
        private readonly CompressorInterface $compressor,
        private readonly TurbineInterface $turbine
    ) {
    }

    /**
     * @inheritDoc
     */
    public function start(?string $configPath): void
    {
        $inputStream = new ReadableResourceStream(STDIN);
        $outputStream = new WritableResourceStream(STDOUT);

        $this->streamContext->useStreams($inputStream, $outputStream);

        $this->turbine->start()
            ->then(fn () => $this->compressor->start($configPath))
            ->then(function () {
                // Start listening for RPC from Ignition now that Manifold is ready.
                $this->ignitionTransport->listen();
                $this->ignitionTransport->resume();
            });
    }
}
