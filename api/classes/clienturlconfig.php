<?php 
class ClientURLConfig
{
	/**
	 * Client URL ID
	 *
	 * @var integer
	 */
	private $_iURLID;
	/**
	 * Client URL type ID
	 *
	 * @var integer
	 */
	private $_iTypeID;
	/**
	 * Client actual URL value
	 *
	 * @var string
	 */	
	private $_sURL;	
	
	/**
	 * Default Constructor
	 *
	 * @param 	integer $id 			For Merchant Sub Account.
	 * @param 	integer $typetid 		Parent account ID.
	 * @param 	integer $url 			ID of the PSP.	 	
	 */
	public function __construct($id, $typetid, $url)
	{
		$this->_iURLID = (integer) $id;
		$this->_iTypeID = (integer) $typetid;		
		$this->_sURL = (string)$url;		
	}
	/**
	 * Returns the URL ID.
	 *
	 * @return 	integer
	 */
	public function getID() { return $this->_iURLID; }
	/**
	 * Returns the URL type ID.
	 *
	 * @return 	integer
	 */
	public function getTypeID() { return $this->_iTypeID; }	
	/**
	 * Returns the actual client URL.
	 *
	 * @return 	PSPConfig
	 */
	public function getClientURL() { return $this->_sURL; }
	
	
	
	public function toXML()
	{		
		$xml = '';
		$xml .= '<url id = "' . intval($this->getID()) . '" type-id = "' . intval($this->getTypeID()) . '">';
		$xml .= htmlspecialchars($this->getClientURL(), ENT_NOQUOTES);
		$xml .= '</url>'; 
		
		return $xml;
	}
	
	public static function produceConfig($id, $typeid, $url)
	{		
		if($id > 0){
			return new ClientURLConfig($id, $typeid, $url);
		}
		else 
		{
			return null;
		}		
	}
	
}
?>