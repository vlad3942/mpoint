<?php 
class ClientIINRangeConfig
{
	/**
	 * The unique ID of the IIN range.
	 *
	 * @var integer
	 */	
	private $_iID;
	/**
	 * The action ID for the IIN range
	 *
	 * @var integer
	 */	
	private $_iActionID;
	/**
	 * The Minimum value for the range
	 *
	 * @var integer
	 */	
	private $_iMin;
	/**
	 * The Maximum value for the range
	 *
	 * @var integer
	 */
	private $_iMax;		

	/**
	 * Default Constructor
	 *
	 * @param 	integer $id 		The unique ID client's IIN range.
	 * @param 	integer $actionid	The action ID for the IIN range
	 * @param 	integer $min	 	The Minimum value for the range
	 * @param 	integer $max		The Maximum value for the range	  	 	
	 */
	public function __construct($id, $actionid, $min, $max )
	{
		$this->_iID = (integer) $id;	
		$this->_iActionID = (integer) $actionid;
		$this->_iMax = (integer) $max;	
		$this->_iMin = (integer) $min;					
	}
	
	public function getActionID() { return $this->_iActionID; }	
	public function getMaxRangeValue() { return $this->_iMax; }	
	public function getMinRangeValue() { return $this->_iMin; }		
	
	public function toXML()
	{
		$xml = '<issuer-identification-number-range id="' . $this->_iID . '" action-id="' . $this->_iActionID . '">';		
		$xml .= '<max>'. $this->_iMax .'</max>';
		$xml .= '<min>'. $this->_iMax .'</min>';		 
		$xml .= '</issuer-identification-number-range>';

		return $xml;
	}
	
	public static function produceConfig(RDB $oDB, $id)
	{
		$sql = "SELECT id, actionid, minrange, maxrange		
				FROM Client". sSCHEMA_POSTFIX .".IINRange_Tbl				
				WHERE id = ". intval($id) ." AND enabled = '1'";
		//echo $sql .'\n';				
		$RS = $oDB->getName($sql);	
	
		if (is_array($RS) === true && count($RS) > 0)
		{		
			return new ClientIINRangeConfig($RS["ID"], $RS["ACTIONID"], $RS["MINRANGE"], $RS["MAXRANGE"] );
		}
		else { return null; }
	}
	
	public static function produceConfigurations(RDB $oDB, $clientid)
	{			
		$sql = "SELECT id
				FROM Client". sSCHEMA_POSTFIX .".IINRange_Tbl				
				WHERE clientid = ". intval($clientid) ." AND enabled = '1'";
		//echo $sql .'\n';			
		$aObj_Configurations = array();
		$res = $oDB->query($sql);
		while ($RS = $oDB->fetchName($res) )
		{
			$aObj_Configurations[] = self::produceConfig($oDB, $RS["ID"]);
		}
		
		return $aObj_Configurations;		
	}	
}
?>