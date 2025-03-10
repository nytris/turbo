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

namespace Nytris\Turbo\Binary\Turbo;

use Exception;
use Nytris\Turbo\Binary\Turbo\Command\IgnitionCommand;
use Nytris\Turbo\Component\Ignition\IgnitionProviderInterface;
use Nytris\Turbo\Nytris\BooterInterface;
use React\EventLoop\Loop;
use Symfony\Component\Console\Application;

/**
 * Class TurboBinary.
 *
 * Defines the Turbo binary logic.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TurboBinary
{
    public function __construct(
        private readonly Application $application,
        private readonly BooterInterface $booter,
        private readonly IgnitionProviderInterface $ignitionProvider
    ) {
    }

    /**
     * Runs the binary.
     *
     * @throws Exception
     */
    public function run(): void
    {
        $ignitionCommand = new IgnitionCommand($this->booter, $this->ignitionProvider, Loop::get());

        $this->application->add($ignitionCommand);
        $this->application->setDefaultCommand($ignitionCommand->getName());

        $this->application->run();
    }
}
