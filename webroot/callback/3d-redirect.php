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
// Require specific Business logic for the Adyen component
require_once(sCLASS_PATH ."/adyen.php");
// Require specific Business logic for the DSB PSP component
require_once(sCLASS_PATH ."/dsb.php");
// Require specific Business logic for the VISA checkout component
require_once(sCLASS_PATH ."/visacheckout.php");
// Require specific Business logic for the Apple Pay component
require_once(sCLASS_PATH ."/applepay.php");
// Require specific Business logic for the Emirates' Corporate Payment Gateway (CPG) component
require_once(sCLASS_PATH ."/cpg.php");
// Require specific Business logic for the AMEX Express Checkout component
require_once(sCLASS_PATH ."/amexexpresscheckout.php");
// Require specific Business logic for the Master Pass component
require_once(sCLASS_PATH ."/masterpass.php");
// Require specific Business logic for the Wirecard component
require_once(sCLASS_PATH ."/wirecard.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/dibs.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/securetrading.php");
// Require specific Business logic for the CCAvenue component
require_once(sCLASS_PATH ."/ccavenue.php");
// Require specific Business logic for the PayPal component
require_once(sCLASS_PATH ."/paypal.php");
// Require specific Business logic for the PayFort component
require_once(sCLASS_PATH ."/payfort.php");
// Require specific Business logic for the DataCash component
require_once(sCLASS_PATH ."/datacash.php");
// Require specific Business logic for the Mada Mpgs component
require_once(sCLASS_PATH ."/mada_mpgs.php");
// Require specific Business logic for the 2C2P component
require_once(sCLASS_PATH ."/ccpp.php");
// Require specific Business logic for the MayBank component
require_once(sCLASS_PATH ."/maybank.php");
// Require specific Business logic for the PublicBank component
require_once(sCLASS_PATH ."/publicbank.php");
// Require specific Business logic for the AliPay component
require_once(sCLASS_PATH ."/alipay.php");
require_once(sCLASS_PATH ."/alipay_chinese.php");
// Require specific Business logic for the POLi component
require_once(sCLASS_PATH ."/poli.php");
// Require specific Business logic for the QIWI component
require_once(sCLASS_PATH ."/qiwi.php");
// Require specific Business logic for the Nets component
require_once(sCLASS_PATH ."/nets.php");
// Require specific Business logic for the Klarna component
require_once(sCLASS_PATH ."/klarna.php");
// Require specific Business logic for the mVault component
require_once(sCLASS_PATH ."/mvault.php");
// Require specific Business logic for the Trustly component
require_once(sCLASS_PATH ."/trustly.php");
// Require specific Business logic for the 2C2P-ALC component
require_once(sCLASS_PATH ."/ccpp_alc.php");
// Require specific Business logic for the paytabs component
require_once(sCLASS_PATH ."/paytabs.php");
// Require specific Business logic for the citcon component
require_once(sCLASS_PATH ."/citcon.php");
// Require specific Business logic for the PPRO component
require_once(sCLASS_PATH ."/ppro.php");
// Require specific Business logic for the Amex component
require_once(sCLASS_PATH ."/amex.php");
// Require specific Business logic for the chase component
require_once(sCLASS_PATH ."/chase.php");
// Require specific Business logic for the Cielo component
require_once(sCLASS_PATH ."/cielo.php");
// Require specific Business logic for the VeriTrans4G component
require_once(sCLASS_PATH ."/psp/veritrans4g.php");
// Require specific Business logic for the DragonPay component
require_once(sCLASS_PATH ."/aggregator/dragonpay.php");
// Require specific Business logic for the cellulant component
require_once(sCLASS_PATH ."/cellulant.php");

// Require specific Business logic for the FirstData component
require_once(sCLASS_PATH ."/first-data.php");
// Require specific Business logic for the CYBS ie. Global Payments component
require_once(sCLASS_PATH ."/global-payments.php");
require_once(sCLASS_PATH ."/cybersource.php");


// Require specific Business logic for the WorldPay component
require_once(sCLASS_PATH . "/worldpay.php");

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
	$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/global.txt", sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
	$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), intval($obj_XML->{'threed-redirect'}->{"psp-config"}["id"]) );

	$iStateID = (integer) $obj_XML->{'threed-redirect'}->status["code"];

    $obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), intval($obj_TxnInfo->getPSPID()) );

    $obj_mPoint = Callback::producePSP($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO, $obj_PSPConfig);

    $obj_ClientInfo = ClientInfo::produceInfo($obj_XML->{'threed-redirect'}->{'client-info'}, CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_XML->{'threed-redirect'}->{'client-info'}->mobile["country-id"]), $_SERVER['HTTP_X_FORWARDED_FOR']);

	$year = substr(strftime("%Y"), 0, 2);
	$sExpirydate =  $year.$obj_XML->{'threed-redirect'}->transaction->card->expiry->year ."-". $obj_XML->{'threed-redirect'}->transaction->card->expiry->month;
	// If transaction is in Account Validated i.e 1998 state no action to be done

    array_push($aStateId,$iStateID);
    $propertyValue = $obj_PSPConfig->getAdditionalProperties(Constants::iInternalProperty, '3DVERIFICATION');
    //Log the incoming status code.
    $obj_mPoint->newMessage($obj_TxnInfo->getID(), $iStateID, $sRawXML);
    if(($obj_PSPConfig->getProcessorType() === Constants::iPROCESSOR_TYPE_ACQUIRER || $obj_PSPConfig->getProcessorType() === Constants::iPROCESSOR_TYPE_PSP)&& $propertyValue === 'mpi' && $iStateID == Constants::iPAYMENT_3DS_SUCCESS_STATE) {

        if($iStateID == Constants::iPAYMENT_3DS_SUCCESS_STATE)
        {
            $aMpiRule = array();
            $bIsSkipAuth = false;
            if($obj_XML->{'threed-redirect'}->transaction->card->{'info-3d-secure'})
            {
                $aPaymentSecureData = array();
                $aPaymentSecureData['eci'] = (string)$obj_XML->{'threed-redirect'}->transaction->card->{'info-3d-secure'}->{'cryptogram'}["eci"];
                $aPaymentSecureData['cavv'] = (string)$obj_XML->{'threed-redirect'}->transaction->card->{'info-3d-secure'}->{'cryptogram'};
                $aPaymentSecureData['cavvAlgorithm'] = (string)$obj_XML->{'threed-redirect'}->transaction->card->{'info-3d-secure'}->{'cryptogram'}["algorithm-id"];


                for ($j=0; $j<count($obj_XML->{'threed-redirect'}->transaction->card->{'info-3d-secure'}->{'additional-data'}->param); $j++ )
                {
                    $sKey = (string)$obj_XML->{'threed-redirect'}->transaction->card->{'info-3d-secure'}->{'additional-data'}->param[$j]['name'];
                    $sValue =(string) $obj_XML->{'threed-redirect'}->transaction->card->{'info-3d-secure'}->{'additional-data'}->param[$j];
                    $aPaymentSecureData[$sKey] = $sValue;
                }
                $obj_mPoint->storePaymentSecureInfo($obj_TxnInfo->getID(),$aPaymentSecureData);
            }
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
                $bIsSkipAuth = $obj_mPoint->applyRule($obj_XML,$aMpiRule);
            }

            if($bIsSkipAuth === false)
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
                $cryptogram = $card_obj->card->{'info-3d-secure'}->addChild('cryptogram', $obj_XML->{'threed-redirect'}->transaction->card->{'info-3d-secure'}->cryptogram);
                $cryptogram->addAttribute('eci', $obj_XML->{'threed-redirect'}->transaction->card->{'info-3d-secure'}->cryptogram['eci']);
                $cryptogram->addAttribute('algorithm-id', $obj_XML->{'threed-redirect'}->transaction->card->{'info-3d-secure'}->cryptogram['algorithm-id']);
                $cryptogram->addAttribute('xid', base64_encode((string)$obj_XML->{'threed-redirect'}->transaction['external-id']));
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


                $code = $obj_mPoint->authorize($obj_PSPConfig, $card_obj->card, $obj_ClientInfo);

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
                $obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_3DS_SUCCESS_AUTH_NOT_ATTEMPTED_STATE,'3DS authentication successfully completed and authorization not attempted due to rule matched');
                $xml .= '<status code="'.Constants::iPAYMENT_3DS_SUCCESS_AUTH_NOT_ATTEMPTED_STATE.'">3DS authentication successfully completed and authorization not attempted
	                     due to rule matched</status>';
            }

        }
        else
        {
            $sql = "UPDATE Log" . sSCHEMA_POSTFIX . ".Transaction_Tbl
                            SET extid=''
                            WHERE id = " . $obj_XML->{'threed-redirect'}->transaction['id'];
            //echo $sql ."\n";
            $_OBJ_DB->query($sql);
            $xml .= '<status code="'.$iStateID.'">3D verification status : '.$obj_XML->{'threed-redirect'}->status.'</status>';
        }
    }
    else
    {
             $status = $obj_XML->{'threed-redirect'}->{'status'};
        	 if (strlen($status) >0 == false){ $status .= 'Transaction Declined'; };
        	 $xml .= '<status code="2010">'.$status.'</status>';
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

