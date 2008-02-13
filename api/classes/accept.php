<?php
/**
 * The Credit Card sub-package provides methods for retrieving credit card data
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage Accept
 * @version 1.0
 */

/**
 * 
 *
 */
class Accept extends General
{
	/**
	 * Data object with the User Agent Profile for the customer's mobile device.
	 *
	 * @var UAProfile
	 */
	private $_obj_UA;
	
	/**
	 * Default Constructor
	 *
	 * @param	RDB $oDB			Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param	TranslateText $oDB 	Reference to the Text Translation Object for translating any text into a specific language
	 * @param	UAProfile $oUA 		Reference to the data object with the User Agent Profile for the customer's mobile device
	 */
	public function __construct(RDB &$oDB, TranslateText &$oTxt, UAProfile &$oUA)
	{
		parent::__construct($oDB, $oTxt);
		
		$this->_obj_UA = $oUA;
	}
	
	/**
	 * Calculates the meta-data used for displaying mPoint's logo.
	 * The method will return the meta-data as an XML Document in the following format:
	 * 	<mpoint-logo>
	 *		<width>{CALCUALTED WITH FOR THE LOGO}/width>';
	 *		<height>{CALCUALTED HEIGHT FOR THE LOGO}</height>';
	 *	</mpoint-logo>';
	 * 
	 * @see 	iMPOINT_LOGO_SCALE
	 *
	 * @return 	string
	 */
	public function getmPointLogoInfo()
	{
		/* ========== Calculate Logo Dimensions Start ========== */
		$iWidth = $this->_obj_UA->getWidth() * iMPOINT_LOGO_SCALE / 100;
		$iHeight = $this->_obj_UA->getHeight() * iMPOINT_LOGO_SCALE / 100;
		
		if ($iWidth / 622 > $iHeight / 138) { $fScale = $iHeight / 138; }
		else { $fScale = $iWidth / 622; }
		
		$iWidth = intval($fScale * 622);
		$iHeight = intval($fScale * 138);
		/* ========== Calculate Logo Dimensions End ========== */
		
		// Construct XML with meta-data
		$xml = '<mpoint-logo>';
		$xml .= '<width>'. $iWidth .'</width>';
		$xml .= '<height>'. $iHeight .'</height>';
		$xml .= '</mpoint-logo>';
		
		return $xml;
	}
	
	/**
	 * Returns all custom Client Variables that the Client sent as part of the request.
	 * The custom Client Variables will be returned as an XML document in the following format:
	 * 	<client-vars>
	 * 		<item>
	 * 			<name>{NAME OF THE VARIABLE}</name>
	 * 			<value>{DATA CONTAINED IN THE VARIABLE}</value>
	 * 		</item>
	 * 		<item>
	 * 			<name>{NAME OF THE VARIABLE}</name>
	 * 			<value>{DATA CONTAINED IN THE VARIABLE}</value>
	 * 		</item>
	 * 		...
	 * 	</client-vars>
	 *
	 * @param 	integer $txnid 	Unique ID for the Transaction for which any custom Client Variables should be returned
	 * @return 	string
	 */
	public function getClientVars($txnid)
	{
		// Get custom Client Variables
		$aClientVars = $this->getMessageData($txnid, Constants::iCLIENT_VARS_STATE);
		
		$xml = '<client-vars>';
		foreach ($aClientVars as $name => $value)
		{
			// Create XML for custom Client Variables
			$xml .= '<item>';
			$xml .= '<name>'. htmlspecialchars($name, ENT_NOQUOTES) .'</name>';
			$xml .= '<value>'. htmlspecialchars(htmlspecialchars($value, ENT_NOQUOTES), ENT_NOQUOTES) .'</value>';
			
			$xml .= '</item>';
		}
		$xml .= '</client-vars>';
		
		return $xml;
	}
}
?>