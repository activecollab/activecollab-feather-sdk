<?php

namespace ActiveCollab\SDK;

/**
 * @package ActiveCollab\SDK
 */
interface ConnectorInterface
{
    /**
     * GET data.
     *
     * @param string     $url
     * @param array|null $headers
     *
     * @return Response
     */
    public function get($url, $headers = null);

    /**
     * POST data.
     *
     * @param string     $url
     * @param array|null $headers
     * @param array      $post_data
     * @param array      $files
     *
     * @return Response
     */
    public function post($url, $headers = null, $post_data = null, $files = null);

    /**
     * Send a PUT request.
     *
     * @param string     $url
     * @param array|null $headers
     * @param array      $put_data
     *
     * @return Response
     */
    public function put($url, $headers = null, $put_data = null);

    /**
     * Send a DELETE request.
     *
     * @param string     $url
     * @param array|null $headers
     * @param array      $delete_data
     *
     * @return Response
     */
    public function delete($url, $headers = null, $delete_data = null);
}