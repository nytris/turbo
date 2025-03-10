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

namespace Nytris\Turbo\Component\Rpc\Transport\Listener;

use Nytris\Turbo\Component\Rpc\Transport\Receiver\ReceiverInterface;

/**
 * Interface ListenerInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ListenerInterface
{
    /**
     * Starts listening for incoming messages.
     *
     * If the incoming message is a return value or error for an RPC call originating
     * from this end, then the promise for the call will be resolved/rejected accordingly.
     *
     * If the incoming message is for an RPC call originating from the remote end,
     * then the dispatcher will be used to dispatch the call to the appropriate handler.
     */
    public function listen(ReceiverInterface $receiver): void;

    /**
     * Stops listening for incoming messages.
     */
    public function stop(): void;
}
