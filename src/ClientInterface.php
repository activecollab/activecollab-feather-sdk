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
interface ClientInterface extends VerifySslPeerInterface
{
    const VERSION = '3.0.0';

    /**
     * Return info.
     *
     * @param string|bool $property
     *
     * @return bool|null|string
     */
    public function info($property = false);

    /**
     * Return API URL.
     *
     * @return string
     */
    public function getUrl();

    /**
     * Return API token.
     *
     * @return string
     */
    public function getToken();

    /**
     * Return API version.
     *
     * @return int
     */
    public function getApiVersion();

    /**
     * Return connector instance.
     *
     * @return Connector
     */
    public function &getConnector();

    /**
     * Send a get request.
     *
     * @param string $path
     *
     * @return Response
     */
    public function get($path);

    /**
     * Send a POST request.
     *
     * @param string     $path
     * @param array|null $params
     * @param array|null $attachments
     *
     * @return Response
     */
    public function post($path, $params = null, $attachments = null);

    /**
     * Send a PUT request.
     *
     * @param string     $path
     * @param array|null $params
     *
     * @return Response
     */
    public function put($path, $params = null);

    /**
     * Send a delete command.
     *
     * @param string     $path
     * @param array|null $params
     *
     * @return Response
     */
    public function delete($path, $params = null);
}
