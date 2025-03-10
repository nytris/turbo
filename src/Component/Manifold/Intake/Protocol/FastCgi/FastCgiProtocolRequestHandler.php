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

namespace Nytris\Turbo\Component\Manifold\Intake\Protocol\FastCgi;

use Lisachenko\Protocol\FCGI\FrameParser;
use Lisachenko\Protocol\FCGI\Record\BeginRequest;
use Lisachenko\Protocol\FCGI\Record\Params;
use Lisachenko\Protocol\FCGI\Record\Stdin;
use Nytris\Turbo\Component\Manifold\Intake\Protocol\ProtocolRequestHandlerInterface;
use Nytris\Turbo\Component\Manifold\Intake\Request\RequestHandlerInterface;
use RuntimeException;

/**
 * Class FastCgiProtocolRequestHandler.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FastCgiProtocolRequestHandler implements ProtocolRequestHandlerInterface
{
    /**
     * @var array<int, string>
     */
    private array $buffersByConnectionId = [];
    /**
     * @var array<int, FastCgiRequest>
     */
    private array $requestsByRequestId = [];

    public function __construct(
        private readonly RequestHandlerInterface $requestHandler
    ) {
    }

    /**
     * @inheritDoc
     */
    public function closeConnection(int $connectionId): void
    {
        unset($this->requestsByRequestId[$connectionId]);
        unset($this->buffersByConnectionId[$connectionId]);
    }

    /**
     * @inheritDoc
     */
    public function openConnection(int $connectionId): void
    {
        $this->buffersByConnectionId[$connectionId] = '';
    }

    /**
     * @inheritDoc
     */
    public function receive(int $connectionId, string $chunk): void
    {
        $this->buffersByConnectionId[$connectionId] .= $chunk;

        while (FrameParser::hasFrame($this->buffersByConnectionId[$connectionId])) {
            $record = FrameParser::parseFrame($this->buffersByConnectionId[$connectionId]);

            $requestId = $record->getRequestId();

            if ($record instanceof BeginRequest) {
                $this->requestsByRequestId[$requestId] = new FastCgiRequest($connectionId);
                $this->requestsByRequestId[$requestId]->begin(
                    $requestId,
                    $record->getRole(),
                    $record->getFlags()
                );
            } elseif ($record instanceof Params) {
                $this->requestsByRequestId[$requestId]->addParams($record->getValues());
            } elseif ($record instanceof Stdin) {
                $isLastParam = $record->getContentLength() === 0;

                $request = $this->requestsByRequestId[$requestId] ?? null;
                unset($this->requestsByRequestId[$requestId]);

                if ($request !== null) {
                    $this->requestHandler->beginRequest($request);
                }

                if ($isLastParam) {
                    $this->requestHandler->handleRequest($connectionId, $requestId);
                } else {
                    $this->requestHandler->appendRequestBodyChunk(
                        $connectionId,
                        $requestId,
                        $record->getContentData()
                    );
                }
            } else {
                throw new RuntimeException('Unsupported FastCGI record type: ' . $record->getType());
            }
        }
    }
}
