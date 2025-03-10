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

namespace Nytris\Turbo\Binary\Turbo\Command;

use Nytris\Turbo\Component\Ignition\IgnitionProviderInterface;
use Nytris\Turbo\Nytris\BooterInterface;
use React\EventLoop\LoopInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class IgnitionCommand.
 *
 * Starts the Ignition.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
#[AsCommand(name: 'ignition', description: 'Starts the Ignition (HTTP or FastCGI request processor)', aliases: ['t'])]
class IgnitionCommand extends Command
{
    public function __construct(
        private readonly BooterInterface $booter,
        private readonly IgnitionProviderInterface $ignitionProvider,
        private readonly LoopInterface $eventLoop
    ) {
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->setHelp('Starts the Ignition (HTTP or FastCGI request processor)');

        $this->addOption(
            name: 'listen',
            shortcut: 'l',
            mode: InputOption::VALUE_REQUIRED,
            description: 'The URI to listen on (e.g. "tcp://1.2.3.4:5678" or "unix:///path/to/socket.sock")'
        );
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

        $ignition = $this->ignitionProvider->provideIgnition();
        $ignition->start(
            listenUri: $input->hasOption('listen') ?
                $input->getOption('listen') :
                null,
            configPath: $configPath
        );

        $this->eventLoop->run();

        return self::SUCCESS;
    }
}
