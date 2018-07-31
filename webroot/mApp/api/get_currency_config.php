<?php
/**
 * This API fetches Currency and Country configuration
 * 	- clientid
 *
 */

/**
 * Input XML format
 *
<?xml version="1.0" encoding="UTF-8"?>
<root>
	<get-currency-config client-id="10018">
	  <currency code="USD" country-code="US" />
	</get-currency-config>
</root>
 */

// Require Global Include File
require_once("../../inc/include.php");

require_once(sAPI_CLASS_PATH ."simpledom.php");

$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);
if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true) {
			if (($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH . "mpoint.xsd") === true && count($obj_DOM->{'get-currency-config'}) > 0) {
				// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
				$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );
				
				$i = 0;
				$xml = '';
				while ( ($_OBJ_DB instanceof RDB) === false && $i < 5)
				{
					// Instantiate connection to the Database
					$_OBJ_DB = RDB::produceDatabase($aDB_CONN_INFO["mpoint"]);
					$i++;
				}
				
				$sCurrencyCode= $obj_DOM->{'get-currency-config'}[$i]->currency["code"];
				$sCountryCode= $obj_DOM->{'get-currency-config'}[$i]->currency["country-code"];
				
				$sql = "SELECT  CC.id AS currencyid, CT.id AS countryid, CT.priceformat,CT.symbol,CC.code AS currencycode ,CT.priceformat,
				CC.decimals FROM system".sSCHEMA_POSTFIX.".Country_tbl CT , System".sSCHEMA_POSTFIX.".Currency_tbl CC
                WHERE CC.code = '".$sCurrencyCode ."' AND CT.alpha2code = '".$sCountryCode."'";
				
				//echo $sql ;
				$RS = $_OBJ_DB->getName($sql);
				
				$xml .= '<currency id= "'.$RS["CURRENCYID"].'" decimals="'.$RS["DECIMALS"].'" price-format="'.$RS["SYMBOL"].'" country-id="'.$RS["COUNTRYID"].'" />' ;
		}
		elseif ( ($obj_DOM instanceof SimpleDOMElement) === false)
		{
			header("HTTP/1.1 415 Unsupported Media Type");
			
			$xml = '<status code="415">Invalid XML Document</status>';
		}
		// Error: Wrong operation
		elseif (count($obj_DOM->{'get-currency-config'}) == 0)
		{
			header("HTTP/1.1 400 Bad Request");
			
			$xml = '';
			foreach ($obj_DOM->children() as $obj_Elem)
			{
				$xml .= '<status code="400">Wrong operation: '. $obj_Elem->getName() .'</status>';
			}
		}
		// Error: Invalid Input
		else
		{
			header("HTTP/1.1 400 Bad Request");
			$aObj_Errs = libxml_get_errors();
			
			$xml = '';
			for ($i=0; $i<count($aObj_Errs); $i++)
			{
				$xml = '<status code="400">'. htmlspecialchars($aObj_Errs[$i]->message, ENT_NOQUOTES) .'</status>';
			}
		}
	
}
else
{
	header("HTTP/1.1 401 Unauthorized");
	
	$xml = '<status code="401">Authorization required</status>';
}

header("Content-Type: text/xml; charset=\"UTF-8\"");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';


return $xml;



