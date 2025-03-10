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

namespace Nytris\Turbo\Cgi\Cylinder;

use Exception;
use Nytris\Turbo\Component\Cylinder\CylinderProviderInterface;
use Nytris\Turbo\Nytris\BooterInterface;
use React\EventLoop\LoopInterface;

/**
 * Class CylinderCgi.
 *
 * Defines the Cylinder CGI application logic.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CylinderCgi
{
    public function __construct(
        private readonly BooterInterface $booter,
        private readonly CylinderProviderInterface $cylinderProvider,
        private readonly LoopInterface $eventLoop
    ) {
    }

    /**
     * Starts the long-lived CGI application.
     *
     * @throws Exception
     */
    public function run(?string $configPath, int $cylinderIndex): void
    {
        if ($configPath !== null) {
            $this->booter->boot($configPath);
        }

        $cylinder = $this->cylinderProvider->provideCylinder();
        $cylinder->start($cylinderIndex);

        $this->eventLoop->run();
    }
}
