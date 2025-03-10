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

use Nytris\Turbo\Component\Ignition\Exhaust\ManifoldSubProcess\Launcher\ManifoldLauncherInterface;
use Nytris\Turbo\Component\Rpc\Transport\Stream\StreamContextInterface;
use Nytris\Turbo\Component\Rpc\Transport\TransportInterface;
use React\ChildProcess\Process;

/**
 * Class ManifoldCoordinator.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ManifoldCoordinator implements ManifoldCoordinatorInterface
{
    private ?Process $process = null;

    public function __construct(
        private readonly ManifoldLauncherInterface $launcher,
        private readonly StreamContextInterface $streamContext,
        private readonly TransportInterface $transport
    ) {
    }

    /**
     * @inheritDoc
     */
    public function launch(?string $configPath): void
    {
        $this->process = $this->launcher->launchManifold($configPath);

        $this->process->on('exit', function () {
            // Start queueing messages until we have restarted the Manifold process.
            $this->transport->pause();

            // FIXME: Only do this if the stop was unexpected.
//            $this->restarter->restart($this);
        });

        $this->process->stderr->on('data', function (string $data) {
            print 'STDERR:' . $data . PHP_EOL;
        });
        $this->process->stdout->on('data', function (string $data) {
            print 'STDOUT:' . $data . PHP_EOL;
        });

        $this->streamContext->useStreams(
            $this->process->stdout,
            $this->process->stdin
        );
        $this->transport->listen();
        $this->transport->resume();
    }
}
