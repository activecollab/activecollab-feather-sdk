<?php

/*
 * This library is free software, and it is part of the Active Collab SDK project. Check LICENSE for details.
 *
 * (c) A51 doo <info@activecollab.com>
 */

namespace ActiveCollab\SDK;

use ActiveCollab\SDK\Exceptions\FileNotReadable;
use ActiveCollab\SDK\Exceptions\IssueTokenException;
use InvalidArgumentException;

/**
 * activeCollab API client.
 */
class Client implements ClientInterface
{
    /**
     * @var TokenInterface
     */
    private $token;

    /**
     * API version.
     *
     * @var int
     */
    private $api_version = 1;

    /**
     * Client constructor.
     *
     * @param TokenInterface $token
     * @param int|null       $api_version
     */
    public function __construct(TokenInterface $token, $api_version = null)
    {
        $this->token = $token;

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
    public function getUserAgent()
    {
        return 'Active Collab API Wrapper; v' . self::VERSION;
    }

    // ---------------------------------------------------
    //  Info
    // ---------------------------------------------------

    /**
     * Cached info response.
     *
     * @var bool
     */
    private $info_response = false;

    /**
     * {@inheritdoc}
     */
    public function info($property = false)
    {
        if ($this->info_response === false) {
            $this->info_response = $this->get('info')->getJson();
        }

        if ($property) {
            return isset($this->info_response[$property]) && $this->info_response[$property] ? $this->info_response[$property] : null;
        } else {
            return $this->info_response;
        }
    }

    // ---------------------------------------------------
    //  Make and process requests
    // ---------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
        return $this->token->getToken();
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->token->getUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function getApiVersion()
    {
        return $this->api_version;
    }

    /**
     * Connector instance.
     *
     * @var \ActiveCollab\SDK\Connector
     */
    private $connector;

    /**
     * {@inheritdoc}
     */
    public function &getConnector()
    {
        if (empty($this->connector)) {
            $this->connector = new Connector();
        }

        return $this->connector;
    }

    /**
     * {@inheritdoc}
     */
    public function issueToken($email_or_username, $password, $client_name, $client_vendor, $read_only = false)
    {
        $response = $this->getConnector()->post($this->prepareUrl('issue-token'), [], $this->prepareParams([
            'username' => $email_or_username,
            'password' => $password,
            'client_name' => $client_name,
            'client_vendor' => $client_vendor,
            'read_only' => $read_only,
        ]));

        $error = 0;

        if ($response instanceof Response && $response->isJson()) {
            $json = $response->getJson();

            if (is_array($json) && !empty($json['is_ok']) && !empty($json['token'])) {
                return $json['token'];
            } else {
                if (empty($json['error'])) {
                    return 'Invalid response';
                } else {
                    return $json['error'];
                }
            }
        }

        throw new IssueTokenException($error);
    }

    /**
     * {@inheritdoc}
     */
    public function get($path)
    {
        return $this->getConnector()->get($this->prepareUrl($path), $this->prepareHeaders());
    }

    /**
     * {@inheritdoc}
     */
    public function post($path, $params = null, $attachments = null)
    {
        return $this->getConnector()->post($this->prepareUrl($path), $this->prepareHeaders(), $this->prepareParams($params), $this->prepareAttachments($attachments));
    }

    /**
     * {@inheritdoc}
     */
    public function put($path, $params = null)
    {
        return $this->getConnector()->put($this->prepareUrl($path), $this->prepareHeaders(), $this->prepareParams($params));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($path, $params = null)
    {
        return $this->getConnector()->delete($this->prepareUrl($path), $this->prepareHeaders(), $this->prepareParams($params));
    }

    /**
     * Prepare headers.
     *
     * @return array
     */
    private function prepareHeaders()
    {
        return ['X-Angie-AuthApiToken: ' . $this->getToken()];
    }

    /**
     * Prepare URL from the given path.
     *
     * @param string $path
     *
     * @return string
     */
    private function prepareUrl($path)
    {
        $bits = parse_url($path);

        $path = isset($bits['path']) && $bits['path'] ? $bits['path'] : '/';

        if (substr($path, 0, 1) !== '/') {
            $path = '/' . $path;
        }

        $query = isset($bits['query']) && $bits['query'] ? '?' . $bits['query'] : '';

        return $this->getUrl() . '/api/v' . $this->getApiVersion() . $path . $query;
    }

    /**
     * Prepare params.
     *
     * @param array|null $params
     *
     * @return array
     */
    private function prepareParams($params)
    {
        return empty($params) ? [] : $params;
    }

    /**
     * Prepare attachments for request.
     *
     * @param array|null $attachments
     *
     * @return array|null
     *
     * @throws Exceptions\FileNotReadable
     */
    private function prepareAttachments($attachments = null)
    {
        $file_params = [];

        if ($attachments) {
            $counter = 1;

            foreach ($attachments as $attachment) {
                $path = is_array($attachment) ? $attachment[0] : $attachment;

                if (is_readable($path)) {
                    $file_params['attachment_'.$counter++] = $attachment;
                } else {
                    throw new FileNotReadable($attachment);
                }
            }
        }

        return $file_params;
    }
}
