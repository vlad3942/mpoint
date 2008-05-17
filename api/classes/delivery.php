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
 * @subpackage Delivery
 * @version 1.0
 */

/**
 * Model Class containing all the Business Logic for generating the Product List page
 *
 */
class Delivery extends General
{
	/**
	 * Data object with the Transaction Information
	 *
	 * @var TxnInfo
	 */
	private $_obj_TxnInfo;
	/**
	 * Data object with the Connection Information to make an RPC to a Lookup Server that can find and Address
	 * based on an MSISDN.
	 *
	 * @var HTTPConnInfo
	 */
	private $_obj_ConnInfo;
	
	/**
	 * Default Constructor.
	 *
	 * @param	RDB $oDB				Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param	TranslateText $oTxt 	Text Translation Object for translating any text into a specific language
	 * @param 	TxnInfo $oTI 			Data object with the Transaction Information
	 * @param 	HTTPConnInfo $oCI 		Data object with the Connection Information to a Lookup Server
	 */
	public function __construct(RDB &$oDB, TranslateText &$oTxt, TxnInfo &$oTI, HTTPConnInfo &$oCI=null)
	{
		parent::__construct($oDB, $oTxt);
		
		$this->_obj_TxnInfo = $oTI;
		$this->_obj_ConnInfo = $oCI;
	}
	
	/**
	 * Decodes a string in JSON format and turns it into an array.
	 * The method will return an associative array for objects using the variable names as named keys or
	 * an indexed array for arrays.
	 * JSON is used to serialise both arrays and objects for transport via HTTP.
	 * The method will parse the string looking for the following key tokens:
	 * 	{ = Object Start
	 * 	} = Object End
	 * 	[ = Array Start
	 * 	] = Array End
	 * 	' = String Start or End
	 * 	" = String Start or End
	 * 	: = Key End
	 * 	, = Key / Value Pair End
	 * 	\ = Escape character
	 * 
	 * @link 	www.json.org
	 *
	 * @param 	string $str 	JSON String which should be decoded
	 * @return 	array
	 */
	private function _decode($str)
	{
		$aReturn = array();
		// Flag controlling whether the current character is inside or outside a string
		$bInString = false;
		// Flag controlling whether the JSON string is a serialized object
		$bObject = false;
		// Flag controlling whether the JSON string is a serialized Array
		$bArray = false;
		// Current Array key (only applicable when decoding an object)
		$key = "";
		// Current Value, each character will be appended to this variable unless some specific action needs to be taken
		$val = "";
		
		// Loop through each character in the JSON String
		for ($i=0; $i<strlen($str); $i++)
		{
			// Determine action based on character
			switch ($str{$i} )
			{
			case "{":	// Object Start
				if ($bInString === false) { $bObject = true; }
				else { $val .= $str{$i}; }
				break;
			case "}":	// Object End
				if ($bInString === false)
				{
					$bObject = false;
					$aReturn[trim($key)] = trim($val);
					$key = "";
					$val = "";
				}
				else { $val .= $str{$i}; }
				break;
			case "[":	// Array Start
				if ($bInString === false) { $bArray = true; }
				else { $val .= $str{$i}; }
				break;
			case "]":	// Array End
				if ($bInString === false)
				{
					$bArray = false;
					$aReturn[] = trim($val);
					$key = "";
					$val = "";
				}
				else { $val .= $str{$i}; }
				break;
			case "'":	// String
				// Single Quote has NOT been escaped
				if ($str{$i-1} != "\\")
				{
					if ($bInString === false) { $bInString = true; }
					else { $bInString = false; }
				}
				else { $val .= $str{$i}; }
				break;
			case '"':	// String
				// Double Quote has NOT been escaped
				if ($str{$i-1} != "\\")
				{
					if ($bInString === false) { $bInString = true; }
					else { $bInString = false; }
				}
				else { $val .= $str{$i}; }
				break;
			case ":":	// Key End
				if ($bInString === false)
				{
					$key = $val;
					$val = "";
				}
				else { $val .= $str{$i}; }
				break;
			case ",":	// Key / Value Pair End
				if ($bInString === false)
				{
					if ($bObject === true) { $aReturn[trim($key)] = trim($val); }
					elseif ($bArray === true) { $aReturn[] = trim($val); }
					$key = "";
					$val = "";
				}
				else { $val .= $str{$i}; }
				break;
			case " ":	// Blankspace
				if ($bInString === true) { $val .= $str{$i}; }
				break;
			default:	// Other Character
				// Backslash
				if ($str{$i} == "\\")
				{
					// Blackslash is NOT an escape character
					if ($str{$i+1} != "'" && $str{$i+1} != '"') { $val .= $str{$i}; }
				}
				else { $val .= $str{$i}; }
				break;
			}
		}
		
		return $aReturn;
	}
	
	/**
	 * Uses Interflora's RPC Service to translate an MSISDN to an Address.
	 * The service uses Interflora (whoever they use in turn is anyone's guess)
	 *
	 * @param 	string $msisdn 	MSISDN to use for the Lookup
	 * @return 	array
	 */
	private function _lookupViaInterflora($msisdn)
	{
		$obj_HTTP = new HTTPClient(new Template(), $this->_obj_ConnInfo);
		$obj_HTTP->connect();
		$obj_HTTP->send(str_replace("{PATH}", "{PATH}?number=". $msisdn, $this->constHeaders() ) );
		$obj_HTTP->disconnect();
		file_put_contents(sLOG_PATH ."/jona.log", $obj_HTTP->getReplyBody()  );
		$aDeliveryInfo = $this->_decode($obj_HTTP->getReplyBody() );
		if (count($aDeliveryInfo) > 0)
		{
			$aDeliveryInfo["street"] = $aDeliveryInfo["address"];
			unset($aDeliveryInfo["address"]);
			unset($aDeliveryInfo["phoneno"]);
		}
		else { $aDeliveryInfo = array(); }
				
		return $aDeliveryInfo;
	}
	
	/**
	 * Translates an MSISDN to an Address using the provided lookup server.
	 * 
	 * @param 	string $msisdn 	MSISDN to use for the Lookup
	 * @return 	array
	 */
	public function getDeliveryAddressFromMSISDN($msisdn)
	{
		switch ($this->_obj_TxnInfo->getClientConfig()->getCountryConfig()->getID() )
		{
		case (10):	// Denmark
			$aDeliveryInfo = $this->_lookupViaInterflora($msisdn);
			break;
		default:	// No Lookup Service available for Country
			$aDeliveryInfo = array();
			break;
		}
		
		return $aDeliveryInfo;
	}
	
	public function logDeliveryInfo(array &$aInfo)
	{
		$this->newMessage($this->_obj_TxnInfo->getID(), Constants::iDELIVERY_INFO_STATE, serialize($aInfo) );
	}
}
?>