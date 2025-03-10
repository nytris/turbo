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

namespace Nytris\Turbo\Component\Rpc\Dispatcher;

use BadMethodCallException;
use Nytris\Turbo\Component\Rpc\Handler\HandlerInterface;

/**
 * Interface DispatcherInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface DispatcherInterface
{
    /**
     * Dispatches a call to a method of a registered handler.
     *
     * Throws BadMethodCallException if no handler is registered for the given FQCN.
     * Invokes ->onUndefinedMethod() on the handler if the method is not defined.
     *
     * @param string $handlerFqcn
     * @param string $method
     * @param array<mixed> $args
     * @throws BadMethodCallException if no handler is registered for the given FQCN.
     */
    public function dispatch(string $handlerFqcn, string $method, array $args): mixed;

    /**
     * Registers an RPC handler.
     */
    public function registerHandler(HandlerInterface $handler): void;
}
