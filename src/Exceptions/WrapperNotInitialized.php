<?php

namespace ActiveCollab\SDK\Exceptions;

use ActiveCollab\SDK\Exception;

/**
 * API not initialized exception.
 */
class WrapperNotInitialized extends Exception
{
    /**
     * Construct the new exception instance.
     *
     * @param string $message
     */
    public function __construct($message = null)
    {
        if (empty($message)) {
            $message = 'API wrapper is not initialized. Please set proper API URL and key';
        }

        parent::__construct($message);
    }
}
