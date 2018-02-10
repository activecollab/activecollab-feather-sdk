<?php

/*
 * This library is free software, and it is part of the Active Collab SDK project. Check LICENSE for details.
 *
 * (c) A51 doo <info@activecollab.com>
 */

namespace ActiveCollab\SDK;

use RuntimeException;

/**
 * Abstract result.
 */
class Response implements ResponseInterface
{
    /**
     * Result of curl_getinfo() call.
     *
     * Available elements:
     *
     * "url"
     * "content_type"
     * "http_code"
     * "header_size"
     * "request_size"
     * "filetime"
     * "ssl_verify_result"
     * "redirect_count"
     * "total_time"
     * "namelookup_time"
     * "connect_time"
     * "pretransfer_time"
     * "size_upload"
     * "size_download"
     * "speed_download"
     * "speed_upload"
     * "download_content_length"
     * "upload_content_length"
     * "starttransfer_time"
     * "redirect_time"
     * "certinfo"
     * "request_header"
     *
     * @var array
     */
    private $info;

    /**
     * Raw response.
     *
     * @var string|null
     */
    private $raw_response;

    /**
     * @var array
     */
    private $headers;

    /**
     * Construct a new response object.
     *
     * @param resource    $http
     * @param string|null $raw_response
     * @param array       $headers
     */
    public function __construct(&$http, $raw_response, $headers = [])
    {
        $this->info = curl_getinfo($http);
        $this->raw_response = $raw_response;
        $this->headers = !empty($headers) && is_array($headers) ? $headers : [];
    }

    /**
     * Return an array of response headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Return raw response body.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->raw_response;
    }

    /**
     * Flag whether we have a JSON response or not.
     *
     * @var mixed
     */
    private $is_json = null;

    /**
     * Flag whether JSON was parsed or not.
     *
     * @var bool
     */
    private $json_loaded = false;

    /**
     * Parsed JSON data.
     *
     * @var array|null
     */
    private $json = null;

    /**
     * Return true if response is JSON.
     *
     * @return bool
     */
    public function isJson()
    {
        if ($this->is_json === null) {
            $this->is_json = strpos($this->getContentType(), 'application/json') !== false;
        }

        return $this->is_json;
    }

    /**
     * Return response body as JSON (when applicable).
     *
     * @return mixed
     */
    public function getJson()
    {
        if (empty($this->json_loaded)) {
            if ($this->getBody() && $this->isJson()) {
                $this->json = json_decode($this->getBody(), true);

                if (json_last_error()) {
                    throw new RuntimeException('Failed to parse JSON. Reason: ' . json_last_error_msg());
                }
            }

            $this->json_loaded = true;
        }

        return $this->json;
    }

    /**
     * Return content type.
     *
     * @return string
     */
    public function getContentType()
    {
        return isset($this->info['content_type']) && $this->info['content_type'] ? $this->info['content_type'] : null;
    }

    /**
     * Return HTTP code.
     *
     * @return int
     */
    public function getHttpCode()
    {
        return isset($this->info['http_code']) && $this->info['http_code'] ? $this->info['http_code'] : null;
    }

    /**
     * Make all info elements available via getElementName() magic methods.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (substr($name, 0, 3) == 'get') {
            $bit = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', substr($name, 3)));

            if (isset($this->info[$bit]) && $this->info[$bit]) {
                return $this->info[$bit];
            }
        }

        return null;
    }
}
