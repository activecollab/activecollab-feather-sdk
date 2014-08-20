<?php

  namespace ActiveCollab\SDK;

  use ActiveCollab\SDK\Exceptions\FileNotReadable;
  use ActiveCollab\SDK\Exceptions\IssueTokenException;

  /**
   * activeCollab API client
   */
  final class Client
  {

    // API wrapper version
    const VERSION = '5.0.0';

    /**
     * Return user agent string
     *
     * @return string
     */
    static function getUserAgent()
    {
      return 'activeCollab API Wrapper; v' . self::VERSION;
    }

    // ---------------------------------------------------
    //  Info
    // ---------------------------------------------------

    /**
     * Cached info response
     *
     * @var bool
     */
    private static $info_response = false;

    /**
     * Return info
     *
     * @param string|bool $property
     * @return bool|null|string
     */
    static function info($property = false)
    {
      if (self::$info_response === false) {
        self::$info_response = self::get('info')->getJson();
      }

      if ($property) {
        return isset(self::$info_response[$property]) && self::$info_response[$property] ? self::$info_response[$property] : null;
      } else {
        return self::$info_response;
      }
    }

    // ---------------------------------------------------
    //  Make and process requests
    // ---------------------------------------------------

    /**
     * API URL
     *
     * @var string
     */
    static private $url;

    /**
     * Return API URL
     *
     * @return string
     */
    static function getUrl()
    {
      return self::$url;
    }

    /**
     * Set API URL
     *
     * @param string $value
     */
    static function setUrl($value)
    {
      self::$url = $value;
    }

    /**
     * API version
     *
     * @var int
     */
    static private $api_version = 1;

    /**
     * Return API version
     *
     * @return int
     */
    static function getApiVersion()
    {
      return self::$api_version;
    }

    /**
     * Set API version
     *
     * @param integer $version
     */
    static public function setApiVersion($version)
    {
      self::$api_version = (integer) $version;
    }

    /**
     * API key
     *
     * @var string
     */
    static private $key;

    /**
     * Return API key
     *
     * @return string
     */
    static function getKey()
    {
      return self::$key;
    }

    /**
     * Set API key
     *
     * @param string $value
     */
    static function setKey($value)
    {
      self::$key = $value;
    }

    /**
     * Connector instance
     *
     * @var \ActiveCollab\SDK\Connector
     */
    static private $connector;

    /**
     * Return connector instance
     *
     * @return Connector
     */
    static function &getConnector()
    {
      if (empty(self::$connector)) {
        self::$connector = new Connector();
      }

      return self::$connector;
    }

    /**
     * @param string $email_or_username
     * @param string $password
     * @param string $client_name
     * @param string $client_vendor
     * @param bool $read_only
     * @return string
     * @throws Exceptions\IssueTokenException
     */
    static function issueToken($email_or_username, $password, $client_name, $client_vendor, $read_only = false)
    {
      $response = self::getConnector()->post(self::prepareUrl('issue-token'), [], self::prepareParams([
        'username' => $email_or_username,
        'password' => $password,
        'client_name' => $client_name,
        'client_vendor' => $client_vendor,
        'read_only' => $read_only,
      ]));

      $error = 0;

      if ($response instanceof Response && $response->isJson()) {
        $json = $response->getJson();

        if ($json['is_error']) {
          $error = $json['error'];
        } else {
          return $json['token'];
        }
      }

      throw new IssueTokenException($error);
    }

    /**
     * Send a get request
     *
     * @param string $path
     * @return Response
     */
    static function get($path)
    {
      return self::getConnector()->get(self::prepareUrl($path), self::prepareHeaders());
    }

    /**
     * Send a POST request
     *
     * @param string $path
     * @param array|null $params
     * @param array|null $attachments
     * @return Response
     */
    static function post($path, $params = null, $attachments = null)
    {
      return self::getConnector()->post(self::prepareUrl($path), self::prepareHeaders(), self::prepareParams($params), self::prepareAttachments($attachments));
    }

    /**
     * Send a PUT request
     *
     * @param $path
     * @param array|null $params
     * @param array|null $attachments
     * @return Response
     */
    static function put($path, $params = null, $attachments = null)
    {
      return self::getConnector()->put(self::prepareUrl($path), self::prepareHeaders(), self::prepareParams($params), self::prepareAttachments($attachments));
    }

    /**
     * Send a delete command
     *
     * @param $path
     * @param array|null $params
     * @return Response
     */
    static function delete($path, $params = null)
    {
      return self::getConnector()->delete(self::prepareUrl($path), self::prepareHeaders(), self::prepareParams($params));
    }

    /**
     * Prepare headers
     *
     * @return array
     */
    static private function prepareHeaders()
    {
      return [ 'X-Angie-AuthApiToken: ' . self::getKey() ];
    }

    /**
     * Prepare URL from the given path
     *
     * @param string $path
     * @return string
     */
    static private function prepareUrl($path)
    {
      $bits = parse_url($path);

      $path = isset($bits['path']) && $bits['path'] ? $bits['path'] : '/';

      if (substr($path, 0, 1) !== '/') {
        $path = '/' . $path;
      }

      $query = isset($bits['query']) && $bits['query'] ? '?' . $bits['query'] : '';

      return self::getUrl() . '/api/v' . self::getApiVersion() . $path . $query;
    }

    /**
     * Prepare params
     *
     * @param array|null $params
     * @return array
     */
    static private function prepareParams($params)
    {
      return empty($params) ? [] : $params;
    }

    /**
     * Prepare attachments for request
     *
     * @param array|null $attachments
     * @return array|null
     * @throws Exceptions\FileNotReadable
     */
    static private function prepareAttachments($attachments = null)
    {
      $file_params = [];

      if ($attachments) {
        $counter = 1;

        foreach ($attachments as $attachment) {
          $path = is_array($attachment) ? $attachment[0] : $attachment;

          if (is_readable($path)) {
            $file_params['attachment_' . $counter++] = $attachment;
          } else {
            throw new FileNotReadable($attachment);
          }
        }
      }

      return $file_params;
    }

  }