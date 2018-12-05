<?php
require_once("inc/include.php");


$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

$sPushId = (string)$obj_DOM->notify->{'push-id'};
$sBody = (string)$obj_DOM->notify->{'body'}->{'message'};
$iPlatform = (integer)$obj_DOM->notify->{'platform'};

$bSendMessage = false;

// Instantiate object for holding the necessary information for connecting to GoMobile
$obj_ConnInfo = GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO);
// Instantiate client object for communicating with GoMobile
$obj_GoMobile = new GoMobileClient($obj_ConnInfo);

$iType = 11;
$sChannel = "PBL";
$sKeyword = "CPM";


if (empty($sPushId) === false) {

    $aRequestParams = (array)$obj_DOM->notify->body->params->children();
    $b = array();
    if($iPlatform == PLATFORM_ANDROID_DEVICE)
    {
        $b = array("data" => array("body" => utf8_encode($sBody) ) );

        foreach ($aRequestParams as $key => $value )
        {
            $b['data'][strtoupper($key)] = $value;
        }
    }
    elseif ($iPlatform == PLATFORM_IOS_DEVICE)
    {
        $b["aps"] = array("alert" => array("body" => utf8_encode($sBody) ),
            "sound" => "default",
            "action" => "notify");

       foreach ($aRequestParams as $key => $value )
        {
            $b['aps'][strtoupper($key)] = $value;
        }
        $b['ACTION'] = 5;
    }


    $obj_MsgInfo = GoMobileMessage::produceMessage($iType, $sChannel, $sKeyword, $sPushId, json_encode($b));

    $obj_MsgInfo->setDescription("Push-Notification from push service");
    //$obj_MsgInfo->setSender("CPM");

    $bSendMessage = true;
    // Send messages
    while ($bSendMessage === true && $iAttempts <= 3) {
        $iAttempts++;
        try {
            if ($obj_GoMobile->send($obj_MsgInfo) == 200) {
                $xml .= '<status code="' . $obj_MsgInfo->getReturnCodes() . '">Message successfully sent with ID: ' . $obj_MsgInfo->getGoMobileID() . '</status>';
            } // Error
            else {
                $xml .= '<status code="' . $obj_MsgInfo->getReturnCodes() . '">Message sending failed</status>';
            }
            $bSendMessage = false;
            break;
        } // Communication error, retry message sending
        catch (HTTPException $e) {
            sleep(pow(10, $iAttempts) );
        }
    }

    header("Content-Type: text/xml; charset=\"UTF-8\"");
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<root>';
    echo preg_replace('~\s*(<([^>]*)>[^<]*</\2>|<[^>]*>)\s*~', '$1', $xml);
    echo '</root>';
}
?>
