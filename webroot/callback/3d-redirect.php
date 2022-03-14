<?php
/**
 * This files contains the for the 3DS Callback component which handles acquirer authorization requests.
 * The file will complete the transaction for 3DS verification of the cardholder
 */

// Require Global Include File
require_once("../inc/include.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the End-User Account Factory Provider
require_once(sCLASS_PATH ."/customer_info.php");
// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for Capture component (for use with auto-capture functionality)
require_once(sCLASS_PATH ."/capture.php");
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_psp.php");
// Require specific Business logic for the CPM ACQUIRER component
require_once(sINTERFACE_PATH ."/cpm_acquirer.php");
// Require specific Business logic for the CPM GATEWAY component
require_once(sINTERFACE_PATH ."/cpm_gateway.php");
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");
// Require specific Business logic for the DSB PSP component
require_once(sCLASS_PATH ."/dsb.php");
// Require specific Business logic for the Emirates' Corporate Payment Gateway (CPG) component
require_once(sCLASS_PATH ."/cpg.php");
// Require specific Business logic for the Wirecard component
require_once(sCLASS_PATH ."/wirecard.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/dibs.php");
// Require specific Business logic for the Nets component
require_once(sCLASS_PATH ."/nets.php");
// Require specific Business logic for the mVault component
require_once(sCLASS_PATH ."/mvault.php");
// Require specific Business logic for the Amex component
require_once(sCLASS_PATH ."/amex.php");
// Require specific Business logic for the chase component
require_once(sCLASS_PATH ."/chase.php");
require_once(sCLASS_PATH . '/paymentSecureInfo.php');
// Require Data Class for Client Information
require_once(sCLASS_PATH ."/clientinfo.php");

/**
 * Input XML format
 *
<?xml version="1.0" encoding="UTF-8"?>
<root>
	<callback>
		<psp-config id="12">
			<name>CellpointMobileCOM</name>
		</psp-config>
		<transaction id="1825317" order-no="970-253176" external-id="8814395474257619">
			<amount country-id="100" currency="DKK">10000</amount>
			<card type-id="8">
				<card-number>411111*******4123</card-number>
				<expiry>
					<month>6</month>
					<year>16</year>
				</expiry>
				<token>31232121ddd</token>
			</card>
		</transaction>
		<status code="2000">17103%3A1111%3A6%2F2016</status>
	</callback>
</root>
 */


set_time_limit(600);
// Standard retry strategy connecting to the database has proven inadequate
$i = 0;
while ( ($_OBJ_DB instanceof RDB) === false && $i < 5)
{
	// Instantiate connection to the Database
	$_OBJ_DB = RDB::produceDatabase($aDB_CONN_INFO["mpoint"]);
	$i++;
}
$sRawXML = file_get_contents("php://input");
$obj_XML = simplexml_load_string($sRawXML);

$id = (integer)$obj_XML->{'threed-redirect'}->transaction["id"];
$xml = '';

$aStateId = array();

try
{
	$obj_TxnInfo = TxnInfo::produceInfo($id, $_OBJ_DB);
	$obj_TxnInfo->produceOrderConfig($_OBJ_DB);
	$iAccountValidation = $obj_TxnInfo->hasEitherState($_OBJ_DB,Constants::iPAYMENT_ACCOUNT_VALIDATED);
	// Intialise Text Translation Object
	$_OBJ_TXT = new api\classes\core\TranslateText(array(sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/global.txt", sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");

	$iStateID = (integer) $obj_XML->{'threed-redirect'}->status["code"];
    $iSubCodeID = (integer) $obj_XML->{'threed-redirect'}->status["sub-code"];

    $obj_PaymentProcessor = PaymentProcessor::produceConfig($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $obj_TxnInfo->getPSPID(), $aHTTP_CONN_INFO);
    $obj_mPoint = $obj_PaymentProcessor->getPSPInfo();
    $obj_PSPConfig = $obj_PaymentProcessor->getPSPConfig();

    $obj_ClientInfo = ClientInfo::produceInfo($obj_XML->{'threed-redirect'}->{'client-info'}, CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_XML->{'threed-redirect'}->{'client-info'}->mobile["country-id"]), $_SERVER['HTTP_X_FORWARDED_FOR']);

	$year = substr(strftime("%Y"), 0, 2);
	$sExpirydate =  $year.$obj_XML->{'threed-redirect'}->transaction->card->expiry->year ."-". $obj_XML->{'threed-redirect'}->transaction->card->expiry->month;
	// If transaction is in Account Validated i.e 1998 state no action to be done

    array_push($aStateId,$iStateID);
    $propertyValue = $obj_PSPConfig->getAdditionalProperties(Constants::iInternalProperty, '3DVERIFICATION');
    if ($obj_TxnInfo->hasEitherState($_OBJ_DB, array(Constants::iPAYMENT_3DS_SUCCESS_STATE, Constants::iPAYMENT_3DS_FAILURE_STATE)) === true) {
        $iStateID = Constants::iPAYMENT_3DS_DUPLICATE_STATE;
        $xml .= '<status code="' .$iStateID. '">Request already processed</status>';
    } else {
        //Log the incoming status code.
        $obj_mPoint->newMessage($obj_TxnInfo->getID(), $iStateID, $sRawXML);
        if($iSubCodeID > 0) { $obj_mPoint->newMessage($obj_TxnInfo->getID(), $iSubCodeID, ''); }

        if($obj_XML->{'threed-redirect'}->transaction->card->{'info-3d-secure'})
        {
            $paymentSecureInfo = PaymentSecureInfo::produceInfo($obj_XML->{'threed-redirect'}->transaction->card->{'info-3d-secure'},$obj_PSPConfig->getID(),$obj_TxnInfo->getID());
            if($paymentSecureInfo !== null) $obj_mPoint->storePaymentSecureInfo($paymentSecureInfo);

        }

        $aMpiRule = array();
        $bIsProceedAuth = false;

        if($obj_PSPConfig->getAdditionalProperties(Constants::iInternalProperty,"mpi_rule") !== false)
        {
            $aRules = $obj_PSPConfig->getAdditionalProperties(Constants::iInternalProperty);
            foreach ($aRules as $value)
            {
                if (strpos($value['key'], 'mpi_rule') !== false)
                {
                    $aMpiRule[] = $value['value'];
                }
            }
        }
        else if($obj_TxnInfo->getClientConfig()->getAdditionalProperties(Constants::iInternalProperty,"mpi_rule") !== false)
        {
            $aRules = $obj_TxnInfo->getClientConfig()->getAdditionalProperties(Constants::iInternalProperty);
            foreach ($aRules as $value)
            {
                if (strpos($value['key'], 'mpi_rule') !== false)
                {
                    $aMpiRule[] = $value['value'];
                }
            }
        }
        if(empty($aMpiRule) === false)
        {
            $bIsProceedAuth = $obj_mPoint->applyRule([$obj_XML],$aMpiRule);
        }

        if(($obj_PSPConfig->getProcessorType() === Constants::iPROCESSOR_TYPE_ACQUIRER || $obj_PSPConfig->getProcessorType() === Constants::iPROCESSOR_TYPE_PSP)&& ($propertyValue === 'mpi' || $obj_PSPConfig->isRouteFeatureEnabled(RouteFeatureType::eMPI)) && ($iStateID == Constants::iPAYMENT_3DS_SUCCESS_STATE || $bIsProceedAuth ===true))
        {


            if($iStateID == Constants::iPAYMENT_3DS_SUCCESS_STATE || $bIsProceedAuth === true)
            {

                $mvault = new MVault($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO['mvault']);

                $xmlString = "<card id='" . $obj_XML->{'threed-redirect'}->transaction->card["type-id"] . "'><token>" . $obj_TxnInfo->getToken() . "</token></card>";
                /* Reset the eua-id to contain txn-id which will be linked as external ref for the txn.
                This is only applicable for Acq flow with MPI */
                $obj_TxnInfo->setAccountID($obj_TxnInfo->getID());

                $obj_Elem = $mvault->getPaymentData($obj_PSPConfig, simplexml_load_string($xmlString));
                //var_dump($obj_Elem);die;
                $card_obj = simplexml_load_string($obj_Elem);
                $card_obj = $card_obj->{'payment-data'};
                $card_obj->card->cvc = base64_decode(strrev($obj_TxnInfo->getExternalID()) );
                $card_obj->card['type-id'] = $obj_XML->{'threed-redirect'}->transaction->card["type-id"];
                if (!isset($card_obj->card->{'info-3d-secure'}))
                {
                    $card_obj->card->addChild('info-3d-secure','');
                }
                if($obj_XML->{'threed-redirect'}->transaction->card->{'info-3d-secure'}->cryptogram)
                {
                    $cryptogram = $card_obj->card->{'info-3d-secure'}->addChild('cryptogram', $obj_XML->{'threed-redirect'}->transaction->card->{'info-3d-secure'}->cryptogram);
                    $cryptogram->addAttribute('eci', $obj_XML->{'threed-redirect'}->transaction->card->{'info-3d-secure'}->cryptogram['eci']);
                    $cryptogram->addAttribute('algorithm-id', $obj_XML->{'threed-redirect'}->transaction->card->{'info-3d-secure'}->cryptogram['algorithm-id']);
                    $cryptogram->addAttribute('xid', base64_encode((string)$obj_XML->{'threed-redirect'}->transaction['external-id']));
                }

                if(count($obj_XML->{'threed-redirect'}->transaction->card->address) > 0 && count($card_obj->card->address->state) === 0)
                {
                    $address = $card_obj->card->address;
                    foreach ($obj_XML->{'threed-redirect'}->transaction->card->address->attributes() as $name=>$value)
                    {
                        $address->addAttribute($name,$value);
                    }
                    foreach ($obj_XML->xpath('threed-redirect/transaction/card/address/*') as $item)
                    {
                        $node =$address->addChild($item->getName(),$item);
                        foreach ($item->attributes() as $name=>$value)
                        {
                            $node->addAttribute($name,$value);
                        }
                    }
                }
                if(count($obj_XML->{'threed-redirect'}->transaction->card->{'info-3d-secure'}->{'additional-data'}) > 0)
                {
                    $additionalData = $card_obj->card->{'info-3d-secure'}->addChild('additional-data');
                    foreach ($obj_XML->xpath('threed-redirect/transaction/card/info-3d-secure/additional-data/param') as $item)
                    {
                        $param = $additionalData->addChild('param',$item);
                        $param->addAttribute('name',$item['name']);
                    }
                }

                $sql = "UPDATE Log" . sSCHEMA_POSTFIX . ".Transaction_Tbl
                        SET extid=''
                        WHERE id = " . $obj_XML->{'threed-redirect'}->transaction['id'];
                //echo $sql ."\n";
                $_OBJ_DB->query($sql);
                $additionalTxnData = [];
                $additionalTxnData[0]['name'] = "eci";
                $additionalTxnData[0]['value'] = (string)$card_obj->card->{'info-3d-secure'}->cryptogram["eci"];
                $additionalTxnData[0]['type'] = 'Transaction';
                //Store xid in DB
                $additionalTxnData[1]['name'] = 'xid';
                $additionalTxnData[1]['value'] = base64_encode((string)$obj_XML->{'threed-redirect'}->transaction['external-id']);
                $additionalTxnData[1]['type'] = 'Transaction';
                $obj_TxnInfo->setAdditionalDetails($_OBJ_DB, $additionalTxnData,$obj_TxnInfo->getID());

                $obj_card = new Card($card_obj->card, $_OBJ_DB);
                $cardName = $obj_card->getCardName();
                if (empty($cardName) === false) {
                    $card_obj->card->card_name = $cardName;
                }

                $response = $obj_mPoint->authorize($obj_PSPConfig, $card_obj->card, $obj_ClientInfo);
                $code = $response->code;
                if ($code == "100")
                {
                    $xml .= '<status code="100">Payment Authorized Using Stored Card</status>';
                }
                else if($code == "2000") { $xml .= '<status code="2000">Payment authorized</status>'; }
                else if($code == "2009") { $xml .= '<status code="2009">Payment authorized and Card Details Stored.</status>'; }
                else
                {
                    $obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);

                    header("HTTP/1.1 502 Bad Gateway");

                    $xml .= '<status code="92">Authorization failed, '.$obj_PSPConfig->getName().' returned error: '. $code .'</status>';
                }
            }
            else
            {
                $sql = "UPDATE Log" . sSCHEMA_POSTFIX . ".Transaction_Tbl
                            SET extid=''
                            WHERE id = " . $obj_XML->{'threed-redirect'}->transaction['id'];
                //echo $sql ."\n";
                $_OBJ_DB->query($sql);

                $obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_REJECTED_STATE, '');
                $obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iAUTHENTICATION_DECLINED_SUB_CODE, '');

                $obj_mPoint->updateSessionState($iStateID,$obj_TxnInfo->getPSPID(),$obj_TxnInfo->getAmount(),"",0,null,"",$obj_TxnInfo->getClientConfig()->getSurePayConfig($_OBJ_DB),0,null,$iSubCodeID);

                $xml .= '<status code="'.$iStateID.'">3D verification status : '.$obj_XML->{'threed-redirect'}->status.'</status>';
            }
        }
        else
        {
            $obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_REJECTED_STATE, '');
            $obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iAUTHENTICATION_DECLINED_SUB_CODE, '');

            $obj_mPoint->updateSessionState($iStateID,$obj_TxnInfo->getPSPID(),$obj_TxnInfo->getAmount(),"",0,null,"",$obj_TxnInfo->getClientConfig()->getSurePayConfig($_OBJ_DB),0,null,$iSubCodeID);

            $status = $obj_XML->{'threed-redirect'}->{'status'};
            if (strlen($status) >0 == false){ $status .= 'Transaction Declined'; };
            $xml .= '<status code="2010">'.$status.'</status>';
        }
    }
}
catch (TxnInfoException $e)
{
	header("HTTP/1.1 500 Internal Server Error");
	$xml .= '<status code="'. $e->getCode() .'">'. htmlspecialchars($e->getMessage(), ENT_NOQUOTES). '</status>';
	trigger_error($e->getMessage() ."\n". $HTTP_RAW_POST_DATA, E_USER_WARNING);
}
catch (CallbackException $e)
{
	header("HTTP/1.1 500 Internal Server Error");
	$xml .= '<status code="'. $e->getCode() .'">'. htmlspecialchars($e->getMessage(), ENT_NOQUOTES). '</status>';
	trigger_error($e->getMessage() ."\n". $HTTP_RAW_POST_DATA, E_USER_WARNING);
}
catch (HTTPException $e)
{
    header("HTTP/1.1 500 Internal Server Error");
    $xml .= '<status code="'. $e->getCode() .'">'. htmlspecialchars($e->getMessage(), ENT_NOQUOTES). '</status>';
    trigger_error($e->getMessage() ."\n". $HTTP_RAW_POST_DATA, E_USER_WARNING);
}

header("Content-Type: text/xml; charset=\"UTF-8\"");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';

