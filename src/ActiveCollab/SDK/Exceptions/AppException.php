<?php

  namespace ActiveCollab\Exceptions;

  use ActiveCollab\Exception;

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
     * @param integer $code
     * @param string $server_response
     * @param string $message
     */
    function __construct($code, $server_response = null, $message = null) {
      if($server_response && substr($server_response, 0, 1) === '{') {
        $this->server_response = json_decode($server_response, true);
      } else {
        $this->server_response = $server_response;
      } // if

      if($message === null) {
        switch($code) {
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

  }