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

use Nytris\Core\Includer\Includer;
use Nytris\Turbo\Cgi\Cylinder\CylinderCgi;
use Nytris\Turbo\Component\Cylinder\CylinderProvider;
use Nytris\Turbo\Nytris\Booter;
use React\EventLoop\Loop;

require_once $_composer_autoload_path ?? __DIR__ . '/../vendor/autoload.php';

// TODO: Call set_time_limit() at start of a request's handling,
//       as it should be applied relative to the current time.

// Cylinder PHP-FPM worker request entrypoint.
(
    new CylinderCgi(
        new Booter(new Includer()),
        new CylinderProvider(),
        Loop::get()
    )
)->run(
    configPath: $_GET['nytris'] ?? null,
    cylinderIndex: (int) $_GET['index']
);
