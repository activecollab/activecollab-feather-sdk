<?php

  /**
   * Include activeCollab API wrapper
   */

  spl_autoload_register(function($class) {
    $parts = explode('\\', $class);

    if(array_shift($parts) == 'ActiveCollab' && array_shift($parts) == 'SDK') {
      require_once __DIR__ . '/SDK/' . implode('/', $parts) . '.php';
    } //
  });