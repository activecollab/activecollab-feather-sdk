<?php

/*
 * This library is free software, and it is part of the Active Collab SDK project. Check LICENSE for details.
 *
 * (c) A51 doo <info@activecollab.com>
 */

namespace ActiveCollab\SDK\Exceptions;

class TwoFactorAuthRequired extends \RuntimeException
{
    private string $intent_id;

    public function __construct(string $intent_id, string $message = 'Two-factor authentication is required')
    {
        parent::__construct($message);
        $this->intent_id = $intent_id;
    }

    public function getIntentId(): string
    {
        return $this->intent_id;
    }
}
