<?php

  namespace ActiveCollab\Connectors;

  use ActiveCollab\Client;
  use ActiveCollab\Connector;
  use ActiveCollab\Exceptions\AppException;
  use ActiveCollab\Exceptions\CallFailed;

  /**
   * Curl connector
   *
   * @package ActiveCollab\Connectors
   */
  class Curl extends Connector {

    /**
     * GET data
     *
     * @param string $url
     * @return string
     */
    function get($url) {
      $http = $this->getHandle($url);
      return $this->execute($http);
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
      $http = $this->getHandle($url);

      if($files) {
        curl_setopt($http, CURLOPT_HTTPHEADER, array('Content-type: multipart/form-data'));

        $counter = 1;

        foreach($files as $file) {
          $post_data['attachment_' . $counter++] = '@' . $file . ';type=application/octet-stream';
        } // foreach
      } // if

      curl_setopt($http, CURLOPT_POST, 1);
      curl_setopt($http, CURLOPT_POSTFIELDS, $post_data);

      return $this->execute($http);
    } // post

    /**
     * Return curl resource
     *
     * @param string $url
     * @return resource
     */
    private function &getHandle($url) {
      $http = curl_init();

      curl_setopt($http, CURLOPT_USERAGENT, Client::getUserAgent());
      curl_setopt($http, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($http, CURLOPT_URL, $url);

      return $http;
    } // getHandle

    /**
     * Do the call
     *
     * @param resource $http
     * @return string
     * @throws CallFailed
     * @throws AppException
     */
    private function execute(&$http) {
      $result = curl_exec($http);

      if($result === false) {
        $error_code = curl_errno($http);
        $error_message = curl_error($http);

        curl_close($http);

        throw new CallFailed($error_code, $result, $error_message);
      } else {
        $status = (integer) curl_getinfo($http, CURLINFO_HTTP_CODE);

        curl_close($http);

        switch($status) {
          case 200:
            return $result;
          case 400:
          case 401:
          case 403:
          case 404:
          case 409:
          case 500:
          case 503:
          if(is_string($result) && substr($result, 0, 1) === '{') {
            throw new AppException($status, $result); // Known application exception
          } // if
        } // switch

        throw new CallFailed($status, $result); // Unknown exception
      } // if
    } // execute

  }