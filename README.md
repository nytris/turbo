# Nytris Turbo

[![Build Status](https://github.com/nytris/turbo/workflows/CI/badge.svg)](https://github.com/nytris/turbo/actions?query=workflow%3ACI)

[EXPERIMENTAL] Turbocharged HTTP/FastCGI for PHP.

## What is it?
Handle multiple HTTP/FastCGI requests concurrently per process for maximum resource utilisation.

## How does it work?
Provides a FastCGI server that spawns multiple long-lived worker processes to handle requests.
This allows the environment to be reused between requests, which is much more efficient
when that environment is a booted Symfony kernel for example.

## Usage
Install this package with Composer:

```shell
$ composer require nytris/turbo
```

#### Configure Nytris platform:

`nytris.config.php`

```php
<?php

declare(strict_types=1);

use Nytris\Boot\BootConfig;
use Nytris\Boot\PlatformConfig;
use Nytris\Turbo\TurboPackage;

$bootConfig = new BootConfig(new PlatformConfig(__DIR__ . '/var/cache/nytris/'));

$bootConfig->installPackage(new TurboPackage(
    
));

return $bootConfig;
```

### Start the CGI worker pool

```shell
$ vendor/bin/turbo
```

`ignition` process spawns `manifold`, which in turn spawns the pool of CGI `cylinder` processes.
`ignition` is used to restart `manifold` and `cylinder` when needed, such as following a deployment
of new code.

`ignition` takes no arguments, instead configuration is done through the Nytris package installation
in `nytris.config.php` [as documented above](#configure-nytris-platform).

## See also
- [PHP Code Shift][PHP Code Shift]

[PHP Code Shift]: https://github.com/asmblah/php-code-shift
