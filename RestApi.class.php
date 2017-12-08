<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2015 Tyler Bucher
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Underline;

use Exception;

/**
 * Class RestApi handles all restful API requests.
 * @package Underline
 */
class RestApi {

    /**
     * @var string The HTTP method this request was made in, either GET, POST, PUT or DELETE
     */
    protected $method = '';

    /**
     * @var mixed|string The endpoint requested in the URI. eg: /files.
     */
    protected $endpoint = '';

    /**
     * @var array Any additional URI components after the index has filtered it (if it filters it).
     */
    protected $args = Array();

    /**
     * @var bool|null|string Stores the input of the PUT request.
     */
    protected $file = null;

    /**
     * Allow for CORS to assemble and pre-process the data.
     *
     * @param $request array the URI request.
     *
     * @throws Exception if there is an unexpected Header.
     */
    public function __construct(array $request) {
        //  What external servers can use this api.
        header('Access-Control-Allow-Origin: *');
        //  The methods that the server can use with this api.
        header('Access-Control-Allow-Methods: *');
        //  What type of content this server transmit.
        header('Content-Type: application/json');
        // Set vars
        $this->endpoint = array_shift($request);
        $this->args = $request;
        $this->method = $_SERVER['REQUEST_METHOD'];
        // Check For X HTTP request method
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                throw new Exception('Unexpected Header');
            }
        }
        // Set file input for PUT request
        if ($this->method == 'PUT') {
            $this->file = file_get_contents('php://input');
        }
    }

    /**
     * @param string $apiPath      The path to the api classes.
     * @param string $apiNamespace The namespace of the api classes.
     *
     * @return string The json encoded data.
     * @throws Exception if the endpoint file id not found.
     */
    public function processApi(string $apiPath = 'endpoints/', string $apiNamespace = ''): string {
        // Check to see if the api file exists before including it
        if (file_exists($apiPath . $this->endpoint . '.class.php')) {
            /** @noinspection PhpIncludeInspection */
            include_once $apiPath . $this->endpoint . '.class.php';
        } else {
            throw new Exception('Unable to find api file');
        }
        // Initialize the api object and class name
        $apiObject = null;
        $className = $apiNamespace . $this->endpoint;
        // Check to see if the api class exists inside the file
        if (class_exists($className)) {
            $apiObject = new $className($this->method, $this->args, $this->file);
        } else {
            return $this->_response("No Endpoint: $this->endpoint", 404);
        }
        // Check to see if the api implements the ControllerInterface
        if (!$apiObject instanceof ApiEndpoint) {
            return $this->_response("Endpoint configuration error: $this->endpoint", 500);
        }
        return $this->_response($apiObject->handle());
    }

    /**
     * @param mixed $data   The data to encode.
     * @param int   $status The status of the request.
     *
     * @return string The json encoded data.
     */
    private function _response($data, $status = 200): string {
        header('HTTP/1.1 ' . $status . ' ' . $this->_requestStatus($status));
        return json_encode($data);
    }

    /**
     * @param int $code HTTP response code.
     *
     * @return string The HTTP string response.
     */
    private function _requestStatus(int $code): string {
        $status = array(
            100 => 'Continue', 101 => 'Switching Protocols',
            200 => 'OK', 201 => 'Created',
            202 => 'Accepted', 203 => 'Non-Authoritative Information',
            204 => 'No Content', 205 => 'Reset Content',
            206 => 'Partial Content', 300 => 'Multiple Choices',
            301 => 'Moved Permanently', 302 => 'Moved Temporarily',
            303 => 'See Other', 304 => 'Not Modified',
            305 => 'Use Proxy', 400 => 'Bad Request',
            401 => 'Unauthorized', 402 => 'Payment Required',
            403 => 'Forbidden', 404 => 'Not Found',
            405 => 'Method Not Allowed', 406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required', 408 => 'Request Time-out',
            409 => 'Conflict', 410 => 'Gone',
            411 => 'Length Required', 412 => 'Precondition Failed',
            413 => 'Request Entity Too Large', 414 => 'Request-URI Too Large',
            415 => 'Unsupported Media Type', 500 => 'Internal Server Error',
            501 => 'Not Implemented', 502 => 'Bad Gateway',
            503 => 'Service Unavailable', 504 => 'Gateway Time-out',
            505 => 'HTTP Version not supported'
        );
        // Using array_key_exists to prevent E_NOTICE
        return isset($status[$code]) || array_key_exists($code, $status) ? $status[$code] ? $status[$code] : $status[500] : $status[500];
    }
}

/**
 * All RestApi endpoints should implement this interface.
 */
interface ApiEndpoint {

    /**
     * ApiEndpoint constructor.
     *
     * @param string $method The http method requested.
     * @param array  $args   The additional URI components if any.
     * @param string $file   The file for the put request.
     */
    public function __construct(string $method, array $args, string $file);

    /**
     * @return mixed API endpoint return call.
     */
    public function handle();
}
