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

namespace Nytris\Turbo\Component\Rpc\Transport\Stream;

use React\Stream\ReadableStreamInterface;
use React\Stream\WritableStreamInterface;

/**
 * Interface StreamContextInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface StreamContextInterface
{
    /**
     * Fetches the input stream.
     */
    public function getInputStream(): ReadableStreamInterface;

    /**
     * Fetches the output stream.
     */
    public function getOutputStream(): WritableStreamInterface;

    /**
     * Loads new streams to use for input and output.
     */
    public function useStreams(
        ReadableStreamInterface $inputStream,
        WritableStreamInterface $outputStream
    ): void;
}
