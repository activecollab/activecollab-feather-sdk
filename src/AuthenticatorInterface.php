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
     * @return string
     */
    public function getYourOrgName();

    /**
     * @param  string $value
     * @return $this
     */
    public function &setYourOrgName($value);

    /**
     * @return string
     */
    public function getYourAppName();

    /**
     * @param  string $value
     * @return $this
     */
    public function &setYourAppName($value);

    /**
     * @return string
     */
    public function getEmailAddress();

    /**
     * @param  string $value
     * @return $this
     */
    public function &setEmailAddress($value);

    /**
     * @return string
     */
    public function getPassword();

    /**
     * @param  string $value
     * @return $this
     */
    public function &setPassword($value);

    /**
     * @param  mixed[] ...$arguments
     * @return mixed
     */
    public function issueToken(...$arguments);
}
