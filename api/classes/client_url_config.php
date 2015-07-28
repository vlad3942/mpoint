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
	 * Default Constructor
	 *
	 * @param 	integer $id 		The unique ID for the client's URL configuration
	 * @param 	integer $typeid 	The unique ID for the URL type
	 * @param 	integer $url 		The URL configured by client	 	
	 */
	public function __construct($id, $typeid, $url)
	{
		$this->_iID = (integer) $id;
		$this->_iTypeID = (integer) $typeid;		
		$this->_sURL = (string)$url;		
	}
	public function getID() { return $this->_iID; }
	public function getTypeID() { return $this->_iTypeID; }	
	public function getURL() { return $this->_sURL; }
	
	public function toXML()
	{		
		$xml = '<url id="'. intval($this->_iID) .'" type-id="'. intval($this->_iTypeID) .'">';
		$xml .= htmlspecialchars($this->_sURL, ENT_NOQUOTES);
		$xml .= '</url>'; 
		
		return $xml;
	}
}
?>