<?php
/**
 * The SMS Purchase package provides the business logic for mPoint's SMS API.
 * This API will start a new mPoint transaction and generate a WAP Link which is then sent to the recipient.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package SMS_Purchase
 * @version 1.0
 */

/**
 * Model Class containing all the Business Logic for handling an mPoint Transaction initiated by the Customer via SMS.
 *
 */
class SMS_Purchase extends General
{
	/**
	 * Data object with the Client's configuration
	 *
	 * @var ClientConfig
	 */
	private $_obj_ClientConfig;
	/**
	 * Unique ID for the Started Transaction
	 *
	 * @var integer
	 */
	private $_iTransactionID;
	
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
	 * Returns the Unique ID for the Started Transaction.
	 *
	 * @return integer
	 */
	protected function getTransactionID() { return $this->_iTransactionID; }
	/**
	 * Returns the Data object with the Client's configuration
	 *
	 * @return ClientConfig
	 */
	public function &getClientConfig() { return $this->_obj_ClientConfig; }
	
	/**
	 * Starts a new Transaction and generates a unique ID for the log entry.
	 * Additionally the method sets the private variable: _iTransactionID and returns the generated Transaction ID.
	 * The method will throw an mPointException with either code 1001 or 1002 if one of the database queries fails.
	 * 
	 * @see 	General::newTransaction()
	 *
	 * @param 	integer $tid 	Unique ID for the Type of Transaction that is started 
	 * @return 	integer
	 * @throws 	mPointException
	 */
	public function newTransaction($tid)
	{
		$this->_iTransactionID = parent::newTransaction($this->_obj_ClientConfig, $tid);
		
		return $this->_iTransactionID;
	}
	/**
	 * Logs the data for the Products the Customer is purchasing for easy future retrieval.
	 * 
	 * @see 	Constants::iPRODUCTS_STATE
	 * @see 	General::newMessage()
	 */
	public function logProducts()
	{
		$sql = "SELECT id, name, quantity, price, logourl
				FROM Client.Product_Tbl
				WHERE keywordid = ". $this->_obj_ClientConfig->getKeywordConfig()->getID();
//		echo $sql ."\n";
		$aRS = $this->getDBConn()->getAllNames($sql);
		
		// Construct list of Products
		$aProducts = array("names" => array(),
						   "quantities" => array(),
						   "prices" => array(),
						   "logos" => array());
		for ($i=0; $i<count($aRS); $i++)
		{
			$aProducts["names"][$aRS[$i]["ID"] ] = $aRS[$i]["NAME"];
			$aProducts["quantity"][$aRS[$i]["ID"] ] = $aRS[$i]["QUANTITY"];
			$aProducts["price"][$aRS[$i]["ID"] ] = $aRS[$i]["PRICE"];
			$aProducts["logourl"][$aRS[$i]["ID"] ] = $aRS[$i]["LOGOURL"];
		}
		
		
		$this->newMessage($this->_iTransactionID, Constants::iPRODUCTS_STATE, serialize($aProducts) );
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
		$sLink .= "/txn/". base_convert(intval($RS["TIMESTAMP"]), 10, 32) ."Z". base_convert($this->_iTransactionID, 10, 32);
		
		$this->newMessage($this->_iTransactionID, Constants::iCONST_LINK_STATE, $sLink);
		
		return $sLink;
	}
	
	/**
	 * Constructs an MT and sends it to GoMobile.
	 * Prior to sending the message the method will updated the provided Connection Info object with the Client's username / password for GoMobile.
	 * Additionally the method will determine from the customer's Mobile Network Operator whether to send an MT-WAP Push (default) or an MT-SMS 
	 * with the link embedded.
	 * The method will throw an mPointException with an error code in the following scenarios:
	 * 	1011. Operator not supported
	 * 	1012. Message rejected by GoMobile
	 * 	1013. Unable to connect to GoMobile
	 * 
	 * @see 	GoMobileClient
	 * @see 	Constants::iMT_SMS_TYPE
	 * @see 	Constants::iMT_WAP_PUSH_TYPE
	 * @see 	Constants::iMT_PRICE
	 * @see 	General::newMessage()
	 *
	 * @param 	GoMobileConnInfo $oCI 	Connection Info required to communicate with GoMobile
	 * @param 	TxnInfo $oTI 			Data Object for the Transaction for which an MT with the payment link should be send out
	 * @param 	string $url 			Absolute URL to mPoint that will be sent to the customer
	 * @throws 	mPointException
	 */
	public function sendLink(GoMobileConnInfo &$oCI, TxnInfo &$oTI, $url)
	{	
		switch ($oTI->getOperator() )
		{
		case (20002):	// Verizon Wireless - USA
		case (20005):	// Nextel - USA
		case (20006):	// Boost - USA
		case (20007):	// Alltel - USA
		case (20010):	// US Cellular - USA
			$this->newMessage($this->_iTransactionID, Constants::iUNSUPPORTED_OPERATOR, var_export($obj_MsgInfo, true) );
			throw new mPointException("Operator: ". $oTI->getOperator() ." not supported", 1011);
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
		// Send Link to Customer
		$this->sendMT($oCI, $obj_MsgInfo, $oTI);
	}
	
	/**
	 * Creates a new instance of the SMS Purchase class using the provied Message Info object.
	 * The method will query the database in order to fetch the correct Client and Keyword ID using the
	 * Country, Channel and Keyword contained in the Message Information object.
	 * 
	 * @see 	ClientConfig::produceConfig()
	 *
	 * @param	RDB $oDB			Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param	TranslateText $oTxt Text Translation Object for translating any text into a specific language
	 * @param 	SMS $oMI 			GoMobile Message Info object which holds the relevant data for the message
	 * @return 	SMS_Purchase
	 */
	public static function produceSMS_Purchase(RDB &$oDB, TranslateText &$oTxt, SMS &$oMI)
	{
		$sql = "SELECT KW.id AS keywordid, Cl.id AS clientid
				FROM Client.Keyword_Tbl KW
				INNER JOIN Client.Client_Tbl Cl ON KW.clientid = Cl.id AND Cl.enabled = true
				INNER JOIN System.Country_Tbl C ON Cl.countryid = C.id AND C.enabled = true
				WHERE C.id = ". $oMI->getCountry() ." AND C.channel = '". $oMI->getChannel() ."'
					AND Upper(KW.name) = Upper('". $oMI->getKeyword() ."') AND KW.enabled = true";
//		echo $sql ."\n";
		$RS = $oDB->getName($sql);
		
		return new SMS_Purchase($oDB, $oTxt, ClientConfig::produceConfig($oDB, $RS["CLIENTID"], -1, $RS["KEYWORDID"]) );
	}
}
?>