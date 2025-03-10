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

namespace Nytris\Turbo\Component\Manifold\Exhaust\Protocol\FastCgi;

use Lisachenko\Protocol\FCGI;
use Lisachenko\Protocol\FCGI\Record\EndRequest;
use Lisachenko\Protocol\FCGI\Record\Stdout;
use Nytris\Turbo\Component\Ignition\Exhaust\Rpc\Handler\ConnectionCommandHandler;
use Nytris\Turbo\Component\Manifold\Exhaust\Protocol\ProtocolResponseHandlerInterface;
use Nytris\Turbo\Component\Rpc\RpcInterface;

/**
 * Class FastCgiProtocolResponseHandler.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FastCgiProtocolResponseHandler implements ProtocolResponseHandlerInterface
{
    public function __construct(
        private readonly RpcInterface $ignitionRpc
    ) {
    }

    /**
     * @inheritDoc
     */
    public function appendResponseBodyChunk(int $connectionId, int $requestId, string $chunk): void
    {
        $record = new Stdout($chunk);
        $record->setRequestId($requestId);

        $this->ignitionRpc->call(ConnectionCommandHandler::class, 'reply', [
            $connectionId,
            (string) $record
        ]);
    }

    /**
     * @inheritDoc
     */
    public function endRequest(int $connectionId, int $requestId): void
    {
        $exitCode = 0; // TODO - PHP application exit code.

        $finalStdoutRecord = new Stdout('');
        $finalStdoutRecord->setRequestId($requestId);
        $endRequestRecord = new EndRequest(
            protocolStatus: FCGI::REQUEST_COMPLETE,
            appStatus: $exitCode
        );
        $endRequestRecord->setRequestId($requestId);

        $this->ignitionRpc->call(ConnectionCommandHandler::class, 'reply', [
            $connectionId,
            $finalStdoutRecord . $endRequestRecord
        ]);
    }
}
