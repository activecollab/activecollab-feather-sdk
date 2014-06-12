<?php

  namespace ActiveCollab\Connectors;

  use ActiveCollab\Client;
  use ActiveCollab\Connector;
  use ActiveCollab\Exceptions\CallFailed;

  /**
   * Snoopy connector
   *
   * @package ActiveCollab\Connectors
   */
  class Snoopy extends Connector {

    /**
     * Snoopy class path
     *
     * @var string
     */
    private $snoopy_class_path;

    /**
     * Set Snoopy class path
     *
     * @param $snoopy_class_path
     */
    function __constructor($snoopy_class_path) {
      if(is_file($snoopy_class_path) && is_readable($snoopy_class_path)) {
        $this->snoopy_class_path = $snoopy_class_path;
      } // if
    } // __constructor

    /**
     * GET data
     *readre
     * @param string $url
     * @return string
     */
    function get($url) {
      $snoopy = $this->getSnoopyInstance();
      $snoopy->fetch($url);
      return $this->processSnoopyResponse($snoopy);
    } // get

    /**
     * POST data
     *
     * @param string $url
     * @param array $post_data
     * @param array $files
     * @return mixed
     */
    function post($url, $post_data = null, $files = null) {
      $snoopy = $this->getSnoopyInstance();
      $snoopy->submit($url, $post_data, $files);
      return $this->processSnoopyResponse($snoopy);
    } // post

    /**
     * Process Snoopy response
     *
     * @param \Snoopy $snoopy
     * @return string
     * @throws \ActiveCollab\Exceptions\CallFailed
     */
    private function processSnoopyResponse(\Snoopy &$snoopy) {
      if($snoopy->status != 200) {
        throw new CallFailed($snoopy->status, $snoopy->results);
      } else {
        return $snoopy->results;
      } // if
    } // processSnoopyResponse

    /**
     * Return Snoopy instance
     *
     * @return \Snoopy
     */
    private function getSnoopyInstance() {
      require_once $this->snoopy_class_path;

      $snoopy = new \Snoopy();
      $snoopy->agent = Client::getUserAgent();

      return $snoopy;
    } // getSnoopyInstance

  }