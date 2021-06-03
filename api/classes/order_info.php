<?php
use api\classes\billingsummary\info\AddonInfo as BillingSummaryAddonInfo;
use api\classes\billingsummary\info\FareInfo as BillingSummaryFareInfo;

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
     * Unique reference string for the Order
     *
     * @var string
     */
    private $_sOrderRef;
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
     * The type of the product in the Order for a Customer
     *
     * @var integer
     */
    private $_iProductType;
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
     * The Billing Summary Fare Configuration of the Order for a Customer
     *
     * @var array
     */
    private  $_BillingSummaryFareConfigs;
    /**
     * The Billing Summary Add on Configuration of the Order for a Customer
     *
     * @var array
     */
    private  $_BillingSummaryAddonConfigs;

    /**
     * Default Constructor
     *
     * @param $id
     * @param $orderref
     * @param $tid
     * @param $cid
     * @param $amt
     * @param $pnt
     * @param $rwd
     * @param $qty
     * @param $productsku
     * @param $productname
     * @param $productdesc
     * @param $productimgurl
     * @param $flightd
     * @param $passengerd
     * @param $addressd
     * @param $additionaldata
     * @param $fees
     * @param $billingSummaryFared
     * @param $billingSummaryAddond
     */
	public function __construct($id, $orderref, $tid, $cid, $amt, $pnt, $rwd, $qty, $productsku, $productname, $productdesc, $productimgurl,$flightd,$passengerd,$addressd,$additionaldata,$fees, $billingSummaryFared, $billingSummaryAddond, $productType=100)
	{		
		$this->_iID =  (integer) $id;
		$this->_sOrderRef =  (string) $orderref;
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
        $this->_BillingSummaryFareConfigs =  (array) $billingSummaryFared;
        $this->_BillingSummaryAddonConfigs =  (array) $billingSummaryAddond;
        $this->_iProductType = (integer) $productType;
	}

	/**
	 * Returns the Unique ID for the Order
	 *
	 * @return 	integer
	 */
	public function getID() { return $this->_iID; }
	/**
	 * Returns the Unique reference string for the Order
	 *
	 * @return 	string
	 */
	public function getOrderRef() { return $this->_sOrderRef; }
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
	 * Returns the type of the product in the Order for a Customer
	 *
	 * @return 	integer
	 */
	public function getProductType() { return $this->_iProductType; }
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
    /**
     * Returns the  Billing Summary Fare Configuration of the Order for a Customer
     *
     * @return 	array
     */
    public function getBillingSummaryFareConfigs() { return $this->_BillingSummaryFareConfigs; }
    /**
     * Returns the Billing Summary Addon Configuration of the Order a Customer
     *
     * @return 	array
     */
    public function getBillingSummaryAddonConfigs() { return $this->_BillingSummaryAddonConfigs; }
		
	public static function produceConfig(RDB $oDB, $id, $amount)
	{
		$sql = "SELECT id, orderref, txnid, countryid, amount, productsku, productname, productdescription, productimageurl, points, reward, quantity,fees, type
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
            $billingSummaryFared = BillingSummaryFareInfo::produceConfigurations($oDB, $id);
            $billingSummaryAddond = BillingSummaryAddonInfo::produceConfigurations($oDB, $id);
			$orderAmount = $RS['AMOUNT'];
			if($amount > -1)
            {
                $orderAmount=$amount;
            }

			return new OrderInfo($RS["ID"], $RS['ORDERREF'],$RS["TXNID"], $RS["COUNTRYID"], $orderAmount, $RS["POINTS"],
								 $RS["REWARD"], $RS["QUANTITY"], $RS["PRODUCTSKU"], $RS["PRODUCTNAME"], $RS["PRODUCTDESCRIPTION"], $RS["PRODUCTIMAGEURL"], $flightdata, $passengerdata, $addressdata,$RSA, $RS["FEES"], $billingSummaryFared, $billingSummaryAddond, $RS["TYPE"]);
		}
		else { return null; }
	}

    public static function produceConfigurationsFromOrderID(RDB $oDB,TxnInfo $obj_TxnInfo)
    {
        $orderId = $obj_TxnInfo->getOrderID();
        $sql = "SELECT OT.id, OT.amount
				FROM Log". sSCHEMA_POSTFIX .".Order_Tbl AS OT
				INNER JOIN Log". sSCHEMA_POSTFIX .".Transaction_Tbl AS TT ON OT.txnid = TT.id 
				WHERE TT.clientid = ".$obj_TxnInfo->getClientConfig()->getID()." AND TT.orderid = '". $oDB->escStr($orderId) ."' AND OT.enabled = '1' AND TT.enabled = '1'";
        //echo $sql ."\n";
        $aConfigurations = array();
        $res = $oDB->query($sql);
        while ($RS = $oDB->fetchName($res) )
        {
            $aConfigurations[] = self::produceConfig($oDB, $RS["ID"], $RS["AMOUNT"]);
        }
        return $aConfigurations;
    }
	
	public static function produceConfigurations(RDB $oDB, $txnid, $ticketNumbers=array())
	{
	    if(is_array($ticketNumbers)){
            $ticketNumbersCount = count($ticketNumbers ) ;
        }
	    $sql = "SELECT id, 	orderref		
				FROM Log". sSCHEMA_POSTFIX .".Order_Tbl 				
				WHERE txnid = ". intval($txnid) ." AND enabled = '1'";
        if($ticketNumbersCount > 0) {
            $sql .= " AND orderref in   ('".implode("','", array_keys($ticketNumbers))."')";
        }
		//echo $sql ."\n";
		$aConfigurations = array();
		$res = $oDB->query($sql);
		while ($RS = $oDB->fetchName($res) )
		{
		    $amount = -1;
		    if($ticketNumbersCount > 0)
            {
               $amount = $ticketNumbers[$RS["ORDERREF"]];
            }
			$aConfigurations[] = self::produceConfig($oDB, $RS["ID"], $amount);
		}
		return $aConfigurations;		
	}
	
	public function toXML()
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
        $xml .= '<product order-ref = "'.$this->getOrderRef().'" sku="'. $this->getProductSKU() .'">';
        $xml .= '<type>'. $this->getProductType() .'</type>';
        $xml .= '<name>'. $this->getProductName() .'</name>';
        $xml .= '<description>'. $this->getProductDesc() .'</description>';
        if($this->getProductImageURL() != '') {
            $xml .= '<image-url>' . $this->getProductImageURL() . '</image-url>';
        }
        if(count($this->getFlightConfigs()) > 0 ) {
            $xml .= '<airline-data>';
            if ($GLOBALS['oldOrderXml'] === true) {
                foreach ($this->getFlightConfigs() as $flight_Obj) {
                    if (($flight_Obj instanceof FlightInfo) === TRUE) {
                        $xml .= $flight_Obj->toXML();
                    }
                }
                foreach ($this->getPassengerConfigs() as $passenger_Obj) {
                    if (($passenger_Obj instanceof PassengerInfo) === TRUE) {

                        $xml .= $passenger_Obj->toXML();

                    }
                }
            } else {
                if (count($this->getPassengerConfigs()) > 0) {
                    $xml .= '<profiles>';
                    foreach ($this->getPassengerConfigs() as $passenger_Obj) {
                        if (($passenger_Obj instanceof PassengerInfo) === TRUE) {

                            $xml .= $passenger_Obj->toXML();

                        }
                    }
                    $xml .= '</profiles>';
                }

                if (count($this->getBillingSummaryFareConfigs()) > 0 || count($this->getBillingSummaryAddonConfigs()) > 0) {
                    $xml .= '<billing-summary>';
                    if (count($this->getBillingSummaryFareConfigs()) > 0) {
                        $xml .= '<fare-detail>';
                        foreach ($this->getBillingSummaryFareConfigs() as $billSummaryFare_Obj) {
                            if (($billSummaryFare_Obj instanceof BillingSummaryFareInfo) === TRUE) {
                                $xml .= $billSummaryFare_Obj->toXML();
                            }
                        }
                        $xml .= '</fare-detail>';
                    }

                    if (count($this->getBillingSummaryAddonConfigs()) > 0) {
                        $xml .= '<add-ons>';
                        foreach ($this->getBillingSummaryAddonConfigs() as $billSummaryAddon_Obj) {
                            if (($billSummaryAddon_Obj instanceof BillingSummaryAddonInfo) === TRUE) {
                                $xml .= $billSummaryAddon_Obj->toXML();
                            }
                        }
                        $xml .= '</add-ons>';
                    }

                    $xml .= '</billing-summary>';
                }

                if (count($this->getFlightConfigs()) > 0) {
                    $xml .= '<trips>';
                    foreach ($this->getFlightConfigs() as $flight_Obj) {
                        if (($flight_Obj instanceof FlightInfo) === TRUE) {
                            $xml .= $flight_Obj->toXML();
                        }
                    }
                    $xml .= '</trips>';
                }
            }

            $xml .= '</airline-data>';
        }
        $xml .= '</product>';
        $xml .= '<amount country-id="'. $this->getCountryID() .'">'. $this->getAmount() .'</amount>';
        $xml .= '<fees>';
        $xml .= '<fee country-id="'. $this->getCountryID() .'">'. $this->getFees() .'</fee>';
        $xml .= '</fees>';
        if($this->getPoints() > 0) {
            $xml .= '<points>' . $this->getPoints() . '</points>';
        }
        if($this->getReward() > 0) {
            $xml .= '<reward>' . $this->getReward() . '</reward>';
        }
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

    public function toAttributeLessXML()
    {
        $xml = '';
        foreach ($this->getAddressConfigs() as $address_Obj)
        {
            if( ($address_Obj instanceof AddressInfo) === true )
            {
                $xml .= $address_Obj->toAttributeLessXML();
            }
        }
        $xml .= '<lineItem>';
        $xml .= '<product>';
        $xml .= '<orderRef>'. $this->getOrderRef() .'</orderRef>';
        $xml .= '<sku>'. $this->getProductSKU() .'</sku>';
        $xml .= '<name>'. $this->getProductName() .'</name>';
        $xml .= '<description>'. $this->getProductDesc() .'</description>';
        $xml .= '<imageUrl>' . $this->getProductImageURL() . '</imageUrl>';

        if(count($this->getFlightConfigs()) > 0 )
        {
            $xml .= '<airlineData>';
            foreach ($this->getFlightConfigs() as $flight_Obj)
            {
                if (($flight_Obj instanceof FlightInfo) === TRUE)
                {
                    $xml .= $flight_Obj->toAttributeLessXML();
                }
            }
            foreach ($this->getPassengerConfigs() as $passenger_Obj)
            {
                if (($passenger_Obj instanceof PassengerInfo) === TRUE)
                {
                    $xml .= $passenger_Obj->toAttributeLessXML();
                }
            }
            $xml .= '</airlineData>';
        }
        $xml .= '</product>';

        $xml .= '<amount>';
        $xml .=     '<countryId>'.$this->getCountryID().'</countryId>';
        $xml .=     '<value>'.$this->getAmount().'</value>';
        $xml .= '</amount>';


        $xml .= '<fees>';
        $xml .= '<fee>';
        $xml .=  '<countryId>'.$this->getCountryID().'</countryId>';
        $xml .=  '<value>'.$this->getFees().'</value>';
        $xml .= '</fee>';
        $xml .= '</fees>';
        $xml .= '<points>'. $this->getPoints() .'</points>';
        $xml .= '<reward>'. $this->getReward() .'</reward>';
        $xml .= '<quantity>'. $this->getQuantity() .'</quantity>';
        $additionalData = $this->getAdditionalData();
        if (empty($additionalData) === false ) {
            $xml .= '<additionalData>';
            foreach ($additionalData as $fAdditionalData)
            {
                $xml .= '<param>';
                $xml .=  '<name>'. $fAdditionalData ["NAME"] . '</name>';
                $xml .=  '<value>'. $fAdditionalData ["VALUE"] . '</value>';
                $xml .= '</param>';
            }
            $xml .= '</additionalData>';
        }
        $xml .= '</lineItem>';
        return $xml;
    }
}
?>