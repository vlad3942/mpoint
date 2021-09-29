<?php

namespace api\classes\merchantservices;

use phpDocumentor\Reflection\DocBlock\Tags\Var_;

class ResponseTemplate
{
    private int $_iHttpStatusCode = 200;
    private  $_response;
    public function __construct(){}

    /**
     * @return int
     */
    public function getHttpStatusCode(): int
    {
        return $this->_iHttpStatusCode;
    }

    /**
     * @param int $iHttpStatusCode
     */
    public function setHttpStatusCode(int $iHttpStatusCode): void
    {
        $this->_iHttpStatusCode = $iHttpStatusCode;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * @param mixed $response
     */
    public function setResponse($response): void
    {
        $this->_response = $response;
    }

    const OK = 200;
    const CREATED = 201;
    const NO_CONTENT = 204;
    const PARTIAL_CONTENT = 206;
    const MULTI_STATUS = 207;
    const SEE_OTHER = 303;
    const NOT_MODIFIED = 304;
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const FILE_NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const NOT_ACCEPTABLE = 406;
    const UNSUPPORTED_MEDIA_TYPE = 415;
    const INTERNAL_SERVER_ERROR = 500;
    const NOT_IMPLEMENTED = 501;
    const BAD_GATEWAY = 502;

    protected static $responses =
        array(
            self::OK => 'HTTP/1.1 200 OK',
            self::CREATED => 'HTTP/1.1 201 CREATED',
            self::MULTI_STATUS => 'HTTP/1.1 207 Multi-Status',
            self::NO_CONTENT => 'HTTP/1.1 204 No Content',
            self::PARTIAL_CONTENT => 'HTTP/1.1 206 Partial Content',
            self::SEE_OTHER => 'HTTP/1.1 303 See Other',
            self::NOT_MODIFIED => 'HTTP/1.1 304 Not Modified',
            self::BAD_REQUEST => 'HTTP/1.1 400 Bad Request',
            self::UNAUTHORIZED => 'HTTP/1.1 401 Unauthorized',
            self::FORBIDDEN => 'HTTP/1.1 403 Forbidden',
            self::FILE_NOT_FOUND => 'HTTP/1.1 404 File Not Found',
            self::METHOD_NOT_ALLOWED => 'HTTP/1.1 405 Method Not Allowed',
            self::NOT_ACCEPTABLE => 'HTTP/1.1 406 Not Acceptable',
            self::UNSUPPORTED_MEDIA_TYPE => 'HTTP/1.1 415 Unsupported Media Type',
            self::INTERNAL_SERVER_ERROR => 'HTTP/1.1 500 Internal Server Error',
            self::NOT_IMPLEMENTED => 'HTTP/1.1 501 Not Implemented',
            self::BAD_GATEWAY => 'HTTP/1.1 502 Bad Gateway',
        );

    public static function getHTTPHeader($code) { return self::$responses[$code]; }


}