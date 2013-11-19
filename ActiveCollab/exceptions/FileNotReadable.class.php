<?php

  namespace ActiveCollab\Exceptions;

  use ActiveCollab\Exception;


  /**
   * HTTP API call exception
   */
  class FileNotReadable extends Exception {

    /**
     * Construct the new exception instance
     *
     * @param string $path
     */
    function __construct($path) {
      if(empty($message)) {
        $message = "File '$path' is not readable";
      } // if

      parent::__construct($message);
    } // __construct

  }