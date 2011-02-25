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
	 * @see 	Constants::iPRODUCTS_STATE
	 * @see 	General::getMessageData()
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

	/**
	 * Returns the Shipping Information for the current Transaction.
	 * The Shipping Information will be returned as an XML document in the following format:
	 * 	<delivery-info>
	 * 		<name>{NAME OF THE RECIPIENT FOR THE ORDER}</name>
	 * 		<company>{COMPANY NAME OR C/O}</company>
	 * 		<street>{NAME OF THE STREET THE ORDER SHOULD BE DELIVERD TO}</street>
	 * 		<zipcode>{POSTAL CODE THE ORDER SHOULD BE DELIVERD TO}</zipcode>
	 * 		<city>{NAME OF THE CITY THE ORDER SHOULD BE DELIVERD IN}</city>
	 * 		<delivery-date>{DATE WHEN THE ORDER SHOULD BE DELIVERED}</delivery-date>
	 * 	</delivery-info>
	 * The delivery-date tag might be empyt, otherwise it contains a date in the format: YYYY-MM-DD
	 *
	 * @see 	Constants::iDELIVERY_INFO_STATE
	 * @see 	General::getMessageData()
	 *
	 * @return 	string
	 */
	public function getDeliveryInfo()
	{
		// Get Delivery Information
		$aDeliveryInfo = $this->getMessageData($this->_obj_TxnInfo->getID(), Constants::iDELIVERY_INFO_STATE);

		$xml = '<delivery-info>';
		// Create XML for Delivery Information
		if (count($aDeliveryInfo) > 0)
		{
			$xml .= '<name>'. htmlspecialchars($aDeliveryInfo["name"], ENT_NOQUOTES) .'</name>';
			$xml .= '<company>'. htmlspecialchars($aDeliveryInfo["company"], ENT_NOQUOTES) .'</company>';
			$xml .= '<street>'. htmlspecialchars($aDeliveryInfo["street"], ENT_NOQUOTES) .'</street>';
			$xml .= '<zipcode>'. htmlspecialchars($aDeliveryInfo["zipcode"], ENT_NOQUOTES) .'</zipcode>';
			$xml .= '<city>'. htmlspecialchars($aDeliveryInfo["city"], ENT_NOQUOTES) .'</city>';
			// Delivery Date part of the Delivery Information
			if (array_key_exists("delivery-date", $aDeliveryInfo) === true)
			{
				$xml .= '<delivery-date>'. htmlspecialchars($aDeliveryInfo["delivery-date"], ENT_NOQUOTES) .'</delivery-date>';
			}
			else { $xml .= '<delivery-date />'; }
		}
		$xml .= '</delivery-info>';

		return $xml;
	}

	/**
	 * Returns the Shipping Information for the current Transaction.
	 * The Shipping Information will be returned as an XML document in the following format:
	 * 	<shipping-info>
	 * 		<name>{NAME OF THE PRODUCT}</name>
	 * 		<price>{TOTAL PRICE FOR ALL THE UNITS</price>
	 * 	</shipping-info>
	 *
	 * @see 	Constants::iSHIPPING_INFO_STATE
	 * @see 	General::getMessageData()
	 *
	 * @return 	string
	 */
	public function getShippingInfo()
	{
		// Get Shipping Information
		$aShippingInfo = $this->getMessageData($this->_obj_TxnInfo->getID(), Constants::iSHIPPING_INFO_STATE);

		$xml = '<shipping-info>';
		// Create XML for Shipping Information
		if (count($aShippingInfo) > 0)
		{
			$xml .= '<name>'. htmlspecialchars($aShippingInfo["company"], ENT_NOQUOTES) .'</name>';
			$xml .= '<logo-url>'. htmlspecialchars($aShippingInfo["logo-url"], ENT_NOQUOTES) .'</logo-url>';
			$xml .= '<price>'. $aShippingInfo["price"] .'</price>';
		}
		$xml .= '</shipping-info>';

		return $xml;
	}
}
?>