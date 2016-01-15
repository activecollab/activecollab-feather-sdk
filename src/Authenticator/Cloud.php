<?php

/*
 * This file is part of the Active Collab project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\SDK\Authenticator;

use ActiveCollab\SDK\Connector;
use ActiveCollab\SDK\Exceptions\IssueTokenException;
use ActiveCollab\SDK\Exceptions\ListAccounts;
use InvalidArgumentException;
use LogicException;

/**
 * @package ActiveCollab\SDK
 */
class Cloud extends Authenticator
{
    /**
     * @var bool
     */
    private $accounts_and_user_loaded = false;

    /**
     * @var array
     */
    private $accounts;

    /**
     * Return Feather (Active Collab and up) accounts.
     *
     * @return array
     */
    public function getAccounts()
    {
        if (!$this->accounts_and_user_loaded) {
            $this->loadAccountsAndUser();
        }

        return $this->accounts;
    }

    /**
     * @var array
     */
    private $all_accounts;

    /**
     * Return all accounts that this user is involved with.
     *
     * @return array
     */
    public function getAllAccounts()
    {
        if (!$this->accounts_and_user_loaded) {
            $this->loadAccountsAndUser();
        }

        return $this->all_accounts;
    }

    /**
     * @var array
     */
    private $user;

    /**
     * @return array
     */
    public function getUser()
    {
        if (!$this->accounts_and_user_loaded) {
            $this->loadAccountsAndUser();
        }

        return $this->user;
    }

    /**
     * @var string
     */
    private $intent;

    /**
     * @return string
     */
    private function getIntent()
    {
        if (!$this->accounts_and_user_loaded) {
            $this->loadAccountsAndUser();
        }

        return $this->intent;
    }

    /**
     * {@inheritdoc}
     */
    public function issueToken(...$arguments)
    {
        if (empty($arguments[0]) || !is_int($arguments[0])) {
            throw new InvalidArgumentException('Account ID is required');
        }

        $intent = $this->getIntent();

        $account_id = $arguments[0];

        if (empty($this->accounts[$account_id])) {
            throw new InvalidArgumentException("Account #{$account_id} not loaded");
        } else {
            $connector = new Connector();
            $response = $connector->post('https://my.activecollab.com/api/v1/external/login', null, [
                'client_vendor' => $this->getYourOrgName(),
                'client_name' => $this->getYourAppName(),
                'intent' => $intent,
            ]);

            if ($response->isJson()) {
                $result = $response->getJson();

                if (empty($result['is_ok']) || empty($result['token'])) {
                    throw new IssueTokenException(0);
                } else {
                    return new Token($this->accounts[$account_id]['url'], $result['token']);
                }
            } else {
                throw new IssueTokenException(0);
            }
        }
    }

    /**
     * Load account and user details from Active Collab ID.
     *
     * @throws IssueTokenException
     */
    private function loadAccountsAndUser()
    {
        if (!$this->accounts_and_user_loaded) {
            $email_address = $this->getEmailAddress();
            $password = $this->getPassword();

            if (empty($email_address) || empty($password)) {
                throw new IssueTokenException(0);
            }

            $connector = new Connector();
            $response = $connector->post('https://my.activecollab.com/api/v1/external/login', null, [
                'email' => $this->getEmailAddress(),
                'password' => $this->getPassword(),
            ]);

            if ($response->isJson()) {
                $result = $response->getJson();

                if (empty($result['is_ok'])) {
                    if (empty($result['message'])) {
                        throw new ListAccounts();
                    } else {
                        throw new ListAccounts($result['message']);
                    }
                } elseif (empty($result['user']) || empty($result['user']['intent'])) {
                    throw new ListAccounts('Invalid response');
                } else {
                    $this->accounts = $this->all_accounts = [];

                    if (!empty($result['accounts']) && is_array($result['accounts'])) {
                        foreach ($result['accounts'] as $account) {
                            $this->all_accounts[] = $account;

                            if ($account['class'] == 'FeatherApplicationInstance') {
                                $account_id = (integer) $account['name'];

                                $this->accounts[$account_id] = [
                                    'id' => (integer) $account['name'],
                                    'name' => $account['display_name'],
                                    'url' => $account['url'],
                                ];
                            } else {

                            }
                        }
                    }

                    $this->intent = $result['user']['intent'];
                    unset($result['user']['intent']);
                    $this->user = $result['user'];
                }
            } else {
                throw new IssueTokenException(0);
            }
        }
    }
}
