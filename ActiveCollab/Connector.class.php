<?php

  namespace ActiveCollab;

  /**
   * Abstract connector
   */
  abstract class Connector {

    /**
     * GET data
     *
     * @param string $url
     * @return mixed
     */
    abstract function get($url);

    /**
     * POST data
     *
     * @param string $url
     * @param array $post_data
     * @param array $files
     * @return mixed
     */
    abstract function post($url, $post_data = null, $files = null);

  }