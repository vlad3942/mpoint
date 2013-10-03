<?php
/**
 * This file contains the business logic for the Shipping component in mPoint's shopping flow.
 * The component will generate a page using the Shop Configuration, which lists all of the available Shipping methods.
 * Additionally the component will calculate the total shipping fee based on the value of the customer's order.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Shop
 * @subpackage Shipping
 * @version 1.0
 */

/**
 * Model Class containing all the Business Logic for calculatin the Shipping Cost and generating the Shipping Info Page.
 *
 */
class Shipping extends General
{
	/**
	 * Data object with the Transaction Information
	 *
	 * @var TxnInfo
	 */
	private $_obj_TxnInfo;
	/**
	 * Data object holding the Shop Configuration
	 *
	 * @var ShopConfig
	 */
	private $_obj_ShopConfig;

	/**
	 * Default Constructor.
	 *
	 * @param	RDB $oDB			Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param	TranslateText $oDB 	Text Translation Object for translating any text into a specific language
	 * @param 	TxnInfo $oTI 			Data object with the Transaction Information
	 * @param 	ShopConfig $oSC 	Data object holding the Shop Configuration
	 */
	public function __construct(RDB &$oDB, TranslateText &$oTxt, TxnInfo &$oTI, ShopConfig &$oSC)
	{
		parent::__construct($oDB, $oTxt);

		$this->_obj_TxnInfo = $oTI;
		$this->_obj_ShopConfig = $oSC;
	}

	/**
	 * Logs the Shipping Information in the Database
	 *
	 * @param 	integer $id 	Unique ID for the selected Shipping Company
	 * @param 	integer $cost 	Total Cost of the Order
	 */
	public function logShippingInfo($id, $cost)
	{
		$sql = "SELECT name, logourl
				FROM System".sSCHEMA_POSTFIX.".Shipping_Tbl
				WHERE id = ". intval($id) ." AND enabled = '1'";
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);

		$aShippingInfo = array("company" => $RS["NAME"],
							   "logo-url" => $RS["LOGOURL"],
							   "price" => General::formatAmount($this->_obj_ShopConfig->getCountryConfig(), $cost) );
		$this->newMessage($this->_obj_TxnInfo->getID(), Constants::iSHIPPING_INFO_STATE, serialize($aShippingInfo) );
	}

	/**
	 * Fetches the available Shipping Companies for the Shop and returns them as an XML document in the following format:
	 * 	<shipping>
	 * 		<company id="{INTERNAL ID FOR THE SHIPPING COMPANY}">
	 * 			<name>{NAME OF THE SHIPPING COMPANY}</name>
	 * 			<logo-url>{ABSOLUTE URL TO THE SHIPPING COMPANY'S LOGO}</logo-url>
	 * 			<cost currency="{CURRENCY USED IN THE COUNTRY}">{TOTAL COST FOR USING THE SHIPPING COMPANY IN COUNTRY'S SMALLEST CURRENCY}</cost>
	 * 			<price>{TOTAL PRICE FOR USING THE SHIPPING COMPANY}</price>
	 * 		</company>
	 * 		<company id="{INTERNAL ID FOR THE SHIPPING COMPANY}">
	 * 			<name>{NAME OF THE SHIPPING COMPANY}</name>
	 * 			<logo-url>{ABSOLUTE URL TO THE SHIPPING COMPANY'S LOGO}</logo-url>
	 * 			<cost currency="{CURRENCY USED IN THE COUNTRY}" symbol="{SYMBOL USED TO REPRESENT THE CURRENCY}">{TOTAL COST FOR USING THE SHIPPING COMPANY IN COUNTRY'S SMALLEST CURRENCY}</cost>
	 * 			<price>{TOTAL PRICE FOR USING THE SHIPPING COMPANY}</price>
	 * 		</company>
	 * 		...
	 * 	</shipping>
	 *
	 * @param 	integer $cost 	Total Cost of the Order
	 * @return 	XML
	 */
	public function getShippingCompanies($cost)
	{
		$sql = "SELECT SS.id, SS.name, SS.logourl, CS.cost, CS.free_ship
				FROM Client".sSCHEMA_POSTFIX.".Shipping_Tbl CS
				INNER JOIN System".sSCHEMA_POSTFIX.".Shipping_Tbl SS ON CS.shippingid = SS.id AND SS.enabled = '1'
				WHERE CS.shopid = ". $this->_obj_ShopConfig->getID();
//		echo $sql ."\n";
		$aRS = $this->getDBConn()->getAllNames($sql);

		$xml = '<shipping>';
		for ($i=0; $i<count($aRS); $i++)
		{
			$xml .= '<company id="'. $aRS[$i]["ID"] .'">';
			$xml .= '<name>'. htmlspecialchars($aRS[$i]["NAME"]) .'</name>';
			$xml .= '<logo-url>'. htmlspecialchars($aRS[$i]["LOGOURL"]) .'</logo-url>';
			// Free Shipping
			if ($aRS[$i]["FREE_SHIP"] > 0 && $cost > $aRS[$i]["FREE_SHIP"])
			{
				$iShippingCost = 0;
			}
			// Shipping Cost
			else { $iShippingCost = $aRS[$i]["COST"]; }
			$xml .= '<cost currency="'. $this->_obj_ShopConfig->getCountryConfig()->getCurrency() .'" symbol="'. $this->_obj_ShopConfig->getCountryConfig()->getSymbol() .'">'. $iShippingCost .'</cost>';
			$xml .= '<price>'. ($iShippingCost==0?$this->getText()->_("FREE"):General::formatAmount($this->_obj_ShopConfig->getCountryConfig(), $aRS[$i]["COST"]) ) .'</price>';
			$xml .= '</company>';
		}
		$xml .= '</shipping>';

		return $xml;
	}
}
?>