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
	<get-currency-config client-id="10018" currency-code="USD" country-code="US">
	</get-currency-config>
</root>
 */

// Require Global Include File
require_once("../../inc/include.php");

require_once(sAPI_CLASS_PATH ."simpledom.php");
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
$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

$sql = "SELECT CT.id AS countryid, CC.id AS currencyid, CT.name AS country, CT.priceformat,CT.symbol,CC.code AS currencycode ,
CT.decimals from system".sSCHEMA_POSTFIX.".Country_tbl CT Join System".sSCHEMA_POSTFIX.".Currency_tbl CC ON (CT.currencyid = CC.id)";


if (isset($obj_DOM->{'get-currency-config'}[$i]["currency-code"]) === true)
{
	$sCurrencyCode= $obj_DOM->{'get-currency-config'}[$i]["currency-code"];
	$sql .= " WHERE CC.code = '".$sCurrencyCode ."'" ;
}
if (isset($obj_DOM->{'get-currency-config'}[$i]["country-code"]) === true)
{
	$sCountryCode= $obj_DOM->{'get-currency-config'}[$i]["country-code"];
	$sql .= " AND CT.alpha2code = '".$sCountryCode."'";
}


$aConfigurations = array();
$res = $_OBJ_DB->query($sql);

while ($RS = $_OBJ_DB->fetchName($res) )
{
	$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB,$RS["COUNTRYID"]);
	$xml .= $obj_CountryConfig->toXML();
}

header("Content-Type: text/xml; charset=\"UTF-8\"");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';


return $xml;



