<?php

  namespace ActiveCollab\SDK;

  /**
   * Abstract result
   */
  class Response {

    function __construct($http, $response) {

    } // __construct

    /**
     * Return raw response body
     *
     * @return string
     */
    function getBody() {

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

    } // getContentType

    /**
     * Return HTTP code
     *
     * @return integer
     */
    function getHttpCode() {

    } // getHttpCode

  }