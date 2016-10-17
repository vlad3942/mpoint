<?php
// Require include file for including all Shared and General APIs
require_once("../inc/include.php");

require_once(sAPI_CLASS_PATH ."simpledom.php");

// Require Interface for defining how the Database is accessed
//require_once(sAPI_INTERFACE_PATH ."database.php");

// Require Database Abstraction API
//require_once(sAPI_CLASS_PATH ."database.php");

$aDB_CONN_INFO["mpoint"]["host"] = "localhost";
$aDB_CONN_INFO["mpoint"]["port"] = 5001;
$aDB_CONN_INFO["mpoint"]["path"] = "mpoint";
$aDB_CONN_INFO["mpoint"]["username"] = "mpoint";
$aDB_CONN_INFO["mpoint"]["password"] = "mpoint";
$aDB_CONN_INFO["mpoint"]["charset"] = "UTF8"; 

$_OBJ_DB = RDB::produceDatabase($aDB_CONN_INFO["mpoint"]);

$obj_XML = simplexml_load_string(file_get_contents('currency_code_list.xml', FILE_USE_INCLUDE_PATH));

/* echo "<pre/>";
print_r($obj_XML);exit;  */

header("Content-Type: text/html");

$data_not_found = "";


$idList = array();

for ($i=0; $i<count($obj_XML->CcyTbl->CcyNtry); $i++)
{
	$obj_Children_XML = $obj_XML->CcyTbl->CcyNtry[$i];
/* 	
	echo "<pre/>";
	print_r($obj_Children_XML); */
	
	$decimals = intval($obj_Children_XML->CcyMnrUnts);
	
	if($decimals > 0)
	{
		$country_name = strtolower($obj_Children_XML->CtryNm);
		
		$currency_code = strtolower($obj_Children_XML->Ccy);
	
		$sql = "select id, decimals from SYSTEM.COUNTRY_TBL where LOWER(name) = '".$country_name."' and LOWER(currency) = '".$currency_code."'";
		
		//echo $sql."<br/>";
		
		$RS = $_OBJ_DB->getName($sql);
		$id = intval($RS["ID"]);
		
		if($id > 0)
		{
			$idList[] = $id;
			
		 	$currency_xml .= "UPDATE System_OWNR.COUNTRY_TBL SET decimals = ".intval($RS["DECIMALS"])." WHERE id = ".$id.";";
		 	
		 	$currency_xml .= "<br/>";
		 	
		} else { $data_not_found .= "No data found for country : ".$country_name." and currency: ".$currency_code."<br/>"; }
	}

	
}

//echo "Id list : ".implode(",", $idList);

echo "Update queries : <br/>";
echo $currency_xml;
echo "<br/>";
echo $data_not_found;
exit;