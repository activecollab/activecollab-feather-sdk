<?php

/*
 * This file is part of the Active Collab project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\SDK\Exceptions;

use ActiveCollab\SDK\Exception;

/**
 * @package ActiveCollab\SDK\Exceptions
 */
class ListAccounts extends Exception
{
    /**
     * ListAccounts constructor.
     *
     * @param string          $message
     * @param \Exception|null $previous
     */
    public function __construct($message = null, $previous = null)
    {
        if (empty($message)) {
            $message = 'Failed to list user accounts';
        }

        parent::__construct($message, 0, $previous);
    }
}
