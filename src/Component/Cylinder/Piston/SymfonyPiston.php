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

use Nytris\Turbo\Component\Cylinder\Exhaust\Response\Response;
use Nytris\Turbo\Http\RequestInterface;
use React\Promise\PromiseInterface;
use function React\Promise\resolve;

/**
 * Class SymfonyPiston.
 *
 * Handles HTTP requests that were received over either HTTP or FastCGI.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SymfonyPiston implements PistonInterface
{
    /**
     * @inheritDoc
     */
    public function handleRequest(RequestInterface $request): PromiseInterface
    {
        $response = new Response(
            statusCode: 200,
            reasonPhrase: 'OK',
            headers: [
                'Content-Type' => ['text/plain'],
                'X-My-Custom-Response-Header' => ['Hello world!'],
            ],
            bodyCallback: function () {
                echo 'Hello world from Nytris Turbo!';
            }
        );

        return resolve($response);
    }
}
