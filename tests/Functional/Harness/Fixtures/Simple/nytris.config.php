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

use Nytris\Boot\BootConfig;
use Nytris\Boot\PlatformConfig;
use Nytris\Turbo\TurboPackage;

$bootConfig = new BootConfig(
    new PlatformConfig(dirname(__DIR__, 4) . '/var/cache/nytris')
);

$baseDir = dirname(__DIR__, 5);
$dataDir = $baseDir . '/var/test';

$bootConfig->installPackage(new TurboPackage(
    cylinderCount: 1,
    tappetListenUri: 'unix://' . $dataDir . '/tappet.test.sock',
    cylinderValveConnectUri: 'unix://' . $dataDir . '/tappet.test.sock',
));

return $bootConfig;
