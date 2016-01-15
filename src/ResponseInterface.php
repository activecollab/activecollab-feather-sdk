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
interface ResponseInterface
{
    /**
     * Return raw response body.
     *
     * @return string
     */
    public function getBody();

    /**
     * Return true if response is JSON.
     *
     * @return bool
     */
    public function isJson();

    /**
     * Return response body as JSON (when applicable).
     *
     * @return mixed
     */
    public function getJson();

    /**
     * Return content type.
     *
     * @return string
     */
    public function getContentType();

    /**
     * Return HTTP code.
     *
     * @return int
     */
    public function getHttpCode();
}
