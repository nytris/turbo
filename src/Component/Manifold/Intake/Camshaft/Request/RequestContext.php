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

namespace Nytris\Turbo\Component\Manifold\Intake\Camshaft\Request;

use LogicException;
use Nytris\Turbo\Http\RequestInterface;

/**
 * Class RequestContext.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RequestContext implements RequestContextInterface
{
    private ?RequestInterface $request = null;

    /**
     * @inheritDoc
     */
    public function beginRequest(RequestInterface $request): void
    {
        $this->request = $request;
    }

    /**
     * @inheritDoc
     */
    public function endRequest(): void
    {
        $this->request = null;
    }

    /**
     * @inheritDoc
     */
    public function getCurrentRequest(): RequestInterface
    {
        if ($this->request === null) {
            throw new LogicException('No request is currently being handled');
        }

        return $this->request;
    }
}
