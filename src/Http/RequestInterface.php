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

namespace Nytris\Turbo\Http;

/**
 * Interface RequestInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface RequestInterface
{
    /**
     * Fetches the unique ID of the connection this request was made on.
     */
    public function getConnectionId(): int;

    /**
     * Fetches the globally-unique ID of this request and its connection.
     */
    public function getGlobalId(): string;

    /**
     * Fetches the unique ID of this request within its connection.
     */
    public function getRequestId(): int;
}
