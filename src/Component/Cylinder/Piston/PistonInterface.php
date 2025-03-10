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

namespace Nytris\Turbo\Component\Cylinder\Piston;

use Nytris\Turbo\Http\RequestInterface;
use Nytris\Turbo\Http\ResponseInterface;
use React\Promise\PromiseInterface;

/**
 * Interface PistonInterface.
 *
 * Handles HTTP requests that were received over either HTTP or FastCGI.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface PistonInterface
{
    /**
     * Handles a HTTP request.
     *
     * @return PromiseInterface<ResponseInterface>
     */
    public function handleRequest(RequestInterface $request): PromiseInterface;
}
