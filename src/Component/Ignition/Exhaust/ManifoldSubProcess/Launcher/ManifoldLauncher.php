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

namespace Nytris\Turbo\Component\Ignition\Exhaust\ManifoldSubProcess\Launcher;

use React\ChildProcess\Process;

/**
 * Class ManifoldLauncher.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ManifoldLauncher implements ManifoldLauncherInterface
{
    /**
     * @inheritDoc
     */
    public function launchManifold(?string $configPath): Process
    {
        $manifoldPath = dirname(__DIR__, 6) . '/libexec/manifold';
        $cmd = PHP_BINARY . ' -dxdebug.mode=debug ' . $manifoldPath . ' start';

        if ($configPath !== null) {
            $cmd .= ' -c ' . escapeshellarg($configPath);
        }

        $process = new Process(cmd: $cmd);

        $process->start();

        return $process;
    }
}
