<?php

  namespace ActiveCollab\SDK;

  use ActiveCollab\SDK\Response;
  use ActiveCollab\SDK\Exceptions\AppException;
  use ActiveCollab\SDK\Exceptions\CallFailed;


  /**
   * Abstract connector
   */
  class Connector {

    /**
     * GET data
     *
     * @param string $url
     * @param array|null $headers
     * @return Response
     */
    function get($url, $headers = null) {
      return $this->execute($http = $this->getHandle($url, $headers));
    } // get

    /**
     * POST data
     *
     * @param string $url
     * @param array|null $headers
     * @param array $post_data
     * @param array $files
     * @return Response
     */
    function post($url, $headers = null, $post_data = null, $files = null) {
      if($files) {
        if(empty($headers)) {
          $headers = [];
        }

        $headers[] = 'Content-type: multipart/form-data';
      }

      $http = $this->getHandle($url, $headers);

      if($files) {
        if(empty($post_data)) {
          $post_data = [];
        }

        $counter = 1;

        foreach($files as $file) {
          $post_data['attachment_' . $counter++] = '@' . $file . ';type=application/octet-stream';
        }

        curl_setopt($http, CURLOPT_POST, 1);
        curl_setopt($http, CURLOPT_POSTFIELDS, $post_data);
      } else {
        if($post_data) {
          curl_setopt($http, CURLOPT_POST, 1);
          curl_setopt($http, CURLOPT_POSTFIELDS, http_build_query($post_data));
        }
      }

      return $this->execute($http);
    } // post

    /**
     * Send a PUT request
     *
     * @param string $url
     * @param array|null $headers
     * @param array $put_data
     * @return Response
     */
    function put($url, $headers = null, $put_data = null) {
      $http = $this->getHandle($url, $headers);

      curl_setopt($http, CURLOPT_CUSTOMREQUEST, 'PUT');
      if($put_data) {
        curl_setopt($http, CURLOPT_POSTFIELDS, http_build_query($put_data));
      } // if

      return $this->execute($http);
    } // put

    /**
     * Send a DELETE request
     *
     * @param string $url
     * @param array|null $headers
     * @param array $delete_data
     * @return Response
     */
    function delete($url, $headers = null, $delete_data = null) {
      $http = $this->getHandle($url, $headers);

      curl_setopt($http, CURLOPT_CUSTOMREQUEST, 'DELETE');
      if($delete_data) {
        curl_setopt($http, CURLOPT_POSTFIELDS, http_build_query($delete_data));
      } // if

      return $this->execute($http);
    } // delete

    /**
     * Return curl resource
     *
     * @param string $url
     * @param array|null $headers
     * @return resource
     */
    private function &getHandle($url, $headers) {
      $http = curl_init();

      curl_setopt($http, CURLOPT_USERAGENT, Client::getUserAgent());
      curl_setopt($http, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($http, CURLINFO_HEADER_OUT, true);
      curl_setopt($http, CURLOPT_URL, $url);

      if(is_array($headers) && count($headers)) {
        curl_setopt($http, CURLOPT_HTTPHEADER, $headers);
      } // if

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
      $raw_response = curl_exec($http);

      if($raw_response === false) {
        $error_code = curl_errno($http);
        $error_message = curl_error($http);

        curl_close($http);

        throw new CallFailed($error_code, $raw_response, null, $error_message);
      } else {
        $response = new Response($http, $raw_response);

        curl_close($http);

        switch($response->getHttpCode()) {
          case 200:
            return $response;
          case 400:
          case 401:
          case 403:
          case 404:
          case 409:
          case 500:
          case 503:
            if(is_string($raw_response) && substr($raw_response, 0, 1) === '{') {
              throw new AppException($response->getHttpCode(), $raw_response, $response->getTotalTime()); // Known application exception
            } // if
        } // switch

        throw new CallFailed($response->getHttpCode(), $raw_response, $response->getTotalTime()); // Unknown exception
      } // if
    } // execute

  }