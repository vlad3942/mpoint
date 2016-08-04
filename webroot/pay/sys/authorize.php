<?php
// Require Global Include File
require_once("../../inc/include.php");

require_once(sAPI_CLASS_PATH ."simpledom.php");

// Require data class for Payment Service Provider Configurations
require_once(sCLASS_PATH ."/pspconfig.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");

// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_psp.php");

// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/dibs.php");
// Require specific Business logic for the WannaFind component
require_once(sCLASS_PATH ."/wannafind.php");
// Require specific Business logic for the WorldPay component
require_once(sCLASS_PATH ."/worldpay.php");
// Require specific Business logic for the wirecard component
require_once(sCLASS_PATH ."/wirecard.php");
// Require specific Business logic for the datacash component
require_once(sCLASS_PATH ."/datacash.php");
// Require specific Business logic for the globalcollect component
require_once(sCLASS_PATH ."/globalcollect.php");

// Require Business logic for the Select Credit Card component
require_once(sCLASS_PATH ."/credit_card.php");

$aMsgCds = array();
$msg = "";

$obj_Validator = new Validate($_SESSION['obj_TxnInfo']->getCountryConfig() );

$currentMonthDate = date('m/y');
$givenDate = $_POST['expiry-month'].'/'.$_POST['expiry-year'];


if(preg_match('/^\\d{2}\\/\\d{2}$/', $givenDate) == 0 || $givenDate < $currentMonthDate) { $aMsgCds[] = 20; }


if( count($_POST['cardnumber']) == 0 || (count($_POST['cardnumber']) > 0 && $obj_Validator->valCardNumber($_POST['cardnumber']) != 10)
) { $aMsgCds[] = 25; }

if (count($aMsgCds) == 0)
{
	$msg = 99;
	
	$obj_mPoint = new CreditCard($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo'], $_SESSION['obj_UA']);
	
	$obj_mPoint->newMessage($_SESSION['obj_TxnInfo']->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE, "");
	
	$obj_XML = simplexml_load_string($obj_mPoint->getCards($_SESSION['obj_TxnInfo']->getAmount() ) );
	$obj_XML = $obj_XML->xpath("item[@id = ". intval($_POST['cardtype']) ." and @pspid = ".intval($_POST['pspid'])."]");
	$obj_XML = $obj_XML[0];
	
	$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $_SESSION['obj_TxnInfo']->getClientConfig()->getAccountConfig()->getClientID(),
			$_SESSION['obj_TxnInfo']->getClientConfig()->getAccountConfig()->getID(), intval($_POST['pspid']));
	
	$obj_mPoint = Callback::producePSP($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo'], $aHTTP_CONN_INFO, $obj_PSPConfig);
	
	$card_XML = '<card type-id="'.intval($_POST['cardtype']).'">
		        <amount country-id="'.$_SESSION['obj_TxnInfo']->getCountryConfig()->getID().'">'.$_SESSION['obj_TxnInfo']->getAmount().'</amount>
		        <card-holder-name>'.$_POST['cardholdername'].'</card-holder-name>
		         <card-number>'.$_POST['cardnumber'].'</card-number>
		         <expiry>'.$givenDate.'</expiry>
		         <cvc>'.$_POST['cvv'].'</cvc>
		      </card>';
	
	$obj_Card = simplexml_load_string($card_XML);
	
	try 
	{
		$code = $obj_mPoint->authTicket($obj_PSPConfig, $obj_Card);
	}
	catch(Exception $e)
	{
		$msg = 59;
	}
	
	if ($code == 100)
	{
		$url = "http://". $_SERVER['SERVER_NAME'] ."/pay/accept.php?mpoint-id=". $_SESSION['obj_TxnInfo']->getID() ."&". session_name() ."=". session_id();
	}
	else
	{
		$obj_mPoint->delMessage($_SESSION['obj_TxnInfo']->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
		
		$url = "http://". $_SERVER['SERVER_NAME'] ."/pay/card.php?mpoint-id=". $_SESSION['obj_TxnInfo']->getID() ."&". session_name() ."=". session_id() ."&msg=".$msg;
	}
	
	header("location: ". $url);
	exit;
} 
else
{
		
	if (isset($sPath) === false) { $sPath = "pay/card.php?"; }
	for ($i=0; $i<count($aMsgCds); $i++)
	{
		$msg .= "&msg=". $aMsgCds[$i];
	}

	header("location: http://". $_SERVER['HTTP_HOST'] ."/". $sPath . session_name() ."=". session_id() . $msg);
	exit;
}