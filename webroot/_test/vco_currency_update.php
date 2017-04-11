<?php
// Require include file for including all Shared and General APIs
require_once("../inc/include.php");
require_once(sAPI_CLASS_PATH ."simpledom.php");

// Require Interface for defining how the Database is accessed
//require_once(sAPI_INTERFACE_PATH ."database.php");

// Require Database Abstraction API
//require_once(sAPI_CLASS_PATH ."database.php");

/* $aDB_CONN_INFO["mpoint"]["host"] = "dolinuxbl473.hq.emirates.com";
$aDB_CONN_INFO["mpoint"]["port"] = 6516;
$aDB_CONN_INFO["mpoint"]["path"] = "mesbop_ha";
$aDB_CONN_INFO["mpoint"]["username"] = "mesb_user";
$aDB_CONN_INFO["mpoint"]["password"] = "MESB_USER";
$aDB_CONN_INFO["mpoint"]["charset"] = "UTF8"; 

$_OBJ_DB = RDB::produceDatabase($aDB_CONN_INFO["mpoint"]);
*/

$given_data_from_vco = array(
		'AE' => 'AED',
		'AF' => 'USD',
		'AO' => 'AOA',
		'AR' => 'ARS',
		'AT' => 'EUR',
		'AU' => 'AUD',
		'AW' => 'USD',
		'BD' => 'BDT',
		'BE' => 'EUR',
		'BH' => 'BHD',
		'BO' => 'USD',
		'BR' => 'BRL',
		'BW' => 'USD',
		'CA' => 'CAD',
		'CH' => 'CHF',
		'CI' => 'XOF',
		'CN' => 'CNY',
		'CY' => 'EUR',
		'CZ' => 'CZK',
		'DE' => 'EUR',
		'DJ' => 'USD',
		'DK' => 'DKK',
		'DZ' => 'DZD',
		'EE' => 'EUR',
		'EG' => 'EGP',
		'ES' => 'EUR',
		'ET' => 'ETB',
		'FI' => 'EUR',
		'FR' => 'EUR',
		'GB' => 'GBP',
		'GH' => 'USD',
		'GR' => 'EUR',
		'HK' => 'HKD',
		'HU' => 'HUF',
		'ID' => 'IDR',
		'IE' => 'EUR',
		'IN' => 'INR',
		'IQ' => 'USD',
		'IR' => 'USD',
		'IT' => 'EUR',
		'JO' => 'JOD',
		'JP' => 'JPY',
		'KE' => 'USD',
		'KR' => 'KRW',
		'KW' => 'KWD',
		'LB' => 'USD',
		'LK' => 'LKR',
		'LU' => 'EUR',
		'MA' => 'MAD',
		'MM' => 'USD',
		'MT' => 'EUR',
		'MU' => 'MUR',
		'MV' => 'USD',
		'MX' => 'MXN',
		'MY' => 'MYR',
		'MZ' => 'USD',
		'NA' => 'USD',
		'NG' => 'NGN',
		'NL' => 'EUR',
		'NO' => 'NOK',
		'NP' => 'USD',
		'NZ' => 'NZD',
		'OM' => 'OMR',
		'PA' => 'USD',
		'PH' => 'USD',
		'PK' => 'PKR',
		'PL' => 'PLN',
		'PT' => 'EUR',
		'QA' => 'QAR',
		'RO' => 'EUR',
		'RU' => 'RUB',
		'SA' => 'SAR',
		'SC' => 'USD',
		'SD' => 'USD',
		'SE' => 'SEK',
		'SG' => 'SGD',
		'SN' => 'XOF',
		'SS' => 'USD',
		'SX' => 'USD',
		'TH' => 'THB',
		'TN' => 'TND',
		'TR' => 'USD',
		'TW' => 'TWD',
		'TZ' => 'USD',
		'UA' => 'USD',
		'UG' => 'USD',
		'US' => 'USD',
		'VN' => 'VND',
		'ZA' => 'ZAR',
		'ZM' => 'USD',
		'ZW' => 'USD'
);

$aCountry = array();

$obj_XML = simplexml_load_string(file_get_contents('countries.xml', FILE_USE_INCLUDE_PATH));


foreach($given_data_from_vco as $country_iso => $country_currency)
{
	$obj_Country_Element = $obj_XML->xpath("/countries/country[@cca2='".$country_iso."']");
	if(is_null($obj_Country_Element) == false)
	{
		$countryNames = explode(",", current(current($obj_Country_Element)['name']));
		
		$aCountry[$country_iso] = "'$countryNames[0]'";
	}
	else 
	{
		echo "Country not present in xml iso : ".$country_iso." with currency :".$country_currency;
	}
}

$sql = "select id, currency, name from SYSTEM.COUNTRY_TBL 
where name IN (".implode(",",$aCountry).")
order by currency, id desc";

//echo $sql;exit;

$res = $_OBJ_DB->query($sql);

$currency_xml = "";

while ($RS = $_OBJ_DB->fetchName($res) )
{

	$currency_xml .= '&lt;xsl:when test="$country-id';
	$currency_xml .= "='".$RS["ID"]."'";
	$currency_xml .= '"';
	$currency_xml .= '&gt;'.$RS["CURRENCY"].'&lt;/xsl:when>&lt;!-- '.$RS["NAME"].' --&gt;  ';
	$currency_xml .= '<br/>';
}

header("Content-Type: text/html");

echo $currency_xml;
exit;

