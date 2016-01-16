<?php

/*
 * This library is free software, and it is part of the Active Collab SDK project. Check LICENSE for details.
 *
 * (c) A51 doo <info@activecollab.com>
 */

namespace ActiveCollab\SDK\Exceptions;

use ActiveCollab\SDK\Exception;

/**
 * HTTP API call exception.
 */
class CallFailed extends Exception
{
    /**
     * Error codes from API.
     *
     * @var array
     */
    private $http_codes = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
    ];

    /**
     * Construct the new exception instance.
     *
     * @param int        $http_code
     * @param string     $server_response
     * @param float|null $request_time
     * @param string     $message
     */
    public function __construct($http_code, $server_response = null, $request_time = null, $message = null)
    {
        $this->http_code = $http_code;

        if ($server_response && substr($server_response, 0, 1) === '{') {
            $this->server_response = json_decode($server_response, true);
        } else {
            $this->server_response = $server_response;
        }

        $this->request_time = $request_time;

        if (empty($message)) {
            if (isset($this->http_codes[$http_code])) {
                $message = 'HTTP error '.$http_code.': '.$this->http_codes[$http_code];
            } else {
                $message = 'Unknown HTTP error';
            }
        }

        parent::__construct($message);
    }

    /**
     * @var int
     */
    private $http_code;

    /**
     * Return response code.
     *
     * @return int
     */
    public function getHttpCode()
    {
        return $this->http_code;
    }

    /**
     * @var string
     */
    private $server_response;

    /**
     * Return server response.
     *
     * @return int
     */
    public function getServerResponse()
    {
        return $this->server_response;
    }

    /**
     * @var float|null
     */
    private $request_time;

    /**
     * Return total request time.
     */
    public function getRequestTime()
    {
        return $this->request_time;
    }
}
