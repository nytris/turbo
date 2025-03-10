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

namespace Nytris\Turbo\Component\Cylinder;

use Nytris\Turbo\Component\Cylinder\Valve\ValveInterface;
use Nytris\Turbo\Component\Manifold\Exhaust\Rpc\Handler\LaunchCommandHandler;
use Nytris\Turbo\Component\Rpc\RpcInterface;
use React\Promise\PromiseInterface;

/**
 * Class Cylinder.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Cylinder implements CylinderInterface
{
    public function __construct(
        private readonly ValveInterface $valve,
        private readonly RpcInterface $manifoldRpc
    ) {
    }

    /**
     * @inheritDoc
     */
    public function start(int $cylinderIndex): PromiseInterface
    {
        return $this->valve->start()
            ->then(function () use ($cylinderIndex) {
                return $this->manifoldRpc->call(
                    handlerFqcn: LaunchCommandHandler::class,
                    method: 'ready',
                    args: [$cylinderIndex]
                );
            });
    }
}
