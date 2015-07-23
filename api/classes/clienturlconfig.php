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
		$xml  = '';
		if(($this->getPSPConfig() instanceof PSPConfig) == true){
			$xml .= '<payment-service-provider id = "'.$this->getID().'" psp-id = "'.$this->getPSPConfig()->getID().'">';			
			$xml .= '<name>'. htmlspecialchars($this->getPSPConfig()->getName(), ENT_NOQUOTES) .'</name>';							
			$xml .= '</payment-service-provider>';				
		}
		
		return $xml;
	}
	
	public static function produceConfig($id, $typeid, $url)
	{		
		return new ClientURLConfig($id, $typeid, $url);		
	}
	
}
?>