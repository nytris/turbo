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

namespace Nytris\Turbo\Component\Manifold\Intake\Camshaft\Request;

use Nytris\Turbo\Http\RequestInterface;

/**
 * Interface RequestContextInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface RequestContextInterface
{
    /**
     * Marks the beginning of handling the given request.
     */
    public function beginRequest(RequestInterface $request): void;

    /**
     * Marks the end of handling the current request.
     */
    public function endRequest(): void;

    /**
     * Fetches the current request.
     */
    public function getCurrentRequest(): RequestInterface;
}
