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
	 * The Flight Configuration of the product in the Order for a Customer
	 *
	 * @var array
	 */
	private  $_FlightConfigs;
	/**
	 * The Passenger Configuration of the product in the Order for a Customer
	 *
	 * @var array
	 */
	private  $_PassengerConfigs;
	/**
	 * The Address Configuration of the Order for a Customer
	 *
	 * @var array
	 */
	private  $_AddressConfigs;

    /**
     * The Additional Data of the Order for a Customer
     *
     * @var array
     */
    private $_aAdditionalData;
    /**
     * The Fees of the Order for a Customer
     *
     * @var long
     */
    private $_iFees;
	/**
	 * Default Constructor
	 *

	 *
	 */
	public function __construct($id, $tid, $cid, $amt, $pnt, $rwd, $qty, $productsku, $productname, $productdesc, $productimgurl,$flightd,$passengerd,$addressd,$additionaldata,$fees)
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
		$this->_FlightConfigs =  (array) $flightd;
		$this->_PassengerConfigs =  (array) $passengerd;
		$this->_AddressConfigs = (array) $addressd;
        $this->_aAdditionalData = $additionaldata;
        $this->_iFees = (float) $fees;
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
	/**
	 * Returns the  Flight Configuration of the product in the Order for a Customer
	 *
	 * @return 	array
	 */
	public function getFlightConfigs() { return $this->_FlightConfigs; }
	/**
	 * Returns the  Passenger Configuration of the product in the Order for a Customer
	 *
	 * @return 	array
	 */
	public function getPassengerConfigs() { return $this->_PassengerConfigs; }
	/**
	 * Returns the Address Configuration of the Order a Customer
	 *
	 * @return 	array
	 */
	public function getAddressConfigs() { return $this->_AddressConfigs; }
	/**
	 * Returns the Fees the customer will pay for the Order
	 *
	 * @return 	long
	 */
	public function getFees() { return $this->_iFees; }
    /**
     * Returns the Additional Data of this flight
     *
     * @return array
     */
    public function getAdditionalData() {
        return $this->_aAdditionalData;
    }
		
	public static function produceConfig(RDB $oDB, $id)
	{
		$sql = "SELECT id, txnid, countryid, amount, productsku, productname, productdescription, productimageurl, points, reward, quantity,fees
				FROM Log". sSCHEMA_POSTFIX .".Order_Tbl				
				WHERE id = ". intval($id) ." AND enabled = '1'";
//		echo $sql ."\n";	
		$RS = $oDB->getName($sql);

		if (is_array($RS) === true && count($RS) > 0)
		{
            $sqlA = "SELECT name, value FROM log" . sSCHEMA_POSTFIX . ".additional_data_tbl WHERE type='Order' and externalid=" . $RS ["ID"];
            // echo $sqlA;
            $RSA = $oDB->getAllNames ( $sqlA );

			$order_type = "order";
			$flightdata = FlightInfo::produceConfigurations($oDB, $id);
			$passengerdata = PassengerInfo::produceConfigurations($oDB, $id);
			$addressdata = AddressInfo::produceConfigurations($oDB, $id, $order_type);
			return new OrderInfo($RS["ID"], $RS["TXNID"], $RS["COUNTRYID"], $RS["AMOUNT"], $RS["POINTS"],
								 $RS["REWARD"], $RS["QUANTITY"], $RS["PRODUCTSKU"], $RS["PRODUCTNAME"], $RS["PRODUCTDESCRIPTION"], $RS["PRODUCTIMAGEURL"], $flightdata, $passengerdata, $addressdata,$RSA, $RS["FEES"]);
		}
		else { return null; }
	}

    public static function produceConfigurationsFromOrderID(RDB $oDB, $orderid)
    {
        $sql = "SELECT OT.id			
				FROM Log". sSCHEMA_POSTFIX .".Order_Tbl AS OT
				INNER JOIN Log". sSCHEMA_POSTFIX .".Transaction_Tbl AS TT ON OT.txnid = TT.id 
				WHERE TT.orderid = '". $oDB->escStr($orderid) ."' AND OT.enabled = '1' AND TT.enabled = '1'";
        //echo $sql ."\n";
        $aConfigurations = array();
        $res = $oDB->query($sql);
        while ($RS = $oDB->fetchName($res) )
        {
            $aConfigurations[] = self::produceConfig($oDB, $RS["ID"]);
        }
        return $aConfigurations;
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
	
	public function toXML($ticketNumbers = null)
	{
		$xml = '';
		foreach ($this->getAddressConfigs() as $address_Obj)
		{
			if( ($address_Obj instanceof AddressInfo) === true )
			{
				 
				$xml .= $address_Obj->toXML();
				 
			}
		}
		$xml .= '<line-item>';
        $xml .= '<product sku="'. $this->getProductSKU() .'">';
        $xml .= '<name>'. $this->getProductName() .'</name>';
        $xml .= '<description>'. $this->getProductDesc() .'</description>';
        $xml .= '<image-url>'. $this->getProductImageURL() .'</image-url>';
        $xml .= '<airline-data>';
        foreach ($this->getFlightConfigs() as $flight_Obj)
        {
        	if( ($flight_Obj instanceof FlightInfo) === true )
        	{
        	    if($flight_Obj->getAdditionalData()) {
        	        $ticketNumber = null;
                    foreach ($flight_Obj->getAdditionalData() as $fAdditionalData) {
                        if( strtolower($fAdditionalData['NAME']) === 'ticketnumber')
                        {
                            $ticketNumber = $fAdditionalData['VALUE'];
                        }
                    }
                    if (array_key_exists($ticketNumber, $ticketNumbers))
                    {
                        $xml .= $flight_Obj->toXML();
                    }
                }
        	}
        }
        foreach ($this->getPassengerConfigs() as $passenger_Obj)
        {
        	if( ($passenger_Obj instanceof PassengerInfo) === true )
        	{
        			
        		$xml .= $passenger_Obj->toXML();
        			
        	}
        }
        $xml .= '</airline-data>';
        $xml .= '</product>';
        $xml .= '<amount country-id="'. $this->getCountryID() .'">'. $this->getAmount() .'</amount>';
        $xml .= '<fees>';
        $xml .= '<fee country-id="'. $this->getCountryID() .'">'. $this->getFees() .'</fee>';
        $xml .= '</fees>';
        $xml .= '<points>'. $this->getPoints() .'</points>';
        $xml .= '<reward>'. $this->getReward() .'</reward>';
        $xml .= '<quantity>'. $this->getQuantity() .'</quantity>';
        $additionalData = $this->getAdditionalData();
        if (empty($additionalData) === false ) {
            $xml .= '<additional-data>';
            foreach ($additionalData as $fAdditionalData) {
                $xml .= '<param name="' . $fAdditionalData ["NAME"] . '">' . $fAdditionalData ["VALUE"] . '</param>';
            }
            $xml .= '</additional-data>';
        }
        $xml .= '</line-item>';
        return $xml;
	}
}
?>