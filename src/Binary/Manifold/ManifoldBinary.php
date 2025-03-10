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

namespace Nytris\Turbo\Binary\Manifold;

use Exception;
use Nytris\Turbo\Binary\Manifold\Command\StartCommand;
use Nytris\Turbo\Component\Manifold\ManifoldProviderInterface;
use Nytris\Turbo\Nytris\BooterInterface;
use React\EventLoop\Loop;
use Symfony\Component\Console\Application;

/**
 * Class ManifoldBinary.
 *
 * Defines the Manifold binary logic.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ManifoldBinary
{
    public function __construct(
        private readonly Application $application,
        private readonly BooterInterface $booter,
        private readonly ManifoldProviderInterface $manifoldProvider
    ) {
    }

    /**
     * Runs the binary.
     *
     * @throws Exception
     */
    public function run(): void
    {
        $startCommand = new StartCommand($this->booter, $this->manifoldProvider, Loop::get());

        $this->application->add($startCommand);
        $this->application->setDefaultCommand($startCommand->getName());

        $this->application->run();
    }
}
