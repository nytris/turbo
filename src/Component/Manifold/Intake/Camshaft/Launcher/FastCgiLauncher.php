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

use Lisachenko\Protocol\FCGI;
use Lisachenko\Protocol\FCGI\FrameParser;
use LogicException;
use Nytris\Turbo\Component\Manifold\Exhaust\CylinderCoordinator\Launcher\FastCgiStreamListener;
use Nytris\Turbo\Component\Manifold\Exhaust\CylinderCoordinator\Launcher\FastCgiStreamTransmitter;
use Nytris\Turbo\Component\Manifold\Exhaust\Response\ResponseHandlerInterface;
use Nytris\Turbo\Component\Manifold\Intake\Camshaft\Request\RequestContext;
use Nytris\Turbo\Component\Manifold\Intake\Camshaft\Worker\LaunchingWorkerCollectionInterface;
use Nytris\Turbo\Component\Manifold\Intake\Camshaft\Worker\WorkerFactoryInterface;
use Nytris\Turbo\Component\Rpc\Transport\Stream\StreamContext;
use React\Promise\PromiseInterface;
use React\Socket\ConnectionInterface;
use React\Socket\Connector;
use React\Socket\ConnectorInterface;
use RuntimeException;

/**
 * Class FastCgiLauncher.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FastCgiLauncher implements WorkerLauncherInterface
{
    /**
     * Special FastCGI request ID for the single worker spawn request.
     */
    public const SPAWN_REQUEST_ID = 1;

    private readonly string $cylinderScriptPath;

    public function __construct(
        private readonly string $fastCgiServerUri,
        private readonly ConnectorInterface $connector = new Connector(),
        ?string $cylinderScriptPath = null
    ) {
        $this->cylinderScriptPath = $cylinderScriptPath ?? dirname(__DIR__, 6) . '/cgi-bin/cylinder.php';
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
        return $this->connector->connect($this->fastCgiServerUri)
            ->then(function (ConnectionInterface $fastCgiConnection) use (
                $cylinderIndex,
                $launchingWorkerCollection,
                $responseHandler,
                $workerFactory
            ) {
                $promise = $launchingWorkerCollection->awaitLaunchingWorker($cylinderIndex)
                    ->then(function (ConnectionInterface $rpcConnection) use (
                        $cylinderIndex,
                        $fastCgiConnection,
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
                            new FastCgiStreamListener($rpcStreamContext),
                            new FastCgiStreamTransmitter($rpcStreamContext)
                        );

                        $stdoutBuffer = '';

                        $fastCgiConnection->on('data', function (string $chunk) use (
                            $requestContext,
                            $responseHandler,
                            &$stdoutBuffer
                        ) {
                            $stdoutBuffer .= $chunk;

                            while (FrameParser::hasFrame($stdoutBuffer)) {
                                $record = FrameParser::parseFrame($stdoutBuffer);

                                /*
                                 * Note that the FastCGI record's request ID will always be the same
                                 * (the fixed spawn request ID) for the single request used to spawn the worker.
                                 *
                                 * The current request ID can be fetched from the RequestContext.
                                 */
                                if ($record->getRequestId() !== self::SPAWN_REQUEST_ID) {
                                    throw new LogicException(sprintf(
                                        'Expected request ID %d but got %d',
                                        self::SPAWN_REQUEST_ID,
                                        $record->getRequestId()
                                    ));
                                }

                                $request = $requestContext->getCurrentRequest();

                                if ($record instanceof FCGI\Record\Stdout) {
                                    $responseHandler->appendResponseBodyChunk(
                                        $request->getConnectionId(),
                                        $request->getRequestId(),
                                        $record->getContentData()
                                    );
                                } elseif ($record instanceof FCGI\Record\EndRequest) {
                                    // The spawned worker's FastCGI request has ended: this means
                                    // the worker itself has exited and must be relaunched
                                    // in order to process further requests.

                                    // FIXME: Relaunch via/using RequestTable.
                                    throw new RuntimeException('FIXME: Need to relaunch worker');
                                } else {
                                    throw new RuntimeException('Unsupported FastCGI record type: ' . $record->getType());
                                }
                            }
                        });

                        return $worker->start();
                    });

                // Make FastCGI request with cylinder index as querystring arg.
                $records = [
                    (new FCGI\Record\BeginRequest(role: FCGI::RESPONDER))->setRequestId(self::SPAWN_REQUEST_ID),
                    (new FCGI\Record\Params([
                        'QUERY_STRING' => 'cylinderIndex=' . $cylinderIndex,
                        'REQUEST_METHOD' => 'GET',
                        'SCRIPT_FILENAME' => $this->cylinderScriptPath,
                    ]))->setRequestId(self::SPAWN_REQUEST_ID),
                    // Spec requires an empty Params record to mark the end of params.
                    (new FCGI\Record\Params([]))->setRequestId(self::SPAWN_REQUEST_ID),
                    // Spec requires an empty Stdin record to mark the end of input.
                    (new FCGI\Record\Stdin(''))->setRequestId(self::SPAWN_REQUEST_ID)
                ];
                $fastCgiConnection->write(implode('', $records));

                return $promise;
            });
    }
}
