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

namespace Nytris\Turbo\Component\Ignition;

use Nytris\Turbo\Component\Ignition\Exhaust\ManifoldSubProcess\ManifoldCoordinatorInterface;
use Nytris\Turbo\Component\Ignition\Intake\Acceptor\AcceptorInterface;
use Nytris\Turbo\Component\Ignition\Intake\Server\ServerFactoryInterface;

/**
 * Class Ignition.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Ignition implements IgnitionInterface
{
    public function __construct(
        private readonly ServerFactoryInterface $serverFactory,
        private readonly AcceptorInterface $acceptor,
        private readonly ManifoldCoordinatorInterface $manifoldCoordinator,
        private readonly string $listenUri
    ) {
    }

    /**
     * @inheritDoc
     */
    public function start(?string $listenUri, ?string $configPath): void
    {
        $server = $this->serverFactory->createServer($listenUri ?? $this->listenUri);

        $this->acceptor->listen($server);
        $this->manifoldCoordinator->launch($configPath);

        echo 'Listening on ' . str_replace('tcp:', 'http:', $server->getAddress()) . PHP_EOL;
    }
}
