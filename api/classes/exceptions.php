<?php
/* ==================== mPoint Exception Classes Start ==================== */
/**
 * Super class for all mPoint Exceptions
 */
class mPointException extends Exception { }
/**
 * Super class for all mPoint Exceptions
 */
abstract class mPointControllerException extends mPointException
{
	public function __construct($code, $message='', $previous=null)
	{
		parent::__construct($message, $code);
	}

	protected function statusElement($code, $message) { return '<status code="'. $code .'">'. $message .'</status>'; }

	public function getResponseXML() { return $this->statusElement($this->code, $this->message); }

	public abstract function getHTTPCode();
}

class mPointSimpleControllerException extends mPointControllerException
{
	private $_httpCode;

	public function __construct($httpCode, $code, $message='', $previous=null)
	{
		parent::__construct($code, $message);
		$this->_httpCode = $httpCode;
	}

	public function getHTTPCode() { return $this->_httpCode; }
}

class mPointSecurityException extends mPointControllerException
{
	const UNDEFINED_CLIENT_ID = 1;
	const INVALID_CLIENT_ID = 2;
	const UNKNOWN_CLIENT_ID = 3;
	const CLIENT_DISABLED = 4;
	const UNDEFINED_ACCOUNT = 11;
	const INVALID_ACCOUNT = 12;
	const UNKNOWN_ACCOUNT = 13;
	const ACCOUNT_DISABLED = 14;

	const UNAUTHORIZED = 401;
	const INVALID_CREDENTIALS = 402;
	const FORBIDDEN = 403;

	public function getResponseXML()
	{
		switch ($this->code)
		{
		case self::UNDEFINED_CLIENT_ID:
		case self::INVALID_CLIENT_ID:
		case self::UNKNOWN_CLIENT_ID:
		case self::CLIENT_DISABLED:
		case self::UNDEFINED_ACCOUNT:
		case self::INVALID_ACCOUNT:
		case self::UNKNOWN_ACCOUNT:
		case self::ACCOUNT_DISABLED:
			return $this->statusElement($this->code, "Client ID / Account doesn't match");
		case self::UNAUTHORIZED:
			return $this->statusElement(401, "Authorization required");
		case self::INVALID_CREDENTIALS:
			return $this->statusElement(401, "Username / Password doesn't match");
		case self::FORBIDDEN:
			return $this->statusElement(403, "Access Denied");
		default:
			return $this->statusElement(500, "Unexpected Error");
		}
	}

	public function getHTTPCode()
	{
		switch ($this->code)
		{
		case self::UNDEFINED_CLIENT_ID:
		case self::INVALID_CLIENT_ID:
		case self::UNKNOWN_CLIENT_ID:
		case self::CLIENT_DISABLED:
		case self::UNDEFINED_ACCOUNT:
		case self::INVALID_ACCOUNT:
		case self::UNKNOWN_ACCOUNT:
		case self::ACCOUNT_DISABLED:
			return HTTP::BAD_REQUEST;
		case self::UNAUTHORIZED:
		case self::INVALID_CREDENTIALS:
			return HTTP::UNAUTHORIZED;
		case self::FORBIDDEN:
			return HTTP::FORBIDDEN;
		default:
			return HTTP::INTERNAL_SERVER_ERROR;
		}
	}
}

class mPointBaseValidationException extends mPointControllerException
{
	const NOT_XML = 401;
	const INVALID_XML = 402;
	const WRONG_OPERATION = 403;

	/**
	 * @var SimpleDOMElement
	 */
	private $_rootChildren;

	public function __construct($code, SimpleXMLElement $rootChildren)
	{
		$this->_rootChildren = $rootChildren;
		parent::__construct($code, "Base validation error", null);
	}

	public function getResponseXML()
	{
		switch ($this->code)
		{
		case self::NOT_XML:
			return $this->statusElement(415, "Invalid XML Document");
		case self::INVALID_XML:
			return $this->_invalidXMLMessage();
		case self::WRONG_OPERATION:
			return $this->_wrongOperationMessage();
		default:
			return $this->statusElement(500, "Unexpected Error");
		}
	}

	public function getHTTPCode()
	{
		switch ($this->code)
		{
		case self::NOT_XML:
			return HTTP::UNSUPPORTED_MEDIA_TYPE;
		case self::INVALID_XML:
		case self::WRONG_OPERATION:
			return HTTP::BAD_REQUEST;
		default:
			return HTTP::INTERNAL_SERVER_ERROR;
		}
	}

	private function _invalidXMLMessage()
	{
		$aObj_Errs = libxml_get_errors();

		$msg = '';
		foreach ($aObj_Errs as $error)
		{
			$msg .= $this->statusElement(400, htmlspecialchars($error->message, ENT_NOQUOTES) );
		}

		return $msg;
	}

	private function _wrongOperationMessage()
	{
		$msg = '';
		foreach ($this->_rootChildren as $element)
		{
			$msg .= $this->statusElement(400, $element->getName() );
		}

		return $msg;
	}
}


class mPointCustomValidationException extends mPointControllerException
{
	/**
	 * @var array
	 */
	private $_aMsg;

	public function __construct(array $aMsg)
	{
		$this->_aMsg = $aMsg;
		parent::__construct(-1, "Custom validation error", null);
	}

	public function getResponseXML()
	{
		$msg = '';
		foreach ($this->_aMsg as $code => $data)
		{
			$msg .= $this->statusElement($code, htmlspecialchars($data, ENT_NOQUOTES) );
		}

		return $msg;
	}

	public function getHTTPCode() { return HTTP::BAD_REQUEST; }
}
/* ==================== mPoint Exception Classes End ==================== */