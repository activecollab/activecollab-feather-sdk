<?php

/*
 * This file is part of the Active Collab project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\SDK;

/**
 * @package ActiveCollab\SDK
 */
interface AuthenticatorInterface
{
    /**
     * @param string $email_address
     * @param string $password
     * @param mixed[] ...$additional
     * @return mixed
     */
    public function issueToken($email_address, $password, ...$additional);
}
