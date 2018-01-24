<?php
/***
 * This cron can be executed by using below command as example
 *  lynx -dump http://mpoint.sit.cellpointmobile.com/internal/add_transaction-data.php?client-account=10047-100098 >> /var/log/cpm/mPoint/notify.log
 */

require_once("../inc/include.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the Mobile Web module
require_once(sCLASS_PATH ."/mobile_web.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require Data Class for Client Information
require_once(sCLASS_PATH ."/clientinfo.php");

$totalRecordsToAdd = 17;

if(isset($_REQUEST['client-account']) == true)
{
	$requiredParameters = $_REQUEST['client-account'];
	
	if(isset($requiredParameters) == true)
	{
		$sClientAccount = $requiredParameters;
				
		$aPaymentMethods = array(Constants::iVISA_CARD, Constants::iMASTERCARD, Constants::iAMEX_CARD, Constants::iDANKORT_CARD);
		
		$aPsp = array(Constants::iDIBS_PSP, Constants::iWIRE_CARD_PSP, Constants::iWORLDPAY_PSP);
		
		if(strpos($sClientAccount, ",") > 0)
		{
			$aClientAccounts =  explode(",", $sClientAccount);
		} else {$aClientAccounts = array($sClientAccount);}
		
		
		foreach($aClientAccounts as $clientAccount)
		{
			list($iClientId, $iAccountId) = explode("-", $clientAccount); 
			
			// Validate basic information
			$code = Validate::valBasic($_OBJ_DB, (integer) $iClientId, (integer) $iAccountId);
			if ($code == 100)
			{
				$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $iClientId, (integer) $iAccountId);
				
				$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, 100);
				if ( ($obj_CountryConfig instanceof CountryConfig) === false || $obj_CountryConfig->getID() < 1) { $obj_CountryConfig = $obj_ClientConfig->getCountryConfig(); }
				
				
				for($i = 1; $i <= $totalRecordsToAdd; $i++)
				{
					$obj_mPoint = new MobileWeb($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);
					$iTXNID = $obj_mPoint->newTransaction(Constants::iPURCHASE_VIA_APP);
					
					try
					{
						
						// Update Transaction State
						$obj_mPoint->newMessage($iTXNID, Constants::iINPUT_VALID_STATE, "Initialised from script");
					
						$data['typeid'] = Constants::iPURCHASE_VIA_APP;
						$data['amount'] = (float) 1025;
						$data['country-config'] = $obj_CountryConfig;
						
						$data['description'] = (string) "From cron script.";
						$data['gomobileid'] = -1;
						$data['orderid'] = (string) "SCRIPT-".$iTXNID;
						$data['mobile'] = 30206172;
						$data['email'] = 'jona@oismail.com';
						$data['customer-ref'] = 'Test-User';
						
						$obj_TxnInfo = TxnInfo::produceInfo($iTXNID,$_OBJ_DB, $obj_ClientConfig, $data);
						
						// Update Transaction Log
						$obj_mPoint->logTransaction($obj_TxnInfo);
						
					}catch(Exception $e)
					{
						echo $e->getMessage();
					}
				}
				
				//Authorize transactions
				$sql = "SELECT TXNID FROM Log".sSCHEMA_POSTFIX.".Message_Tbl ORDER BY id DESC LIMIT 7";
				
				//echo $sql ."\n";
				$res = $_OBJ_DB->query($sql);
				
				while ($RS = $_OBJ_DB->fetchName($res) )
				{
					if(empty($RS['TXNID']) === false)
					{
												
						$obj_mPoint->newMessage($RS['TXNID'], Constants::iPAYMENT_WITH_ACCOUNT_STATE, "Payment with storecard from script");
						
						$obj_mPoint->newMessage($RS['TXNID'], Constants::iPAYMENT_ACCEPTED_STATE, "Authorized from script");
						
						$pspid = $aPsp[array_rand($aPsp)];
		
						$cardid = $aPaymentMethods[array_rand($aPaymentMethods)];
						
						$updateSql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
						SET pspid = ". intval($pspid).", cardid = ". intval($cardid)."
						WHERE id = ". $RS['TXNID'];
				
						//echo $sql ."\n";
						$_OBJ_DB->query($updateSql);				
					}
				}
				
				
				//Captured transactions
				$sql = "SELECT TXNID FROM Log".sSCHEMA_POSTFIX.".Message_Tbl WHERE stateid = ".Constants::iPAYMENT_WITH_ACCOUNT_STATE." ORDER BY id DESC LIMIT 3";
				
				//echo $sql ."\n";
				$res = $_OBJ_DB->query($sql);
				
				$iCount = 0;
				while ($RS = $_OBJ_DB->fetchName($res) )
				{
					if(empty($RS['TXNID']) === false)
					{
						$obj_mPoint->newMessage($RS['TXNID'], Constants::iPAYMENT_CAPTURED_STATE, "Captured from script");
						
					}
				}		
				
				//refunded transactions
				$sql = "SELECT TXNID FROM Log".sSCHEMA_POSTFIX.".Message_Tbl WHERE stateid = ".Constants::iPAYMENT_CAPTURED_STATE." ORDER BY id DESC LIMIT 1";
				
				//echo $sql ."\n";
				$res = $_OBJ_DB->query($sql);
				
				$iCount = 0;
				while ($RS = $_OBJ_DB->fetchName($res) )
				{
					if(empty($RS['TXNID']) === false)
					{
						$obj_mPoint->newMessage($RS['TXNID'], Constants::iPAYMENT_REFUNDED_STATE, "Refunded from script");
				
					}
				}
				
				//refunded transactions
				$sql = "SELECT TXNID FROM Log".sSCHEMA_POSTFIX.".Message_Tbl WHERE stateid = ".Constants::iPAYMENT_CAPTURED_STATE." ORDER BY id DESC LIMIT 1";
				
				//echo $sql ."\n";
				$res = $_OBJ_DB->query($sql);
				
				$iCount = 0;
				while ($RS = $_OBJ_DB->fetchName($res) )
				{
					if(empty($RS['TXNID']) === false)
					{
						$obj_mPoint->newMessage($RS['TXNID'], Constants::iPAYMENT_DECLINED_STATE, "Declined from script");
				
					}
				}
				
				//Cancelled transactions
				$sql = "SELECT TXNID FROM Log".sSCHEMA_POSTFIX.".Message_Tbl WHERE stateid = ".Constants::iPAYMENT_WITH_ACCOUNT_STATE." ORDER BY id DESC LIMIT 1";
				
				//echo $sql ."\n";
				$res = $_OBJ_DB->query($sql);
				
				$iCount = 0;
				while ($RS = $_OBJ_DB->fetchName($res) )
				{
					if(empty($RS['TXNID']) === false)
					{
						$obj_mPoint->newMessage($RS['TXNID'], Constants::iPAYMENT_CANCELLED_STATE, "Cancelled from script");
				
					}
				}
		
			} else { trigger_error("Improper clientid or accountid : ".$iClientId."::".$iAccountId, E_USER_WARNING); }
			
		}
	} else { echo "Please pass client-account=params .\n"; }
} else { echo "Please pass client-account=params .\n"; }