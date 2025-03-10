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

namespace Nytris\Turbo;

use Closure;
use Nytris\Turbo\Component\Cylinder\Cylinder;
use Nytris\Turbo\Component\Cylinder\CylinderInterface;
use Nytris\Turbo\Component\Cylinder\Piston\SymfonyPiston;
use Nytris\Turbo\Component\Cylinder\Rpc\Handler\RequestCommandHandler;
use Nytris\Turbo\Component\Cylinder\Valve\Valve;
use Nytris\Turbo\Component\Ignition\Connection\ConnectionTable;
use Nytris\Turbo\Component\Ignition\Exhaust\ManifoldSubProcess\Launcher\ManifoldLauncher;
use Nytris\Turbo\Component\Ignition\Exhaust\ManifoldSubProcess\ManifoldCoordinator;
use Nytris\Turbo\Component\Ignition\Exhaust\Rpc\Handler\ConnectionCommandHandler;
use Nytris\Turbo\Component\Ignition\Ignition;
use Nytris\Turbo\Component\Ignition\IgnitionInterface;
use Nytris\Turbo\Component\Ignition\Intake\Acceptor\Acceptor;
use Nytris\Turbo\Component\Ignition\Intake\Server\ServerFactory;
use Nytris\Turbo\Component\Manifold\Exhaust\Protocol\FastCgi\FastCgiProtocolResponseHandler;
use Nytris\Turbo\Component\Manifold\Exhaust\Response\ResponseHandler;
use Nytris\Turbo\Component\Manifold\Exhaust\Rpc\Handler\ResponseCommandHandler;
use Nytris\Turbo\Component\Manifold\Exhaust\Turbine;
use Nytris\Turbo\Component\Manifold\Intake\Camshaft\Camshaft;
use Nytris\Turbo\Component\Manifold\Intake\Camshaft\Launcher\CliLauncher;
use Nytris\Turbo\Component\Manifold\Intake\Camshaft\Launcher\WorkerLauncherInterface;
use Nytris\Turbo\Component\Manifold\Intake\Camshaft\Request\RequestTable;
use Nytris\Turbo\Component\Manifold\Intake\Camshaft\Worker\LaunchingWorkerCollection;
use Nytris\Turbo\Component\Manifold\Intake\Camshaft\Worker\WorkerFactory;
use Nytris\Turbo\Component\Manifold\Intake\Compressor;
use Nytris\Turbo\Component\Manifold\Intake\Protocol\FastCgi\FastCgiProtocolRequestHandler;
use Nytris\Turbo\Component\Manifold\Intake\Request\CylinderRequestHandler;
use Nytris\Turbo\Component\Manifold\Intake\Rpc\Handler\ConnectionCommandHandler as ManifoldIntakeConnectionCommandHandler;
use Nytris\Turbo\Component\Manifold\Intake\Tappet\Tappet;
use Nytris\Turbo\Component\Manifold\Manifold;
use Nytris\Turbo\Component\Manifold\ManifoldInterface;
use Nytris\Turbo\Component\Rpc\Call\CallTable;
use Nytris\Turbo\Component\Rpc\Dispatcher\Dispatcher;
use Nytris\Turbo\Component\Rpc\Rpc;
use Nytris\Turbo\Component\Rpc\Transport\Listener\StreamListener;
use Nytris\Turbo\Component\Rpc\Transport\Receiver\Receiver;
use Nytris\Turbo\Component\Rpc\Transport\Stream\StreamContext;
use Nytris\Turbo\Component\Rpc\Transport\Transmitter\StreamTransmitter;
use Nytris\Turbo\Component\Rpc\Transport\Transport;
use React\Socket\Connector;

/**
 * Class TurboPackage.
 *
 * Configures the installation of Nytris Turbo.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TurboPackage implements TurboPackageInterface
{
    public function __construct(
        private readonly int $cylinderCount = self::DEFAULT_CYLINDER_COUNT,
        private readonly string $listenUri = 'tcp://0.0.0.0:5678',
        private readonly string $tappetListenUri = 'tcp://0.0.0.0:5679',
        private readonly string $cylinderValveConnectUri = 'tcp://127.0.0.1:5679',
        private readonly WorkerLauncherInterface $cylinderWorkerLauncher = new CliLauncher(),
        /**
         * @var (Closure(Closure(int): IgnitionInterface, TurboPackageInterface): IgnitionInterface)|null
         */
        private readonly ?Closure $ignitionComponentFactory = null,
        /**
         * @var (Closure(Closure(int): ManifoldInterface, TurboPackageInterface): ManifoldInterface)|null
         */
        private readonly ?Closure $manifoldComponentFactory = null,
        /**
         * @var (Closure(Closure(int): CylinderInterface, TurboPackageInterface): CylinderInterface)|null
         */
        private readonly ?Closure $cylinderComponentFactory = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getCylinderCount(): int
    {
        return $this->cylinderCount;
    }

    /**
     * @inheritDoc
     */
    public function getCylinderComponentFactory(): Closure
    {
        $defaultFactory = function () {
            $cylinderFromManifoldCallTable = new CallTable();
            $cylinderFromManifoldDispatcher = new Dispatcher();
            $cylinderToManifoldStreamContext = new StreamContext();
            $cylinderToManifoldTransmitter = new StreamTransmitter($cylinderToManifoldStreamContext);
            $cylinderFromManifoldReceiver = new Receiver(
                $cylinderFromManifoldCallTable,
                $cylinderFromManifoldDispatcher,
                $cylinderToManifoldTransmitter
            );
            $cylinderToManifoldTransport = new Transport(
                $cylinderToManifoldTransmitter,
                new StreamListener($cylinderToManifoldStreamContext),
                $cylinderFromManifoldReceiver
            );
            $cylinderToManifoldRpc = new Rpc(
                $cylinderFromManifoldCallTable,
                $cylinderToManifoldTransport
            );
            $valve = new Valve(
                $cylinderToManifoldStreamContext,
                $cylinderToManifoldTransport,
                new Connector(),
                $this->cylinderValveConnectUri
            );

            $cylinderFromManifoldDispatcher->registerHandler(
                new RequestCommandHandler(
                    $cylinderToManifoldRpc,
                    new SymfonyPiston()
                )
            );

            return new Cylinder(
                $valve,
                $cylinderToManifoldRpc
            );
        };

        return $this->cylinderComponentFactory !== null ?
            fn () => ($this->cylinderComponentFactory)($defaultFactory, $this) :
            $defaultFactory;
    }

    /**
     * @inheritDoc
     */
    public function getCylinderValveConnectUri(): string
    {
        return $this->cylinderValveConnectUri;
    }

    /**
     * @inheritDoc
     */
    public function getIgnitionComponentFactory(): Closure
    {
        $defaultFactory = function () {
            $serverFactory = new ServerFactory();

            $ignitionToManifoldCallTable = new CallTable();
            $ignitionFromManifoldDispatcher = new Dispatcher();
            $ignitionToManifoldStreamContext = new StreamContext();
            $ignitionToManifoldTransmitter = new StreamTransmitter($ignitionToManifoldStreamContext);
            $ignitionFromManifoldReceiver = new Receiver(
                $ignitionToManifoldCallTable,
                $ignitionFromManifoldDispatcher,
                $ignitionToManifoldTransmitter
            );
            $ignitionToManifoldTransport = new Transport(
                $ignitionToManifoldTransmitter,
                new StreamListener($ignitionToManifoldStreamContext),
                $ignitionFromManifoldReceiver
            );
            $ignitionToManifoldRpc = new Rpc(
                $ignitionToManifoldCallTable,
                $ignitionToManifoldTransport
            );
            $ignitionConnectionTable = new ConnectionTable();

            $ignitionFromManifoldDispatcher->registerHandler(new ConnectionCommandHandler(
                $ignitionConnectionTable
            ));

//            // FIXME
//            $ignitionToManifoldTransmitter->directConnect($manifoldFromIgnitionReceiver);
//            $manifoldToIgnitionTransmitter->directConnect($ignitionFromManifoldReceiver);
//            $manifoldToCylinderTransmitter->directConnect($cylinderFromManifoldReceiver);
//            $cylinderToManifoldTransmitter->directConnect($manifoldFromCylinderReceiver);

            return new Ignition(
                $serverFactory,
                new Acceptor(
                    $ignitionToManifoldRpc,
                    $ignitionConnectionTable
                ),
                new ManifoldCoordinator(
                    new ManifoldLauncher(),
                    $ignitionToManifoldStreamContext,
                    $ignitionToManifoldTransport
                ),
                $this->listenUri
            );
        };

        return $this->ignitionComponentFactory !== null ?
            fn () => ($this->ignitionComponentFactory)($defaultFactory, $this) :
            $defaultFactory;
    }

    /**
     * @inheritDoc
     */
    public function getListenUri(): string
    {
        return $this->listenUri;
    }

    /**
     * @inheritDoc
     */
    public function getManifoldComponentFactory(): Closure
    {
        $defaultFactory = function () {
            $manifoldFromIgnitionCallTable = new CallTable();
            $manifoldFromIgnitionDispatcher = new Dispatcher();
            $manifoldToIgnitionStreamContext = new StreamContext();
            $manifoldToIgnitionTransmitter = new StreamTransmitter($manifoldToIgnitionStreamContext);
            $manifoldFromIgnitionReceiver = new Receiver(
                $manifoldFromIgnitionCallTable,
                $manifoldFromIgnitionDispatcher,
                $manifoldToIgnitionTransmitter
            );
            $manifoldToIgnitionTransport = new Transport(
                $manifoldToIgnitionTransmitter,
                new StreamListener($manifoldToIgnitionStreamContext),
                $manifoldFromIgnitionReceiver
            );
            $manifoldToIgnitionRpc = new Rpc(
                $manifoldFromIgnitionCallTable,
                $manifoldToIgnitionTransport
            );

            $manifoldToCylinderCallTable = new CallTable();
            $manifoldFromCylinderDispatcher = new Dispatcher();
            $camshaftRequestTable = new RequestTable();
            $responseHandler = new ResponseHandler(
                new FastCgiProtocolResponseHandler($manifoldToIgnitionRpc)
            );
            $launchingWorkerCollection = new LaunchingWorkerCollection();
            $camshaft = new Camshaft(
                $camshaftRequestTable,
                $this->cylinderWorkerLauncher,
                $launchingWorkerCollection,
                new WorkerFactory(
                    $manifoldFromCylinderDispatcher
                ),
                $responseHandler,
                $this->cylinderCount
            );

            $manifoldFromIgnitionDispatcher->registerHandler(new ManifoldIntakeConnectionCommandHandler(
                new FastCgiProtocolRequestHandler(
                    new CylinderRequestHandler($camshaft)
                )
            ));

            $manifoldFromCylinderDispatcher->registerHandler(
                new ResponseCommandHandler(
                    $responseHandler,
                    $camshaftRequestTable
                )
            );

            return new Manifold(
                $manifoldToIgnitionStreamContext,
                $manifoldToIgnitionTransport,
                new Compressor(
                    $camshaft,
                    new Tappet(
                        $launchingWorkerCollection,
                        $this->tappetListenUri
                    )
                ),
                new Turbine()
            );
        };

        return $this->manifoldComponentFactory !== null ?
            fn () => ($this->manifoldComponentFactory)($defaultFactory, $this) :
            $defaultFactory;
    }

    /**
     * @inheritDoc
     */
    public function getPackageFacadeFqcn(): string
    {
        return Turbo::class;
    }

    /**
     * @inheritDoc
     */
    public function getTappetListenUri(): string
    {
        return $this->tappetListenUri;
    }
}
