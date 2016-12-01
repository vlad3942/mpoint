<?php
// Require include file for including all Shared and General APIs
require_once("../inc/include.php");

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


$sql = "select id, currency, name from SYSTEM.COUNTRY_TBL 
where currency IN ('AED','USD','AOA','ARS','EUR','AUD','USD','BDT','EUR','BHD','USD','BRL','USD','CAD','CHF','XOF','CNY','EUR','CZK','EUR','USD','DKK','DZD','EUR','EGP','EUR','ETB','EUR','EUR','GBP','USD','EUR','HKD','HUF','IDR','EUR','INR','USD','USD','EUR','JOD','JPY','USD','KRW','KWD','USD','LKR','EUR','MAD','USD','EUR','MUR','USD','MXN','MYR','USD','USD','NGN','EUR','NOK','USD','NZD','OMR','USD','USD','PKR','PLN','EUR','QAR','EUR','RUB','SAR','USD','USD','SEK','SGD','XOF','USD','USD','THB','TND','USD','TWD','USD','USD','USD','USD','VND','ZAR','USD','USD')
order by currency, id desc";

//echo $sql;

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

