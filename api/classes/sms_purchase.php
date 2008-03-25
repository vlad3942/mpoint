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
		$obj_Txt = new TranslateText(array(sLANGUAGE_PATH . $obj_ClientConfig->getLanguage() ."/global.txt", sLANGUAGE_PATH . $obj_ClientConfig->getLanguage() ."/custom.txt"), sSYSTEM_PATH, 0);
		
		return new SMS_Purchase($oDB, $obj_Txt, $obj_ClientConfig);
	}
}
?>