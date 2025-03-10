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

namespace Nytris\Turbo\Component\Rpc;

use React\Promise\PromiseInterface;

/**
 * Interface RpcInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface RpcInterface
{
    /**
     * Calls a method on a remote handler object, returning the result as a promise.
     *
     * @param string $handlerFqcn
     * @param string $method
     * @param array<mixed> $args
     * @return PromiseInterface<mixed>
     */
    public function call(string $handlerFqcn, string $method, array $args = []): PromiseInterface;
}
