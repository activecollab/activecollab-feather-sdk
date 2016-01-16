<?php

/*
 * This library is free software, and it is part of the Active Collab SDK project. Check LICENSE for details.
 *
 * (c) A51 doo <info@activecollab.com>
 */

namespace ActiveCollab\SDK\Exceptions;

use ActiveCollab\SDK\Exception;

/**
 * HTTP API call exception.
 */
class FileNotReadable extends Exception
{
    /**
     * Construct the new exception instance.
     *
     * @param string $path
     */
    public function __construct($path)
    {
        if (empty($message)) {
            $message = "File '$path' is not readable";
        }

        parent::__construct($message);
    }
}
