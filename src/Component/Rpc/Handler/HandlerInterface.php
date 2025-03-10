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

namespace Nytris\Turbo\Component\Rpc\Handler;

/**
 * Interface HandlerInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface HandlerInterface
{
    /**
     * Called when an RPC call is received for a method not defined by the handler.
     *
     * @param string $method
     * @param array<mixed> $args
     */
    public function onUndefinedMethod(string $method, array $args): mixed;
}
