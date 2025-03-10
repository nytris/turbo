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

namespace Nytris\Turbo\Binary\Manifold\Command;

use Nytris\Turbo\Component\Manifold\ManifoldProviderInterface;
use Nytris\Turbo\Nytris\BooterInterface;
use React\EventLoop\LoopInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class StartCommand.
 *
 * Starts the Manifold.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
#[AsCommand(name: 'start', description: 'Starts the Manifold (Worker process that talks to CLI or PHP-FPM)', aliases: ['s'])]
class StartCommand extends Command
{
    public function __construct(
        private readonly BooterInterface $booter,
        private readonly ManifoldProviderInterface $manifoldProvider,
        private readonly LoopInterface $eventLoop
    ) {
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->setHelp('Starts the Manifold (Worker process that talks to CLI or PHP-FPM)');

        // --nytris option allows the Nytris platform config to be overridden.
        $this->addOption(
            name: 'nytris',
            shortcut: 'c',
            mode: InputOption::VALUE_REQUIRED,
            description: 'Path to Nytris config to use (e.g. "./nytris.config.php")'
        );
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configPath = $input->getOption('nytris');

        if ($configPath !== null) {
            $this->booter->boot($configPath);
        }

        $manifold = $this->manifoldProvider->provideManifold();
        $manifold->start($configPath);

        $this->eventLoop->run();

        return self::SUCCESS;
    }
}
