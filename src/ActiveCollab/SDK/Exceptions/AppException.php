<?php

  namespace ActiveCollab\SDK\Exceptions;

  use ActiveCollab\SDK\Exception;

  /**
   * Exception thrown by the app
   */
  class AppException extends Exception {

    // Codes
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const INVALID_PROPERTIES = 400;
    const CONFLICT = 409;
    const OPERATION_FAILED = 500;
    const UNAVAILABLE = 503;

    /**
     * Construct the new exception instance
     *
     * @param integer $http_code
     * @param string $server_response
     * @param float|null $request_time
     * @param string $message
     */
    function __construct($http_code, $server_response = null, $request_time = null, $message = null) {
      $this->http_code = $http_code;
      $this->request_time = $request_time;

      if($server_response && substr($server_response, 0, 1) === '{') {
        $this->server_response = json_decode($server_response, true);
      } else {
        $this->server_response = $server_response;
      } // if

      if($message === null) {
        switch($http_code) {
          case self::BAD_REQUEST:
            $message = 'Bad Request';
            break;
          case self::UNAUTHORIZED:
            $message = 'Unauthorized';
            break;
          case self::FORBIDDEN:
            $message = 'Forbidden';
            break;
          case self::NOT_FOUND:
            $message = 'Not Found';
            break;
          case self::INVALID_PROPERTIES:
            $message = 'Invalid Properties';
            break;
          case self::CONFLICT:
            $message = 'Conflict';
            break;
          case self::OPERATION_FAILED:
            $message = 'Operation failed';
            break;
          case self::UNAVAILABLE:
            $message = 'Unavailable';
            break;
          default:
            $message = 'Unknown HTTP error';
        } // switch

        if(is_array($this->server_response)) {
          $message .= '. Error (' . $this->server_response['type'] . '): ' . $this->server_response['message'];
        } else {
          $message .= '. Error: ' . $this->server_response;
        } // if
      } // if

      parent::__construct($message);
    } // __construct

    /**
     * @var integer
     */
    private $http_code;

    /**
     * Return response code
     *
     * @return integer
     */
    function getHttpCode() {
      return $this->http_code;
    } // getHttpCode

    /**
     * Remember server response
     *
     * @var array|string|null
     */
    private $server_response;

    /**
     * Return server response
     *
     * @return array|string|null
     */
    function getServerResponse() {
      return $this->server_response;
    } // getServerResponse

    /**
     * @var float|null
     */
    private $request_time;

    /**
     * Return total request time
     */
    function getRequestTime() {
      return $this->request_time;
    } // getRequestTime

  }