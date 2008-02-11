<?php
/**
 * The Call Centre package provides the business logic for mPoint's Call Centre API.
 * This API will start a new mPoint transaction and generate a WAP Link which is then sent to the recipient.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package CallCentre
 * @version 1.0
 */

/**
 * Model Class containing all the Business Logic for handling an mPoint Transaction initiated by a Call Centre Agent.
 *
 */
class CallCentre extends General
{
	/**
	 * Data object with the Client's configuration
	 *
	 * @var ClientConfig
	 */
	private $_obj_ClientConfig;
	
	/**
	 * Default Constructor.
	 *
	 * @param	RDB $oDB			Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param	TranslateText $oDB 	Text Translation Object for translating any text into a specific language
	 * @param 	ClientConfig $oCC 	Data object with the Client's configuration
	 */
	public function __construct(RDB &$oDB, TranslateText &$oTxt, ClientConfig &$oCC)
	{
		parent::__construct($oDB, $oTxt);
		
		$this->_obj_ClientConfig = $oCC;
	}
	
	/**
	 * Returns the Data object with the Client's configuration.
	 *
	 * @return ClientConfig
	 */
	protected function &getClientConfig() { return $this->_obj_ClientConfig; }
	
	/**
	 * Logs the data for the Products the Customer is purchasing for easy future retrieval.
	 * 
	 * @see 	Constants::iPRODUCTS_STATE
	 * 
	 * @param 	array $aNames 		Reference to the list of Product Names
	 * @param 	array $aQuantities 	Reference to the list of Product Qantities
	 * @param 	array $aPrices 		Reference to the list of Product Prices
	 * @param 	array $aLogos 		Reference to the list of URLs to the Logo for each Product
	 */
	public function logProducts(array &$aNames, array &$aQuantities, array &$aPrices, array &$aLogos)
	{
		// Construct list of Products
		$aProducts = array("names" => $aNames,
						   "quantities" => $aQuantities,
						   "prices" => $aPrices,
						   "logos" => $aLogos);
		$this->newMessage(Constants::iPRODUCTS_STATE, serialize($aProducts) );
	}
	
	/**
	 * Logs the custom variables provided by the Client for easy future retrieval.
	 * 
	 * @see 	Constants::iCLIENT_VARS_STATE
	 * 
	 * @param 	array $aInput 	Input
	 */
	public function logClientVars(array &$aInput)
	{
		$aClientVars = array();
		foreach ($aInput as $key => $val)
		{
			if (substr($key, 0, 4) == "var_") { $aClientVars[$key] = $val; }
		}
		if (count($aClientVars) > 0) { $this->newMessage(Constants::iCLIENT_VARS_STATE, serialize($aClientVars) ); }
	}
	
	/**
	 * Starts a new Transaction and generates a unique ID for the log entry.
	 * Additionally the method sets the private variable: _iTransactionID and returns the generated Transaction ID.
	 * The method will throw an mPointException with either code 1001 or 1002 if one of the database queries fails.
	 *
	 * @param 	integer $tid 	Unique ID for the Type of Transaction that is started 
	 * @return 	integer
	 * @throws 	mPointException
	 */
	public function newTransaction($tid)
	{
		$sql = "SELECT Nextval('Log.Transaction_Tbl_id_seq') AS id";
		$RS = $this->getDBConn()->getName($sql);
		// Error: Unable to generate a new Transaction ID
		if (is_array($RS) === false) { throw new mPointException("Unable to generate new Transaction ID", 1001); }
		$this->_iTransactionID = $RS["ID"];
		
		$sql = "INSERT INTO Log.Transaction_Tbl
					(id, typeid, clientid, accountid, countryid, keywordid)
				VALUES
					(". $this->_iTransactionID .", ". intval($tid) .", ". $this->getClientConfig()->getID() .", ". $this->getClientConfig()->getAccountConfig()->getID() .", ". $this->getClientConfig()->getCountryConfig()->getID() .", ". $this->getClientConfig()->getKeywordConfig()->getID() .")";
//		echo $sql ."\n";
		// Error: Unable to insert a new record in the Transaction Log
		if (is_resource($this->getDBConn()->query($sql) ) === false)
		{
			if (is_array($RS) === false) { throw new mPointException("Unable to insert new record for Transaction: ". $this->_iTransactionID, 1002); }
		}
		
		return $this->_iTransactionID;
	}
	
	/**
	 * Adds a new entry to the Message log with the provided debug data.
	 * The method will throw an mPointException with code 1003 if the database query fails.
	 *
	 * @param 	integer $sid 	Unique ID of the State that the data is associated with
	 * @param 	string $data 	Debug data to associate with the state
	 * @throws 	mPointException
	 */
	public function newMessage($sid, $data)
	{
		$sql = "INSERT INTO Log.Message_Tbl
					(txnid, stateid, data)
				VALUES
					(". $this->_iTransactionID ." , ". $sid .", '". $this->getDBConn()->escStr($data) ."')";
//		echo $sql ."\n";
		// Error: Unable to insert a new message for Transaction
		if (is_resource($this->getDBConn()->query($sql) ) === false)
		{
			throw new mPointException("Unable to insert new message for Transaction: ". $this->_iTransactionID ." and State: ". $sid, 1003);
		}
	}
	
	/**
	 * Constructs the download link for the transaction.
	 * The link is contructed through the following alghorithm:
	 * 	- {TXN CREATED} is an integer representing the timestamp for when the transaction was created since unix epoch
	 * 	- {TXN ID} is the ID of the outgoing MT-SMS transaction
	 *	The fields are separated by a Z and are using 32 digit numbering (as opposed to "standard" decimal).
	 * 
	 * The returned link has the following format:
	 * 	http://{DOMAIN}/base_convert({TXN CREATED}, 10, 32)Zbase_convert({TXN ID}, 10, 32)
	 * 
	 * A new log entry is created in the Message Log with the constructed link under state "Link Constructed"
	 * 
	 * @see 	CallCentre::newMessage()
	 * @see 	Constants::iCONST_LINK_STATE
	 *
	 * @param 	integer $oid 	GoMobile's ID for the Customer's Mobile Network Operator
	 * @return 	string
	 */
	public function constLink($oid)
	{
		$sql = "SELECT Extract('epoch' from created) AS timestamp
				FROM Log.Transaction_Tbl
				WHERE id = ". $this->_iTransactionID;
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);
		
		$sLink = "http://";
		// Customer's Operator is Sprint
		if ($oid == 20004) { $sLink .= sSPRINT_MPOINT_DOMAIN; }
		else { $sLink .= sDEFAULT_MPOINT_DOMAIN; }
		$sLink .= "/". base_convert(intval($RS["TIMESTAMP"]), 10, 32) ."Z". base_convert($this->_iTransactionID, 10, 32);
		
		$this->newMessage(Constants::iCONST_LINK_STATE, $sLink);
		
		return $sLink;
	}
	
	/**
	 * Constructs an MT and sends it to GoMobile.
	 * Prior to sending the message the method will updated the provided Connection Info object with the Client's username / password for GoMobile.
	 * Additionally the method will determine from the customer's Mobile Network Operator whether to send an MT-WAP Push (default) or an MT-SMS 
	 * with the link embedded.
	 * The method will throw an mPointException with an error code in the following scenarios:
	 * 	1005. Operator not supported
	 * 	1006. Message rejected by GoMobile
	 * 	1007. Unable to connect to GoMobile
	 * 
	 * @see 	GoMobileClient
	 * @see 	Constants::iMT_SMS_TYPE
	 * @see 	Constants::iMT_WAP_PUSH_TYPE
	 * @see 	Constants::iMT_PRICE
	 *
	 * @param 	GoMobileConnInfo $oCI 	Connection Info required to communicate with GoMobile
	 * @param 	TxnInfo $oTI 			Data Object for the Transaction for which an MT with the payment link should be send out
	 * @param 	string $url 			Absolute URL to mPoint that will be sent to the customer
	 * @throws 	mPointException
	 */
	public function sendLink(GoMobileConnInfo &$oCI, TxnInfo &$oTI, $url)
	{
		// Re-Instantiate Connection Information for GoMobile using the Client's username / password
		$oCI = new GoMobileConnInfo($oCI->getProtocol(), $oCI->getHost(), $oCI->getPort(), $oCI->getTimeout(), $oCI->getPath(), $oCI->getMethod(), $oCI->getContentType(), $oTI->getClientConfig()->getUsername(), $oTI->getClientConfig()->getPassword(), $oCI->getLogPath(), $oCI->getMode() );
		
		// Instantiate client object for communicating with GoMobile
		$obj_GoMobile = new GoMobileClient($oCI);
		
		switch ($oTI->getOperator() )
		{
		case (20002):	// Verizon Wireless - USA
		case (20005):	// Nextel - USA
		case (20006):	// Boost - USA
		case (20007):	// Alltel - USA
		case (20010):	// US Cellular - USA
			$this->newMessage(Constants::iUNSUPPORTED_OPERATOR, var_export($obj_MsgInfo, true) );
			throw new mPointException("Operator: ". $oTI->getOperator() ." not supported", 1005);
			break;
		case (20004):	// Sprint - USA
		case (13003):	// 3 - UK
			$sBody = $this->getText()->_("mPoint - Embedded link Indication") ."\n". $url;
			// Instantiate Message Object for holding the message data which will be sent to GoMobile
			$obj_MsgInfo = GoMobileMessage::produceMessage(Constants::iMT_SMS_TYPE, $oTI->getClientConfig()->getCountryConfig()->getID(), $oTI->getOperator(), $oTI->getClientConfig()->getCountryConfig()->getChannel(), $oTI->getClientConfig()->getKeywordConfig()->getKeyword(), Constants::iMT_PRICE, $oTI->getAddress(), $sBody);
			break;
		default:
			// Instantiate Message Object for holding the message data which will be sent to GoMobile
			$obj_MsgInfo = GoMobileMessage::produceMessage(Constants::iMT_WAP_PUSH_TYPE, $oTI->getClientConfig()->getCountryConfig()->getID(), $oTI->getOperator(), $oTI->getClientConfig()->getCountryConfig()->getChannel(), $oTI->getClientConfig()->getKeywordConfig()->getKeyword(), Constants::iMT_PRICE, $oTI->getAddress(), $this->getText()->_("mPoint - WAP Push Indication"), $url);
			break;
		}
		
		/* ========== Send MT Start ========== */
		$bSend = true;		// Continue to send messages
		$iAttempts = 0;		// Number of Attempts
		// Send messages
		while ($bSend === true && $iAttempts < 3)
		{
			$iAttempts++;
			try
			{
				// Error: Message rejected by GoMobile
				if ($obj_GoMobile->communicate($obj_MsgInfo) != 200)
				{
					$this->newMessage(Constants::iMSG_REJECTED_BY_GM_STATE, var_export($obj_MsgInfo, true) );
					throw new mPointException("Message rejected by GoMobile with code(s): ". $obj_MsgInfo->getReturnCodes(), 1006);
				}
				$this->newMessage(Constants::iMSG_ACCEPTED_BY_GM_STATE, var_export($obj_MsgInfo, true) );
				$bSend = false;
			}
			// Communication error, retry message sending
			catch (HTTPException $e)
			{
				// Error: Unable to connect to GoMobile
				if ($iAttempts == 3)
				{
					$this->newMessage(Constants::iGM_CONN_FAILED_STATE, var_export($oCI, true) );
					throw new mPointException("Unable to connect to GoMobile", 1007);
				}
				else { sleep(pow(5, $iAttempts) ); }
			}
		}
		/* ========== Send MT End ========== */
	}
	
	/**
	 * Updates the Transaction Log record for the provided transaction with all data.
	 * The method will throw an mPointException with code 1004 if the database update fails.
	 *
	 * @param 	TxnInfo $oTI 	Data Object for the Transaction which should be updated
	 * @throws 	mPointException
	 */
	public function logTransaction(TxnInfo &$oTI)
	{
		$sql = "UPDATE Log.Transaction_Tbl
				SET typeid = ". $oTI->getTypeID() .", clientid = ". $oTI->getClientConfig()->getID() .", accountid = ". $oTI->getClientConfig()->getAccountConfig()->getID() .",
					countryid = ". $oTI->getClientConfig()->getCountryConfig()->getID() .", keywordid = ". $this->getClientConfig()->getKeywordConfig()->getID() .",
					amount = ". $oTI->getAmount() .", orderid = '". $this->getDBConn()->escStr($oTI->getOrderID() ) ."', lang = '". $this->getDBConn()->escStr($oTI->getLanguage() ) ."',
					address = ". floatval($oTI->getAddress() ) .", operatorid = ". $oTI->getOperator() .", logourl = '". $this->getDBConn()->escStr($oTI->getLogoURL() ) ."',
					cssurl = '". $this->getDBConn()->escStr($oTI->getCSSURL() ) ."', accepturl = '". $this->getDBConn()->escStr($oTI->getAcceptURL() ) ."',
					cancelurl = '". $this->getDBConn()->escStr($oTI->getCancelURL() ) ."', callbackurl = '". $this->getDBConn()->escStr($oTI->getCallbackURL() ) ."'
				WHERE id = ". $oTI->getID(); 
//		echo $sql ."\n";
		// Error: Unable to update Transaction
		if (is_resource($this->getDBConn()->query($sql) ) === false)
		{
			throw new mPointException("Unable to update Transaction: ". $oTI->getID(), 1004);
		}
	}
}
?>