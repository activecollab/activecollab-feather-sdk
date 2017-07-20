<?php

/*
 * This library is free software, and it is part of the Active Collab SDK project. Check LICENSE for details.
 *
 * (c) A51 doo <info@activecollab.com>
 */

namespace ActiveCollab\SDK;

use ActiveCollab\SDK\Exceptions\AppException;
use ActiveCollab\SDK\Exceptions\CallFailed;

/**
 * Connector makes requests and returns API responses.
 */
class Connector implements ConnectorInterface
{
    /**
     * @var bool
     */
    private $ssl_verify_peer = true;
    
    /**
     * @var array()
     */
    private $response_headers;

    /**
     * {@inheritdoc}
     */
    public function getSslVerifyPeer()
    {
        return $this->ssl_verify_peer;
    }

    /**
     * {@inheritdoc}
     */
    public function &setSslVerifyPeer($value)
    {
        $this->ssl_verify_peer = (bool) $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserAgent()
    {
        return 'Active Collab API Wrapper; v' . Client::VERSION;
    }

    /**
     * {@inheritdoc}
     */
    public function get($url, $headers = null)
    {
        $http = $this->getHandle($url, $headers);

        return $this->execute($http);
    }

    /**
     * {@inheritdoc}
     */
    public function post($url, $headers = null, $post_data = null, $files = null)
    {
        if (empty($headers)) {
            $headers = [];
        }

        if ($files) {
            $headers[] = 'Content-type: multipart/form-data';
        } else {
            $headers[] = 'Content-type: application/json';
        }

        $http = $this->getHandle($url, $headers);

        curl_setopt($http, CURLOPT_POST, 1);

        if ($files) {
            if (empty($post_data)) {
                $post_data = [];
            }

            $counter = 1;

            foreach ($files as $file) {
                if (is_array($file)) {
                    list($path, $mime_type) = $file;
                } else {
                    $path = $file;
                    $mime_type = 'application/octet-stream';
                }

                $post_data['attachment_'.$counter++] = '@'.$path.';type='.$mime_type;
            }

            curl_setopt($http, CURLOPT_SAFE_UPLOAD, false); // PHP 5.6 compatibility for file uploads
            curl_setopt($http, CURLOPT_POST, 1);
            curl_setopt($http, CURLOPT_POSTFIELDS, $post_data);
        } else {
            if ($post_data) {
                curl_setopt($http, CURLOPT_POSTFIELDS, json_encode($post_data));
            } else {
                curl_setopt($http, CURLOPT_POSTFIELDS, '{}');
            }
        }

        return $this->execute($http);
    }

    /**
     * {@inheritdoc}
     */
    public function put($url, $headers = null, $put_data = null)
    {
        if (empty($headers)) {
            $headers = [];
        }

        $headers[] = 'Content-type: application/json';

        $http = $this->getHandle($url, $headers);

        curl_setopt($http, CURLOPT_CUSTOMREQUEST, 'PUT');

        if ($put_data) {
            curl_setopt($http, CURLOPT_POSTFIELDS, json_encode($put_data));
        } else {
            curl_setopt($http, CURLOPT_POSTFIELDS, '{}');
        }

        return $this->execute($http);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($url, $headers = null, $delete_data = null)
    {
        if (empty($headers)) {
            $headers = [];
        }

        $headers[] = 'Content-type: application/json';

        $http = $this->getHandle($url, $headers);

        curl_setopt($http, CURLOPT_CUSTOMREQUEST, 'DELETE');

        if ($delete_data) {
            curl_setopt($http, CURLOPT_POSTFIELDS, json_encode($delete_data));
        } else {
            curl_setopt($http, CURLOPT_POSTFIELDS, '{}');
        }

        return $this->execute($http);
    }

    /**
     * Return curl resource.
     *
     * @param string     $url
     * @param array|null $headers
     *
     * @return resource
     */
    private function &getHandle($url, $headers)
    {
        $http = curl_init();
        $headerero = array();

        curl_setopt($http, CURLOPT_USERAGENT, $this->getUserAgent());
        curl_setopt($http, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($http, CURLINFO_HEADER_OUT, true);
        curl_setopt($http, CURLOPT_URL, $url);

        if (!$this->getSslVerifyPeer()) {
            curl_setopt($http, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($http, CURLOPT_SSL_VERIFYHOST, false);
        }

        if (is_array($headers) && count($headers)) {
            curl_setopt($http, CURLOPT_HTTPHEADER, $headers);
        }
        
        curl_setopt($http, CURLOPT_HEADERFUNCTION, $this->getHeaderFunction());

        return $http;
    }

    /**
     * Returns function that captures the server's headers.
     *
     * @return function
     */
    private function getHeaderFunction(){
        $this->response_headers = array();

        return function($curl, $header) {
            $len = strlen($header);
            $header = explode(':', $header, 2);
            if (count($header) < 2) // ignore invalid headers
                return $len;

            $name = strtolower(trim($header[0]));
            if (!array_key_exists($name, $this->response_headers))
                $this->response_headers[$name] = [trim($header[1])];
            else
                $this->response_headers[$name][] = trim($header[1]);

            return $len;
        };
    }

    /**
     * Do the call.
     *
     * @param resource $http
     *
     * @return string
     *
     * @throws CallFailed
     * @throws AppException
     */
    private function execute(&$http)
    {
        $raw_response = curl_exec($http);

        if ($raw_response === false) {
            $error_code = curl_errno($http);
            $error_message = curl_error($http);

            curl_close($http);

            throw new CallFailed($error_code, $raw_response, null, $error_message);
        } else {
            $response = new Response($http, $raw_response, $this->response_headers);
            curl_close($http);

            return $response;
        }
    }
}
