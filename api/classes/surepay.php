<?php
/**
 * The SurePay Package provide methods for monitoring the progress of each Customer.
 * If a Customer is having trouble SurePay will provide help in an attempt to guide him / her through the payment process.
 * Should SurePay determine that the customer has failed completely, the Client's Customer Service will be notified.
 * Currently SurePay performs the following tasks:
 * 	- Monitors that Customers have activated the Payment Link
 * 	- Re-Sends the Payment Link embedded in an SMS for customers who have not yet activated the original link
 *	- Re-Sends the Payment Link as a WAP Push for customers who have not yet activated either the original or the secondary link
 * 	- Notifies Customer Service via e-mail if a customer fails to activate any of the Payment Links sent
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package SurePay
 * @version 1.10
 */

/* ==================== SurePay Exception Classes Start ==================== */
/**
 * Exception class for all SurePay exceptions
 */
class SurePayException extends mPointException { }
/* ==================== SurePay Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for SurePay.
 * The class provides logic for performing the following tasks:
 * 	- Find all Customers who haven't activated the Payment Link
 * 	- Re-Send the Payment Link embedded in an SMS for customers who have not yet activated the original link
 *	- Re-Send the Payment Link as a WAP Push for customers who have not yet activated either the original or the secondary link
 * 	- Notify Customer Service via e-mail if a customer fails to activate any of the Payment Links sent
 *
 */
class SurePay extends General
{
	/**
	 * Data object with the Transaction Information
	 *
	 * @var TxnInfo
	 */
	private $_obj_TxnInfo;

	/**
	 * URL for the Payment Link
	 *
	 * @var string
	 */
	private $_sURL;
	/**
	 * E-Mail Address to Client's Customer Service
	 *
	 * @var string
	 */
	private $_sEMail;
	/**
	 * Timestamp for when the Customer's Transaction was created in the format: YYYY-MM-DD hh:mm:ss
	 *
	 * @var string
	 */
	private $_sTxnCreated;

	/**
	 * Default Constructor.
	 *
	 * @param	RDB $oDB				Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param	TranslateText $oTxt 	Text Translation Object for translating any text into a specific language
	 * @param 	TxnInfo $oTI 			Data object with the Transaction Information
	 * @param 	String $url 			URL for the Payment Link
	 * @param 	String $email 			E-Mail Address to Client's Customer Service
	 * @param 	String $ts 				Timestamp for when the Customer's Transaction was created in the format: YYYY-MM-DD hh:mm:ss
	 */
	public function __construct(RDB &$oDB, TranslateText &$oTxt, TxnInfo &$oTI, $url, $email, $ts)
	{
		parent::__construct($oDB, $oTxt);

		$this->_obj_TxnInfo = $oTI;
		$this->_sURL = trim($url);
		$this->_sEMail = trim($email);
		$this->_sTxnCreated = trim($ts);
	}

	/**
	 * Returns the Data object with the Transaction Information.
	 *
	 * @return TxnInfo
	 */
	public function &getTxnInfo() { return $this->_obj_TxnInfo; }
	/**
	 * Returns the URL for the Payment Link
	 *
	 * @return string
	 */
	public function getLink() { return $this->_sURL; }

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
	 * @param 	GoMobileConnInfo $oCI 	Reference to the data object with the Connection Info required to communicate with GoMobile
	 * @param 	TxnInfo $oTI 			Data Object for the Transaction for which an MT with the payment link should be send out
	 * @param 	string $url 			Absolute URL to mPoint that will be sent to the customer
	 * @throws 	mPointException
	 */
	public function sendEmbeddedLink(GoMobileConnInfo &$oCI, TxnInfo &$oTI, $url)
	{
		switch ($oTI->getOperator() )
		{
		case (20002):	// Verizon Wireless - USA
		case (20005):	// Nextel - USA
		case (20006):	// Boost - USA
		case (20007):	// Alltel - USA
		case (20010):	// US Cellular - USA
			$this->newMessage($oTI->getID(), Constants::iUNSUPPORTED_OPERATOR, var_export($obj_MsgInfo, true) );
			throw new mPointException("Operator: ". $oTI->getOperator() ." not supported", 1011);
			break;
		default:
			$sBody = $this->getText()->_("mPoint - Embedded link Resend Indication") ."\n". $url;
			$sBody = str_replace("{CLIENT}", $oTI->getClientConfig()->getName(), $sBody);
			// Instantiate Message Object for holding the message data which will be sent to GoMobile
			$obj_MsgInfo = GoMobileMessage::produceMessage(Constants::iMT_SMS_TYPE, $oTI->getClientConfig()->getCountryConfig()->getID(), $oTI->getOperator(), $oTI->getClientConfig()->getCountryConfig()->getChannel(), $oTI->getClientConfig()->getKeywordConfig()->getKeyword(), Constants::iMT_PRICE, $oTI->getMobile(), utf8_decode($sBody) );
			break;
		}
		$obj_MsgInfo->setDescription("mPoint - SurePay Link");
		// Send Link to Customer
		$this->sendMT($oCI, $obj_MsgInfo, $oTI);
	}

	/**
	 * Notifies a Client's Customer Service via E-Mail that a customer has not activated the payment link for an order.
	 * This allows a Customer Service agent to manually contact the customer to complete the payment.
	 *
	 */
	public function notifyClient()
	{
		$obj_EMail = new EMailReceipt($this->getDBConn(), $this->getText(), $this->_obj_TxnInfo);

		// Construct Notification Subject
		$sSubject = $this->getText()->_("SurePay Notification - Subject");
		$sSubject = str_replace("{ORDERID}", $this->_obj_TxnInfo->getOrderID(), $sSubject);
		$sSubject = str_replace("{MPOINTID}", $this->_obj_TxnInfo->getID(), $sSubject);
		// Construct Notification Body
		$sBody = $this->getText()->_("SurePay Notification - Body");
		$sBody = str_replace("{MOBILE}", $this->_obj_TxnInfo->getMobile(), $sBody);
		$sBody = str_replace("{ORDERID}", $this->_obj_TxnInfo->getOrderID(), $sBody);
		$sBody = str_replace("{MPOINTID}", $this->_obj_TxnInfo->getID(), $sBody);
		$sBody = str_replace("{PRICE}", General::formatAmount($this->_obj_TxnInfo->getClientConfig()->getCountryConfig(), $this->_obj_TxnInfo->getAmount() ), $sBody);
		$sBody = str_replace("{TIMESTAMP}", $this->_sTxnCreated, $sBody);

		if (mail($this->_sEMail, $sSubject, $sBody, $obj_EMail->constSMTPHeaders() ) === true)
		{
			$this->newMessage($this->_obj_TxnInfo->getID(), Constants::iEMAIL_ACCEPTED_STATE, $sSubject);
		}
		else { $this->newMessage($this->_obj_TxnInfo->getID(), Constants::iEMAIL_REJECTED_STATE, $sSubject); }
	}

	/**
	 * Produces a SurePay object for each Customer who have not activated his Payment Link.
	 * This allows SurePay to perform one of the following task depending on the type:
	 * 	1. Re-Send the Payment Link embedded in an SMS
	 * 	2. Re-Send the Payment Link as a WAP Push
	 * 	3. Notify Client's Customer Service so they can complete the Payment manually
	 * The method will return an array of SurePay object for the scenario type.
	 *
	 * @param 	RDB $oDB 		Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param 	integer $type 	Type of Scenario that customers should be found for
	 * @return 	array
	 */
	public static function produceSurePays(RDB &$oDB, $type)
	{
		// Fetch all Customers who have not yet activated their construced Payment Link
		$sql = "SELECT Txn.id, Msg.data AS url, SP.email, Extract(epoch from Txn.created) AS created
				FROM Log.Transaction_Tbl Txn
				INNER JOIN Client.SurePay_Tbl SP ON Txn.clientid = SP.clientid AND SP.enabled = '1'
				INNER JOIN Log.Message_Tbl Msg ON Txn.id = Msg.txnid
				WHERE Msg.stateid = ". Constants::iCONST_LINK_STATE;
		// Determine elapsed time from the scenario type
		switch ($type)
		{
		case (1):	// Re-Send Payment Link as an MT-SMS
			$iState = Constants::iPAYMENT_LINK_RESENT_AS_EMBEDDED_STATE;
			$sql .= " AND Extract(epoch from Txn.created) >= Extract(epoch from LOCALTIMESTAMP) - SP.resend * 60 - 60
					  AND Extract(epoch from Txn.created) <= Extract(epoch from LOCALTIMESTAMP) - SP.resend * 60";
			break;
		case (2):	// Re-Send Payment Link 2nd time as an MT-WAP Push
			$iState = Constants::iPAYMENT_LINK_RESENT_AS_WAPPUSH_STATE;
			$sql .= " AND Extract(epoch from Txn.created) >= Extract(epoch from LOCALTIMESTAMP) - SP.resend * 3 * 60 - 60
					  AND Extract(epoch from Txn.created) <= Extract(epoch from LOCALTIMESTAMP) - SP.resend * 3 * 60";
			break;
		case (3):	// Notify Customer Service
			$iState = Constants::iCUSTOMER_SERVICE_NOTIFIED_STATE;
			$sql .= " AND Extract(epoch from Txn.created) >= Extract(epoch from LOCALTIMESTAMP) - SP.resend * 3 * 60 - SP.notify * 60 - 60
					  AND Extract(epoch from Txn.created) <= Extract(epoch from LOCALTIMESTAMP) - SP.resend * 3 * 60 - SP.notify * 60";
			break;
		}
		$sql .= " AND NOT EXISTS (SELECT id
								  FROM Log.Message_Tbl M
								  WHERE M.txnid = Txn.id AND M.stateid IN (". Constants::iACTIVATE_LINK_STATE .", ". $iState .") )
				 FOR UPDATE";
//		echo $sql ."\n";
		$res = $oDB->query($sql);
		// Produce a SurePay object for each Customer
		$aObj_mPoints = array();
		while ($RS = $oDB->fetchName($res) )
		{
			$obj_TxnInfo = TxnInfo::produceInfo($RS["ID"], $oDB);
			// Intialise Text Translation Object
			$obj_Txt = new TranslateText(array(sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/global.txt", sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");

			$aObj_mPoints[] = new SurePay($oDB, $obj_Txt, $obj_TxnInfo, $RS["URL"], $RS["EMAIL"], date("Y-m-d H:i:s", $RS["CREATED"]) );
			$aObj_mPoints[count($aObj_mPoints)-1]->newMessage($RS["ID"], $iState, "");
		}

		return $aObj_mPoints;
	}
}
?>