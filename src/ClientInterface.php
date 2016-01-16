<?php

/*
 * This library is free software, and it is part of the Active Collab SDK project. Check LICENSE for details.
 *
 * (c) A51 doo <info@activecollab.com>
 */

namespace ActiveCollab\SDK;

/**
 * @package ActiveCollab\SDK
 */
interface ClientInterface
{
    const VERSION = '2.0.0';

    /**
     * Return user agent string.
     *
     * @return string
     */
    public static function getUserAgent();

    /**
     * Return info.
     *
     * @param string|bool $property
     *
     * @return bool|null|string
     */
    public static function info($property = false);

    /**
     * Return API URL.
     *
     * @return string
     */
    public static function getUrl();

    /**
     * Set API URL.
     *
     * @param string $value
     */
    public static function setUrl($value);

    /**
     * Return API version.
     *
     * @return int
     */
    public static function getApiVersion();

    /**
     * Set API version.
     *
     * @param int $version
     */
    public static function setApiVersion($version);

    /**
     * Return API key.
     *
     * @return string
     */
    public static function getKey();

    /**
     * Set API key.
     *
     * @param string $value
     */
    public static function setKey($value);

    /**
     * Return connector instance.
     *
     * @return Connector
     */
    public static function &getConnector();

    /**
     * @param string $email_or_username
     * @param string $password
     * @param string $client_name
     * @param string $client_vendor
     * @param bool   $read_only
     *
     * @return string
     *
     * @throws Exceptions\IssueTokenException
     */
    public static function issueToken($email_or_username, $password, $client_name, $client_vendor, $read_only = false);

    /**
     * Send a get request.
     *
     * @param string $path
     *
     * @return Response
     */
    public static function get($path);

    /**
     * Send a POST request.
     *
     * @param string     $path
     * @param array|null $params
     * @param array|null $attachments
     *
     * @return Response
     */
    public static function post($path, $params = null, $attachments = null);

    /**
     * Send a PUT request.
     *
     * @param string     $path
     * @param array|null $params
     *
     * @return Response
     */
    public static function put($path, $params = null);

    /**
     * Send a delete command.
     *
     * @param string     $path
     * @param array|null $params
     *
     * @return Response
     */
    public static function delete($path, $params = null);
}
