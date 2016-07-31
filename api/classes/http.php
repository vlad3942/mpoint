<?php

class HTTP
{
	const OK = 200;
	const NO_CONTENT = 204;
	const PARTIAL_CONTENT = 206;
	const NOT_MODIFIED = 304;
	const BAD_REQUEST = 400;
	const UNAUTHORIZED = 401;
	const FORBIDDEN = 403;
	const FILE_NOT_FOUND = 404;
	const METHOD_NOT_ALLOWED = 405;
	const UNSUPPORTED_MEDIA_TYPE = 415;
	const INTERNAL_SERVER_ERROR = 500;
	const NOT_IMPLEMENTED = 501;
	const BAD_GATEWAY = 502;

	protected static $responses =
		array(
			self::OK => 'HTTP/1.1 200 OK',
			self::NO_CONTENT => 'HTTP/1.1 204 No Content',
			self::PARTIAL_CONTENT => 'HTTP/1.1 206 Partial Content',
			self::NOT_MODIFIED => 'HTTP/1.1 304 Not Modified',
			self::BAD_REQUEST => 'HTTP/1.1 400 Bad Request',
			self::UNAUTHORIZED => 'HTTP/1.1 401 Unauthorized',
			self::FORBIDDEN => 'HTTP/1.1 403 Forbidden',
			self::FILE_NOT_FOUND => 'HTTP/1.1 404 File Not Found',
			self::METHOD_NOT_ALLOWED => 'HTTP/1.1 405 Method Not Allowed',
			self::UNSUPPORTED_MEDIA_TYPE => 'HTTP/1.1 415 Unsupported Media Type',
			self::INTERNAL_SERVER_ERROR => 'HTTP/1.1 500 Internal Server Error',
			self::NOT_IMPLEMENTED => 'HTTP/1.1 501 Not Implemented',
			self::BAD_GATEWAY => 'HTTP/1.1 502 Bad Gateway',
		);

	public static function getHTTPHeader($code) { return self::$responses[$code]; }
}