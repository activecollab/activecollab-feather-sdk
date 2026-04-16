<?php

/*
 * This library is free software, and it is part of the Active Collab SDK project. Check LICENSE for details.
 *
 * (c) A51 doo <info@activecollab.com>
 */

namespace ActiveCollab\SDK\Authenticator;

use ActiveCollab\SDK\Authenticator;
use ActiveCollab\SDK\Exceptions\Authentication;
use ActiveCollab\SDK\Exceptions\ListAccounts;
use ActiveCollab\SDK\Exceptions\TwoFactorAuthRequired;
use ActiveCollab\SDK\ResponseInterface;
use InvalidArgumentException;

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
     * Return user information (first name, last name and avatar URL).
     *
     * @return array
     */
    public function getUser()
    {
        if (!$this->accounts_and_user_loaded) {
            $this->loadAccountsAndUser();
        }

        return $this->user;
    }

    public function issueToken(...$arguments)
    {
        if (empty($arguments[0]) || !is_int($arguments[0])) {
            throw new InvalidArgumentException('Account ID is required');
        }

        if (!$this->accounts_and_user_loaded) {
            $this->loadAccountsAndUser();
        }

        $account_id = (integer) $arguments[0];

        if (empty($this->accounts[$account_id])) {
            throw new InvalidArgumentException(
                sprintf(
                    "Account #%d not loaded",
                    $account_id,
                ),
            );
        }

        if (empty($this->accounts[$account_id]['intent'])) {
            throw new InvalidArgumentException(
                sprintf(
                    "No intent available for account #%d",
                    $account_id,
                ),
            );
        }

        $response = $this->getConnector()->post(
            sprintf(
                'https://app.activecollab.com/%d/api/v1/issue-token-intent',
                $account_id,
            ),
            null,
            [
                'client_vendor' => $this->getYourOrgName(),
                'client_name' => $this->getYourAppName(),
                'intent' => $this->accounts[$account_id]['intent'],
            ],
        );

        if ($response instanceof ResponseInterface && $response->isJson()) {
            return $this->issueTokenResponseToToken($response, $this->accounts[$account_id]['url']);
        }

        throw new Authentication('Invalid response');
    }

    /**
     * Complete two-factor authentication with the code received by the user.
     *
     * Call this after catching TwoFactorAuthRequired from getAccounts(), getUser(),
     * or any method that triggers loadAccountsAndUser().
     *
     * @param  string $intent_id  The intent ID from the TwoFactorAuthRequired exception
     * @param  string $code       The 2FA code (from email or authenticator app)
     * @return $this
     */
    public function completeTwoFactorAuth(string $intent_id, string $code): self
    {
        $response = $this->getConnector()->post(
            'https://activecollab.com/api/v1/external/login',
            null,
            [
                'intent_id' => $intent_id,
                'code' => $code,
            ]
        );

        if ($response instanceof ResponseInterface && $response->isJson()) {
            $result = $response->getJson();
            $this->parseLoginResponse($result);
        } else {
            throw new Authentication('Invalid response');
        }

        return $this;
    }

    /**
     * Load account and user details from Active Collab ID.
     */
    private function loadAccountsAndUser()
    {
        if (!$this->accounts_and_user_loaded) {
            $email_address = $this->getEmailAddress();
            $password = $this->getPassword();

            if (empty($email_address) || empty($password)) {
                throw new Authentication('Email address and password are required');
            }

            $response = $this->getConnector()->post('https://activecollab.com/api/v1/external/login', null, [
                'email' => $this->getEmailAddress(),
                'password' => $this->getPassword(),
            ]);

            if ($response instanceof ResponseInterface) {
                if ($response->isJson()) {
                    $result = $response->getJson();

                    if (!empty($result['intent_id']) && empty($result['is_ok'])) {
                        throw new TwoFactorAuthRequired($result['intent_id']);
                    }

                    $this->parseLoginResponse($result);
                } else {
                    throw new Authentication(
                        sprintf(
                            'Invalid response. JSON expected, got "%s", status code "%s"',
                            $response->getContentType(),
                            $response->getHttpCode()
                        )
                    );
                }
            } else {
                throw new Authentication('Invalid response');
            }
        }
    }

    /**
     * Parse login response and populate accounts and user.
     *
     * @param  array $result  Decoded JSON response from Shepherd login endpoint
     */
    private function parseLoginResponse(array $result): void
    {
        if (empty($result['is_ok'])) {
            throw new ListAccounts($result['message'] ?? null);
        }

        if (empty($result['user'])) {
            throw new ListAccounts('Invalid response');
        }

        $this->accounts = $this->all_accounts = [];

        if (!empty($result['accounts']) && is_array($result['accounts'])) {
            foreach ($result['accounts'] as $account) {
                $this->all_accounts[] = $account;

                if ($account['class'] == 'FeatherApplicationInstance' || $account['class'] == 'ActiveCollab\Shepherd\Model\Account\ActiveCollab\FeatherAccount') {
                    $account_id = (integer) $account['name'];

                    $this->accounts[$account_id] = [
                        'id' => (integer) $account['name'],
                        'name' => $account['display_name'],
                        'url' => $account['url'],
                        'intent' => $account['intent'] ?? null,
                    ];
                }
            }
        }

        $this->user = $result['user'];
        $this->accounts_and_user_loaded = true;
    }
}
