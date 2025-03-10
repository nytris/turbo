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

namespace Nytris\Turbo\Component\Manifold\Intake\Camshaft\Launcher;

use Nytris\Turbo\Component\Manifold\Exhaust\Response\ResponseHandlerInterface;
use Nytris\Turbo\Component\Manifold\Intake\Camshaft\Request\RequestContext;
use Nytris\Turbo\Component\Manifold\Intake\Camshaft\Worker\LaunchingWorkerCollectionInterface;
use Nytris\Turbo\Component\Manifold\Intake\Camshaft\Worker\WorkerFactoryInterface;
use Nytris\Turbo\Component\Rpc\Transport\Listener\StreamListener;
use Nytris\Turbo\Component\Rpc\Transport\Stream\StreamContext;
use Nytris\Turbo\Component\Rpc\Transport\Transmitter\StreamTransmitter;
use React\ChildProcess\Process;
use React\Promise\PromiseInterface;
use React\Socket\ConnectionInterface;
use RuntimeException;

/**
 * Class CliLauncher.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CliLauncher implements WorkerLauncherInterface
{
    private readonly string $cylinderBinaryPath;

    public function __construct(
        ?string $cylinderBinaryPath = null
    ) {
        $this->cylinderBinaryPath = $cylinderBinaryPath ?? dirname(__DIR__, 6) . '/libexec/cylinder';
    }

    /**
     * @inheritDoc
     */
    public function launchWorker(
        int $cylinderIndex,
        LaunchingWorkerCollectionInterface $launchingWorkerCollection,
        WorkerFactoryInterface $workerFactory,
        ResponseHandlerInterface $responseHandler,
        ?string $configPath
    ): PromiseInterface {
        // FIXME: Remove Xdebug mode when not debugging.
        $cmd = PHP_BINARY . ' -dxdebug.mode=debug ' . $this->cylinderBinaryPath . ' start ' . $cylinderIndex;

        if ($configPath !== null) {
            $cmd .= ' -c ' . escapeshellarg($configPath);
        }

        $process = new Process(cmd: $cmd);

        $process->start();

        return $launchingWorkerCollection->awaitLaunchingWorker($cylinderIndex)
            ->then(function (ConnectionInterface $rpcConnection) use (
                $cylinderIndex,
                $process,
                $responseHandler,
                $workerFactory
            ) {
                $rpcStreamContext = new StreamContext();
                $rpcStreamContext->useStreams($rpcConnection, $rpcConnection);
                $requestContext = new RequestContext();

                $worker = $workerFactory->createWorker(
                    $cylinderIndex,
                    $rpcStreamContext,
                    $requestContext,
                    new StreamListener($rpcStreamContext),
                    new StreamTransmitter($rpcStreamContext)
                );

                $process->stdout->on('data', function (string $chunk) use (
                    $requestContext,
                    $responseHandler
                ) {
                    $request = $requestContext->getCurrentRequest();

                    $responseHandler->appendResponseBodyChunk(
                        $request->getConnectionId(),
                        $request->getRequestId(),
                        $chunk
                    );
                });

                $process->on('exit', function () {
                    // The worker itself has exited and must be relaunched
                    // in order to process further requests.

                    // FIXME: Relaunch via/using RequestTable.
                    throw new RuntimeException('FIXME: Need to relaunch worker');
                });

                return $worker->start();
            });
    }
}
