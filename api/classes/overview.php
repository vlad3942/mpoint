<?php
/**
 * This file contains the business logic for the Order Overview component in mPoint's payment flow.
 * The component will generate a page using the transaction data, which lists the ordered products and their total price
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage Overview
 * @version 1.0
 */

/**
 * Model Class containing all the Business Logic for generating the order overview page
 *
 */
class Overview extends General
{
	/**
	 * Data object with the Transaction Information
	 *
	 * @var TxnInfo
	 */
	private $_obj_TxnInfo;
	
	/**
	 * Default Constructor.
	 *
	 * @param	RDB $oDB			Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param	TranslateText $oDB 	Text Translation Object for translating any text into a specific language
	 * @param 	TxnInfo $oTI 		Data object with the Transaction Information
	 */
	public function __construct(RDB &$oDB, TranslateText &$oTxt, TxnInfo &$oTI)
	{
		parent::__construct($oDB, $oTxt);
		
		$this->_obj_TxnInfo = $oTI;
		
		$this->newMessage($oTI->getID(), Constants::iACTIVATE_LINK_STATE, $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
	}
	
	/**
	 * Returns the Data object with the Transaction Information.
	 *
	 * @return TxnInfo
	 */
	public function &getTxnInfo() { return $this->_obj_TxnInfo; }
	
	/**
	 * Returns all products for the current Transaction.
	 * The products will be returned as an XML document in the following format:
	 * 	<products>
	 * 		<item>
	 * 			<name>{NAME OF THE PRODUCT}</name>
	 * 			<quantity>{NUMBER OF UNITS FOR THE PRODUCT}</quantity>
	 * 			<price>{TOTAL PRICE FOR ALL THE UNITS</price>
	 * 			<logo-url>{ABSOLUTE URL TO THE PRODUCT LOGO}</logo-url>
	 * 		</item>
	 * 		<item>
	 * 			<name>{NAME OF THE PRODUCT}</name>
	 * 			<quantity>{NUMBER OF UNITS FOR THE PRODUCT}</quantity>
	 * 			<price>{TOTAL PRICE FOR ALL THE UNITS</price>
	 * 			<logo-url>{ABSOLUTE URL TO THE PRODUCT LOGO}</logo-url>
	 * 		</item>
	 * 		...
	 * 	</products>
	 * 
	 * @see 	sDEFAULT_PRODUCT_LOGO
	 *
	 * @return 	string
	 */
	public function getProducts()
	{
		// Get Product Data
		$aProducts = $this->getMessageData($this->_obj_TxnInfo->getID(), Constants::iPRODUCTS_STATE);
		
		$xml = '<products>';
		foreach ($aProducts["names"] as $key => $name)
		{
			// Create XML for mandatory product information
			$xml .= '<item>';
			$xml .= '<name>'. htmlspecialchars($name, ENT_NOQUOTES) .'</name>';
			$xml .= '<quantity>'. $aProducts["quantities"][$key] .'</quantity>';
			$xml .= '<price>'. General::formatAmount($this->_obj_TxnInfo->getClientConfig()->getCountryConfig(), $aProducts["prices"][$key]) .'</price>';
			// Product logo included as part of request from Client
			if (array_key_exists($key, $aProducts["logos"]) === true && empty($aProducts["logos"][$key]) === false)
			{
				$xml .= '<logo-url>'. htmlspecialchars($aProducts["logos"][$key], ENT_NOQUOTES) .'</logo-url>';
			}
			else { $xml .= '<logo-url>'. sDEFAULT_PRODUCT_LOGO .'</logo-url>'; }
			
			$xml .= '</item>';
		}
		$xml .= '</products>';
		
		return $xml;
	}
}
?>