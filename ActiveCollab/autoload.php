<?php

  /**
   * Include activeCollab API wrapper
   */

  spl_autoload_register(function($class) {
    $parts = explode('\\', $class);

    if(array_shift($parts) == 'ActiveCollab') {
      require_once __DIR__ . '/' . implode('/', $parts) . '.php';
    } //
  });