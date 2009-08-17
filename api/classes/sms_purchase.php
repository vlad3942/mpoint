<?php
/**
 * The SMS Purchase package provides the business logic for mPoint's SMS API.
 * This API will start a new mPoint transaction and generate a WAP Link which is then sent to the recipient.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package API
 * @subpackage SMS_Purchase
 * @version 1.10
 */

/**
 * Model Class containing all the Business Logic for handling an mPoint Transaction initiated by the Customer via SMS.
 *
 */
class SMS_Purchase extends MobileWeb
{
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
				WHERE keywordid = ". $this->getClientConfig()->getKeywordConfig()->getID() ." AND enabled = true";
//		echo $sql ."\n";
		$aRS = $this->getDBConn()->getAllNames($sql);

		// Construct list of Products
		$aProducts = array("names" => array(),
						   "quantities" => array(),
						   "prices" => array(),
						   "logos" => array() );
		for ($i=0; $i<count($aRS); $i++)
		{
			$aProducts["names"][$aRS[$i]["ID"] ] = $aRS[$i]["NAME"];
			$aProducts["quantities"][$aRS[$i]["ID"] ] = $aRS[$i]["QUANTITY"];
			$aProducts["prices"][$aRS[$i]["ID"] ] = $aRS[$i]["PRICE"];
			$aProducts["logos"][$aRS[$i]["ID"] ] = $aRS[$i]["LOGOURL"];
		}

		$this->newMessage($this->getTransactionID(), Constants::iPRODUCTS_STATE, serialize($aProducts) );
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
	 * @see 	General::newMessage()
	 * @see 	Constants::iCONST_LINK_STATE
	 *
	 * @param 	integer $txnid 	ID of the Transaction that the Payment Link should be constructed for
	 * @param 	integer $oid 	GoMobile's ID for the Customer's Mobile Network Operator
	 * @param 	string $dir 	Directory where the Customer should start his mPoint Flow
	 * @return 	string
	 */
	public function constLink($txnid, $oid, $dir)
	{
		$sql = "SELECT Extract('epoch' from created) AS timestamp
				FROM Log.Transaction_Tbl
				WHERE id = ". intval($txnid);
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);

		$sLink = "http://";
		// Customer's Operator is Sprint
		if ($oid == 20004) { $sLink .= sSPRINT_MPOINT_DOMAIN; }
		else { $sLink .= sDEFAULT_MPOINT_DOMAIN; }

		$sLink .= "/". $dir ."/". base_convert(intval($RS["TIMESTAMP"]), 10, 32) ."Z". base_convert($txnid, 10, 32);

		$this->newMessage($txnid, Constants::iCONST_LINK_STATE, $sLink);

		return $sLink;
	}
	
	public function findTxnIDFromSMS(&$oMI)
	{
		$sql = "SELECT Txn.id
				FROM Log.Transaction_Tbl Txn
				WHERE Txn.typeid = ". Constants::iSMS_PURCHASE_TYPE ." AND Txn.clientid = ". $this->getClientConfig()->getID() ."
					AND Txn.countryid = ". $oMI->getCountry() ." AND Txn.mobile = '". $oMI->getSender() ."'
					AND NOT EXISTS (SELECT id
									FROM Log.Message_Tbl
									WHERE Txn.id = txnid AND stateid IN (". Constants::iPAYMENT_ACCEPTED_STATE .", ". Constants::iPAYMENT_REJECTED_STATE .")
									LIMIT 1)
				ORDER BY Txn.id DESC
				LIMIT 1";
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);
		
		return is_array($RS) === true ? $RS["ID"]: -1;
	}

	/**
	 * Creates a new instance of the SMS Purchase class using the provied Message Info object.
	 * The method will query the database in order to fetch the correct Client and Keyword ID using the
	 * Country, Channel and Keyword contained in the Message Information object.
	 *
	 * @see 	sLANGUAGE_PATH
	 * @see 	TranslateText
	 * @see 	ClientConfig::produceConfig()
	 *
	 * @param	RDB $oDB			Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param 	SMS $oMI 			GoMobile Message Info object which holds the relevant data for the message
	 * @return 	SMS_Purchase
	 */
	public static function produceSMS_Purchase(RDB &$oDB, SMS &$oMI)
	{
		$sql = "SELECT KW.id AS keywordid, Cl.id AS clientid
				FROM Client.Keyword_Tbl KW
				INNER JOIN Client.Client_Tbl Cl ON KW.clientid = Cl.id AND Cl.enabled = true
				INNER JOIN System.Country_Tbl C ON Cl.countryid = C.id AND C.enabled = true
				WHERE C.id = ". $oMI->getCountry() ." AND C.channel = '". $oMI->getChannel() ."'
					AND Upper(KW.name) = Upper('". $oMI->getKeyword() ."') AND KW.enabled = true";
//		echo $sql ."\n";
		$RS = $oDB->getName($sql);

		$obj_ClientConfig = ClientConfig::produceConfig($oDB, $RS["CLIENTID"], -1, $RS["KEYWORDID"]);
		$obj_Txt = new TranslateText(array(sLANGUAGE_PATH . $obj_ClientConfig->getLanguage() ."/global.txt", sLANGUAGE_PATH . $obj_ClientConfig->getLanguage() ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");

		return new SMS_Purchase($oDB, $obj_Txt, $obj_ClientConfig);
	}
}
?>