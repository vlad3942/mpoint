<?php 
class ClientURLConfig
{
	/**
	 * mPoint's unique ID for the client's URL configuration
	 *
	 * @var integer
	 */
	private $_iID;
	/**
	 * mPoint's unique ID for the URL type
	 *
	 * @var integer
	 */
	private $_iTypeID;
	/**
	 * The URL configured by client
	 *
	 * @var string
	 */	
	private $_sURL;
    /**
     * The URL Name
     *
     * @var string
     */
    private $_sName;

    /**
	 * Default Constructor
	 *
	 * @param 	integer $id 		The unique ID for the client's URL configuration
	 * @param 	integer $typeid 	The unique ID for the URL type
	 * @param 	integer $url 		The URL configured by client	 	
	 * @param 	string $name 		The URL configured by client
	 */
	public function __construct($id, $typeid, $url,$name='')
	{
		$this->_iID = (integer) $id;
		$this->_iTypeID = (integer) $typeid;		
		$this->_sURL = (string)$url;
		$this->_sName = (string)$name;
	}
	public function getID() { return $this->_iID; }
	public function getTypeID() { return $this->_iTypeID; }	
	public function getURL() { return $this->_sURL; }
	public function getName() { return $this->_sName; }

	/**
	 * Convenience method for constructing a HTTPConnInfo object based on a Client URL Configuration
	 *
	 * @param string $method 		HTTP Method to apply
	 * @param string $contentType 	HTTP Content Type to apply
	 * @param int $timeout			Connection timeout
	 * @return HTTPConnInfo
	 * @throws HTTPInvalidConnInfoException
	 */
	public function constConnInfo($method='POST', $contentType='text/xml', $timeout=120)
	{
		$urlParts = parse_url($this->_sURL);

		if (is_array($urlParts) === true)
		{
			$iDefaultPort = $urlParts["scheme"] == 'https' ? 443 : 80;
			$iPort = (integer) $urlParts["port"] > 0 ? (integer) $urlParts["port"] : $iDefaultPort;
			return new HTTPConnInfo($urlParts["scheme"], $urlParts["host"], $iPort, $timeout, @$urlParts["path"], $method, $contentType, @$urlParts["user"], @$urlParts["pass"]);
		}
		else { throw new HTTPInvalidConnInfoException("URL could not be parsed: ". $this->_sURL); }
	}

	public function toXML()
	{		
		$xml = '<url id="'. intval($this->_iID) .'" type-id="'. intval($this->_iTypeID) .'">';
		$xml .= htmlspecialchars($this->_sURL, ENT_NOQUOTES);
		$xml .= '</url>'; 
		
		return $xml;
	}

    public function toAttributeLessXML()
    {
        $xml = '<client_url>';
        $xml .= '<id>'.$this->_iID.'</id>';
        $xml .= '<name>'.$this->_sName.'</name>';
        $xml .= '<type_id>'.$this->_iTypeID.'</type_id>';
        $xml .= '<value>'.htmlspecialchars($this->_sURL, ENT_NOQUOTES).'</value>';
        $xml .= '</client_url>';
        return $xml;
    }

    public static function produceFromXML(SimpleXMLElement &$oXML) :ClientURLConfig
    {
        $iId =(int)$oXML->id;
        $iTypeId =(int)$oXML->type_id;
        $sValue =htmlspecialchars((string)$oXML->value, ENT_NOQUOTES);
        return new ClientURLConfig($iId,$iTypeId,$sValue);
    }
}
?>