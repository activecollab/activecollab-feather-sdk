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
interface ConnectorInterface
{
    /**
     * GET data.
     *
     * @param string     $url
     * @param array|null $headers
     *
     * @return ResponseInterface
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
     * @return ResponseInterface
     */
    public function post($url, $headers = null, $post_data = null, $files = null);

    /**
     * Send a PUT request.
     *
     * @param string     $url
     * @param array|null $headers
     * @param array      $put_data
     *
     * @return ResponseInterface
     */
    public function put($url, $headers = null, $put_data = null);

    /**
     * Send a DELETE request.
     *
     * @param string     $url
     * @param array|null $headers
     * @param array      $delete_data
     *
     * @return ResponseInterface
     */
    public function delete($url, $headers = null, $delete_data = null);
}
