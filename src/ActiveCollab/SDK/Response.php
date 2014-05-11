<?php

  namespace ActiveCollab\SDK;

  /**
   * Abstract result
   */
  class Response {

    /**
     * Result of curl_getinfo() call
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
     * Raw response
     *
     * @var string|null
     */
    private $raw_response;

    /**
     * Construct a new response object
     *
     * @param resource $http
     * @param string|null $raw_response
     */
    function __construct(&$http, $raw_response) {
      $this->info = curl_getinfo($http);
      $this->raw_response = $raw_response;
    } // __construct

    /**
     * Return raw response body
     *
     * @return string
     */
    function getBody() {
      return $this->raw_response;
    } // getBody

    /**
     * Cached JSON data
     *
     * @var mixed
     */
    private $json_loaded = false, $json = null;

    /**
     * Return response body as JSON (when applicable)
     *
     * @return mixed
     */
    function getJson() {
      if(empty($this->json_loaded)) {
        if($this->getBody() && $this->getContentType() === 'application/json') {
          $this->json = json_decode($this->getBody(), true);
        } // if

        $this->json_loaded = true;
      } // if

      return $this->json;
    } // getJson

    /**
     * Return content type
     *
     * @return string
     */
    function getContentType() {
      return isset($this->info['content_type']) && $this->info['content_type'] ? $this->info['content_type'] : null;
    } // getContentType

    /**
     * Return HTTP code
     *
     * @return integer
     */
    function getHttpCode() {
      return isset($this->info['http_code']) && $this->info['http_code'] ? $this->info['http_code'] : null;
    } // getHttpCode

    /**
     * Make all info elements available via getElementName() magic methods
     *
     * @param string $name
     * @return mixed
     */
    function __call($name) {
      if(substr($name, 0, 3) == 'get') {
        $bit = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', substr($name, 3)));

        if(isset($this->info[$bit]) && $this->info[$bit]) {
          return $this->info[$bit];
        } // if
      } // if

      return null;
    } // __call

  }