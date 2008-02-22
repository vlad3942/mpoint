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
	 * Calculates the total Shipping Cost for the order based on the Shop's Configuration.
	 *
	 * @param 	integer $cost 	Total Cost of the Order
	 * @return 	integer
	 */
	public function calcShippingCost($cost)
	{	
		// Free Shipping due to Order Size
		if ($this->_obj_ShopConfig->getMinFreeShipping() > 0 && $cost > $this->_obj_ShopConfig->getMinFreeShipping() )
		{
			$iShippingCost = 0;
		}
		// Free Shipping due to Shop Config
		elseif ($this->_obj_ShopConfig->getMinFreeShipping() < 0) { $iShippingCost = 0; }
		// Shipping Cost
		else { $iShippingCost = $this->_obj_ShopConfig->getMinFreeShipping(); }
		
		return $iShippingCost;	
	}
	
	public function logShippingInfo($cost)
	{
		$aShippingInfo = array("company" => $this->_obj_ShopConfig->getShippingCompany(),
							   "price" => General::formatAmount($this->_obj_ShopConfig->getCountryConfig(), $cost) );
		$this->newMessage($this->_obj_TxnInfo->getID(), Constants::iSHIPPING_INFO_STATE, serialize($aShippingInfo) );
	}
}
?>