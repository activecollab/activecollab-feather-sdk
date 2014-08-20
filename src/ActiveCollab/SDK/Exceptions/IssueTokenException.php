<?php

  namespace ActiveCollab\SDK\Exceptions;

  use ActiveCollab\SDK\Exception;

  /**
   * Exception throw when token could not be issued
   */
  class IssueTokenException extends Exception
  {
    /**
     * @param string $code
     */
    function __construct($code)
    {
      switch ($code) {
        case 1:
          $message = 'Client details not set'; break;
        case 2:
          $message = 'Unknown user'; break;
        case 3:
          $message = 'Invalid Password'; break;
        case 4:
          $message = 'Not allowed for given User and their System Role'; break;
        default:
          $message = 'Unknown error';
      }

      parent::__construct($message);
    }

  }