<?php
/**
 * This file contains the business logic for the List Product component in mPoint's shopping flow.
 * The component will generate a page using the transaction data, which lists all of the available products and allows the customer to
 * select the quantity to purchase for each product.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Shop
 * @subpackage Products
 * @version 1.0
 */

/**
 * Model Class containing all the Business Logic for generating the Product List page
 *
 */
class Products extends Overview
{
	/**
	 * Cached List of Product Data.
	 * This variable should NEVER be accessed directly, instead the private method: _getOrderedProducts() should be used.
	 *
	 * @var array
	 */
	private $_aProductCache;
	
	/**
	 * Returns all products available products for the Keyword.
	 * The products will be returned as an XML document in the following format:
	 * 	<products>
	 * 		<item id="{UNIQUE ID FOR THE PRODUCT}">
	 * 			<name>{NAME OF THE PRODUCT}</name>
	 * 			<amount currency="{CURRENCY AMOUNT IS CHARGED IN}" symbol="{SYMBOL USED TO REPRESENT THE CURRENCY}">{AMOUNT THE CUSTOMER IS CHARGED FOR THE PRODUCT}</amount>
	 * 			<price>{AMOUNT FORMATTED FOR BEING DISPLAYED IN THE GIVEN COUNTRY}</price>
	 * 			<logo-url>{ABSOLUTE URL TO THE PRODUCT LOGO}</logo-url>
	 * 		</item>
	 * 		<item id="{UNIQUE ID FOR THE PRODUCT}">
	 * 			<amount currency="{CURRENCY AMOUNT IS CHARGED IN}" symbol="{SYMBOL USED TO REPRESENT THE CURRENCY}">{AMOUNT THE CUSTOMER IS CHARGED FOR THE PRODUCT}</amount>
	 * 			<name>{NAME OF THE PRODUCT}</name>
	 * 			<price>{AMOUNT FORMATTED FOR BEING DISPLAYED IN THE GIVEN COUNTRY}</price>
	 * 			<logo-url>{ABSOLUTE URL TO THE PRODUCT LOGO}</logo-url>
	 * 		</item>
	 * 		...
	 * 	</products>
	 * 
	 * @see 	sDEFAULT_PRODUCT_LOGO
	 *
	 * @return 	string
	 */
	public function getAllProducts()
	{
		// Get Product Data
		$sql = "SELECT id, name, price, logourl
				FROM Client.Product_Tbl
				WHERE keywordid = ". $this->getTxnInfo()->getClientConfig()->getKeywordConfig()->getID() ." AND enabled = true
				ORDER BY name ASC";
//		echo $sql ."\n";
		$aRS = $this->getDBConn()->getAllNames($sql);
		
		$xml = '<products>';
		for ($i=0; $i<count($aRS); $i++)
		{
			// Create XML for mandatory product information
			$xml .= '<item id="'. $aRS[$i]["ID"] .'">';
			$xml .= '<name>'. htmlspecialchars($aRS[$i]["NAME"], ENT_NOQUOTES) .'</name>';
			$xml .= '<amount currency="'. $this->getTxnInfo()->getClientConfig()->getCountryConfig()->getCurrency() .'" symbol="'. $this->getTxnInfo()->getClientConfig()->getCountryConfig()->getSymbol() .'">'. $aRS[$i]["PRICE"] .'</amount>';
			$xml .= '<price>'. General::formatAmount($this->getTxnInfo()->getClientConfig()->getCountryConfig(), $aRS[$i]["PRICE"]) .'</price>';
			// Product logo included as part of request from Client
			if (empty($aRS[$i]["LOGOURL"]) === false)
			{
				$xml .= '<logo-url>'. htmlspecialchars($aRS[$i]["LOGOURL"], ENT_NOQUOTES) .'</logo-url>';
			}
			else { $xml .= '<logo-url>'. sDEFAULT_PRODUCT_LOGO .'</logo-url>'; }
			
			$xml .= '</item>';
		}
		$xml .= '</products>';
		
		return $xml;
	}
	
	/**
	 * Fetches data for all Ordered Products.
	 *
	 * @param 	array $aProds 	List of IDs for the Ordered Products
	 * @return 	array
	 */
	private function _getOrderedProducts(array &$aProdIDs)
	{
		// Cache not equivalent to the List of IDs for the Ordered Products 
		if (isset($this->_aProductCache) === false || count($this->_aProductCache) != count($aProdIDs) || count(array_diff($this->_aProductCache, $aProdIDs) ) > 0)
		{
			// Get Product Data
			$sql = "SELECT id, name, price, logourl
					FROM Client.Product_Tbl
					WHERE id IN (". implode(",", $aProdIDs) .")
					ORDER BY id ASC";
//			echo $sql ."\n";
			$res = $this->getDBConn()->query($sql);
			
			$this->_aProductCache = array();
			while ($RS = $this->getDBConn()->fetchName($res) )
			{
				$this->_aProductCache[$RS["ID"] ]["name"] = $RS["NAME"];
				$this->_aProductCache[$RS["ID"] ]["price"] = $RS["PRICE"];
				$this->_aProductCache[$RS["ID"] ]["logo-url"] = $RS["LOGOURL"];
			}
		}
		
		return $this->_aProductCache;
	}
	
	public function calcTotalOrder(array &$aProds)
	{
		$aProducts = $this->_getOrderedProducts(array_keys($aProds) );
		
		// Calculate total order cost
		$iTotal = 0;
		foreach ($aProducts as $id => $aProdInfo)
		{
			$iTotal += $aProdInfo["price"] * $aProds[$id];
		}
		
		return $iTotal;
	}
	
	/**
	 * Validate the Purchase of one or more Products.
	 * The method will return one of the following status codes:
	 * 	1. No Products selected for Purchase
	 * 	2. Quantity for all selected products are 0
	 * 	10. Success
	 *
	 * @param 	array $aProds 	List of Products that the customer has decided to Purchase
	 * @return 	integer
	 */
	public function valPurchase(array &$aProds)
	{
		if (count($aProds) == 0) { $code = 1; }
		else
		{
			$code = 2;
			while (list(, $quantity) = each($aProds) )
			{
				if (intval($quantity) > 0) { $code = 10; }
			}
		}
		
		return $code;
	}
	
	public function logProducts(array &$aProds)
	{
		$aProdInfo = $this->_getOrderedProducts(array_keys($aProds) );
		
		// Construct list of Products
		$aProducts = array("names" => array(),
						   "quantities" => array(),
						   "prices" => array(),
						   "logos" => array() );
		foreach ($aProdInfo as $id => $aInfo)
		{
			$aProducts["names"][$id] = $aInfo["name"];
			$aProducts["quantities"][$id] = $aProds[$id];
			$aProducts["prices"][$id] = $aInfo["price"] * $aProds[$id];
			$aProducts["logos"][$id] = $aInfo["logo-url"];
		}
		
		$this->newMessage($this->getTxnInfo()->getID(), Constants::iPRODUCTS_STATE, serialize($aProducts) );
	}
}
?>
