<?php

  namespace ActiveCollab\Exceptions;

  use ActiveCollab\Exception;

  /**
   * API not initialized exception
   */
  class WrapperNotInitialized extends Exception {

    /**
     * Construct the new exception instance
     *
     * @param string $message
     */
    function __construct($message = null) {
      if(empty($message)) {
        $message = 'API wrapper is not initialized. Please set proper API URL and key';
      } // if

      parent::__construct($message);
    } // __construct

  }