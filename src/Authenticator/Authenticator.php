<?php

/*
 * This library is free software, and it is part of the Active Collab SDK project. Check LICENSE for details.
 *
 * (c) A51 doo <info@activecollab.com>
 */

namespace ActiveCollab\SDK\Authenticator;

use ActiveCollab\SDK\AuthenticatorInterface;
use InvalidArgumentException;

/**
 * @package ActiveCollab\SDK
 */
abstract class Authenticator implements AuthenticatorInterface
{
    /**
     * @param string $your_org_name
     * @param string $your_app_name
     * @param string $email_address
     * @param string $password
     */
    public function __construct($your_org_name, $your_app_name, $email_address, $password)
    {
        $this->setYourOrgName($your_org_name);
        $this->setYourAppName($your_app_name);
        $this->setEmailAddress($email_address);
        $this->setPassword($password);
    }

    private $your_org_name;

    /**
     * {@inheritdoc}
     */
    public function getYourOrgName()
    {
        return $this->your_org_name;
    }

    /**
     * {@inheritdoc}
     */
    public function &setYourOrgName($value)
    {
        $this->your_org_name = $value;

        return $this;
    }

    private $your_app_name;

    /**
     * {@inheritdoc}
     */
    public function getYourAppName()
    {
        return $this->your_app_name;
    }

    /**
     * {@inheritdoc}
     */
    public function &setYourAppName($value)
    {
        $this->your_app_name = $value;

        return $this;
    }

    /**
     * @var string
     */
    private $email_address;

    /**
     * {@inheritdoc}
     */
    public function getEmailAddress()
    {
        return $this->email_address;
    }

    /**
     * {@inheritdoc}
     */
    public function &setEmailAddress($value)
    {
        if ($value && filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->email_address = $value;
        } else {
            throw new InvalidArgumentException("Value '$value' is not a valid email address");
        }

        return $this;
    }

    /**
     * @var string
     */
    private $password;

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function &setPassword($value)
    {
        $this->password = $value;

        return $this;
    }
}
