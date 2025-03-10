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

namespace Nytris\Turbo\Binary\Cylinder\Command;

use Nytris\Turbo\Component\Cylinder\CylinderProviderInterface;
use Nytris\Turbo\Nytris\BooterInterface;
use React\EventLoop\LoopInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class StartCommand.
 *
 * Starts the Cylinder.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
#[AsCommand(name: 'start', description: 'Starts the Cylinder (Worker process run under CLI or PHP-FPM)', aliases: ['s'])]
class StartCommand extends Command
{
    public function __construct(
        private readonly BooterInterface $booter,
        private readonly CylinderProviderInterface $cylinderProvider,
        private readonly LoopInterface $eventLoop
    ) {
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->setHelp('Starts the Cylinder (Worker process run under CLI or PHP-FPM)');

        $this->addArgument(
            name: 'index',
            mode: InputArgument::REQUIRED,
            description: 'Cylinder index'
        );

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

        $cylinder = $this->cylinderProvider->provideCylinder();
        $cylinder->start((int) $input->getArgument('index'));

        $this->eventLoop->run();

        return self::SUCCESS;
    }
}
