<?php

/*
 * This library is free software, and it is part of the Active Collab SDK project. Check LICENSE for details.
 *
 * (c) A51 doo <info@activecollab.com>
 */

namespace ActiveCollab\SDK\Authenticator;

use ActiveCollab\SDK\Authenticator;
use ActiveCollab\SDK\Exceptions\Authentication;
use ActiveCollab\SDK\ResponseInterface;
use InvalidArgumentException;

/**
 * @package ActiveCollab\SDK
 */
class SelfHosted extends Authenticator
{
    /**
     * @var string
     */
    private $self_hosted_url;

    /**
     * @var int
     */
    private $api_version = 1;

    /**
     * SelfHosted constructor.
     *
     * @param string   $your_org_name
     * @param string   $your_app_name
     * @param string   $email_address_or_username
     * @param string   $password
     * @param string   $self_hosted_url
     * @param int|null $api_version
     */
    public function __construct($your_org_name, $your_app_name, $email_address_or_username, $password, $self_hosted_url, $api_version = null)
    {
        parent::__construct($your_org_name, $your_app_name, $email_address_or_username, $password);

        if ($self_hosted_url && filter_var($self_hosted_url, FILTER_VALIDATE_URL)) {
            $this->self_hosted_url = rtrim($self_hosted_url, '/');
        } else {
            throw new InvalidArgumentException('Self hosted URL is not valid');
        }

        if ($api_version !== null) {
            if (is_int($api_version) && $api_version > 0) {
                $this->api_version = $api_version;
            } else {
                throw new InvalidArgumentException('API version is expected to be a number');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function issueToken(...$arguments)
    {
        $request_url = "{$this->self_hosted_url}/api/v{$this->api_version}/issue-token";

        $response = $this->getConnector()->post($request_url, [], [
            'username' => $this->getEmailAddress(),
            'password' => $this->getPassword(),
            'client_name' => $this->getYourAppName(),
            'client_vendor' => $this->getYourOrgName(),
        ]);

        if ($response instanceof ResponseInterface) {
            if ($response->isJson()) {
                return $this->issueTokenResponseToToken($response, $this->self_hosted_url);
            } else {
                throw new Authentication(
                    sprintf(
                        'Invalid response from "%s". JSON expected, got "%s", status code "%s"',
                        $request_url,
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
