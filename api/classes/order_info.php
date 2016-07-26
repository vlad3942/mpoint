<?php
/**
 * The Info package contains various data classes holding information such as:
 * 	- Order specific details as received by the cart that is send when a transation is initialized.
 *
 * @author Rohit Malhotra
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Info
 * @subpackage OrderInfo
 * @version 1.10
 */

/* ==================== Order Information Exception Classes Start ==================== */
/**
 * Exception class for all Order Information exceptions
 */
class OrderInfoException extends mPointException { }
/* ==================== Order Information Exception Classes End ==================== */

/**
 * Data class for hold all data relevant for a Transaction
 *
 */
class OrderInfo
{
	/**
	 * Unique ID for the Order
	 *
	 * @var integer
	 */
	private $_iID;	
	/**
	 * Configuration for the Client who owns the Order
	 *
	 * @var TxnInfo
	 */
	private $_obj_TxnInfo;
	/**
	 * Configuration for the Country the Order was processed in
	 *
	 * @var CountryConfig
	 */
	private $_obj_CountryConfig;
	/**
	 * Total amount the customer will pay for the Order without fee
	 *
	 * @var long
	 */
	private $_lAmount;
	/**
	 * Total number of points the customer will pay for the Order
	 *
	 * @var integer
	 */
	private $_iPoints;
	/**
	 * Total number of points the customer will be rewarded for completing the Order
	 *
	 * @var integer
	 */
	private $_iReward;
	/**
	 * Total quantity of the products that was ordered.
	 *
	 * @var integer
	 */
	private $_iQuantity;
	/**
	 * The SKU of the product in the Order for a Customer
	 *
	 * @var string
	 */
	private  $_sProductSKU;
	/**
	 * The Name of the product in the Order for a Customer
	 *
	 * @var string
	 */
	private  $_sProductName;
	/**
	 * The Description of the product in the of a Order for a Customer
	 *
	 * @var string
	 */
	private  $_sProductDescription;
	/**
	 * The Image URL of the product in the Order for a Customer
	 *
	 * @var string
	 */
	private  $_sProductImageURL;
	
	
	/**
	 * Default Constructor
	 *
	 
	 *
	 */
	public function __construct($id, $tid, $cid, $amt, $pnt, $rwd, $qty, $productsku, $productname, $productdesc, $productimgurl)
	{		
		$this->_iID =  (integer) $id;
		$this->_iTransactionID = $tid;
		$this->_iCountryID = $cid;
		$this->_lAmount = (float) $amt;
		$this->_iPoints = (integer) $pnt;
		$this->_iReward = (integer) $rwd;
		$this->_iQuantity = (integer) $qty;
		$this->_sProductSKU = (string) $productsku;
		$this->_sProductName = (string) $productname;
		$this->_sProductDescription = (string) $productdesc;
		$this->_sProductImageURL = (string) $productimgurl;		
	}

	/**
	 * Returns the Unique ID for the Order
	 *
	 * @return 	integer
	 */
	public function getID() { return $this->_iID; }	
	/**
	 * Returns the Configuration for the Country the Order was processed in
	 *
	 * @return 	CountryConfig
	 */
	public function getCountryID() { return $this->_iCountryID; }
	/**
	 * Returns the Configuration for the Transaction in mPoint for the given Order
	 *
	 * @return 	TxnInfo
	 */
	public function getTxnInfo() { return $this->_obj_TxnInfo; }
	/**
	 * Returns the Total amount the customer will pay for the Order without fee
	 *
	 * @return 	long
	 */
	public function getAmount() { return $this->_lAmount; }	
	/**
	 * Returns the number of points the customer will pay for the Order
	 *
	 * @return 	integer
	 */
	public function getPoints() { return $this->_iPoints; }
	/**
	 * Returns the number of points the customer will be rewarded for completing the Order
	 *
	 * @return 	integer
	 */
	public function getReward() { return $this->_iReward; }
/**
	 * Returns the quantity of products that were ordered.
	 *
	 * @return 	integer
	 */
	public function getQuantity() { return $this->_iQuantity; }	
	/**
	 * Returns the SKU of the product in the Order for a Customer
	 *
	 * @return 	string
	 */
	public function getProductSKU() { return $this->_sProductSKU; }
	/**
	 * Returns the Name of the product in the Order for a Customer
	 *
	 * @return 	string
	 */
	public function getProductName() { return $this->_sProductName; }	
	/**
	 * Returns the Description of the product in the of a Order for a Customer
	 *
	 * @return 	string
	 */
	public function getProductDesc() { return $this->_sProductDescription; }	
	/**
	 * Returns the Image URL of the product in the Order for a Customer
	 *
	 * @return 	string
	 */
	public function getProductImageURL() { return $this->_sProductImageURL; }
	
		
	public static function produceConfig(RDB $oDB, $id)
	{
		$sql = "SELECT id, txnid, countryid, amount, productsku, productname, productdescription, productimageurl, points, reward, quantity 
				FROM Log". sSCHEMA_POSTFIX .".Order_Tbl				
				WHERE id = ". intval($id) ." AND enabled = '1'";
//		echo $sql ."\n";	
		$RS = $oDB->getName($sql);
		
		if (is_array($RS) === true && count($RS) > 0)
		{			
			return new OrderInfo($RS["ID"], $RS["TXNID"], $RS["COUNTRYID"], $RS["AMOUNT"], $RS["POINTS"], 
								 $RS["REWARD"], $RS["QUANTITY"], $RS["PRODUCTSKU"], $RS["PRODUCTNAME"], $RS["PRODUCTDESCRIPTION"], $RS["PRODUCTIMAGEURL"]);
		}
		else { return null; }
	}
	
	public static function produceConfigurations(RDB $oDB, $txnid)
	{		
		$sql = "SELECT id			
				FROM Log". sSCHEMA_POSTFIX .".Order_Tbl 				
				WHERE txnid = ". intval($txnid) ." AND enabled = '1'";
		//echo $sql ."\n";
		$aConfigurations = array();
		$res = $oDB->query($sql);
		while ($RS = $oDB->fetchName($res) )
		{
			$aConfigurations[] = self::produceConfig($oDB, $RS["ID"]);
		}		
		return $aConfigurations;		
	}
	
	public function toXML()
	{
		$xml = '';
		$xml .= '<line-item>';
        $xml .= '<product sku="'. $this->getProductSKU() .'">';
        $xml .= '<name>'. $this->getProductName() .'</name>';
        $xml .= '<description>'. $this->getProductDesc() .'</description>';
        $xml .= '<image-url>'. $this->getProductImageURL() .'</image-url>';
        $xml .= '</product>';
        $xml .= '<amount country-id="'. $this->getCountryID() .'">'. $this->getAmount() .'</amount>';
        $xml .= '<points>'. $this->getPoints() .'</points>';
        $xml .= '<reward>'. $this->getReward() .'</reward>';
        $xml .= '<quantity>'. $this->getQuantity() .'</quantity>';
        $xml .= '</line-item>';
        
        return $xml;
	}
}
?>