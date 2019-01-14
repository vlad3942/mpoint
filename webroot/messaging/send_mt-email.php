<?php
require_once("inc/include.php");

$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

$actual_host = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

$xml = $h = '';

$client = (integer)$obj_DOM->notify[0]["client-id"];
if (empty($client) === true || !file_get_contents($actual_host . '/messaging/template/' . $client . '/email.html')) {
    $client = 'default';
}

if (isset($obj_DOM->notify->{'body'}->{'assets'}) === true && isset($obj_DOM->notify->{'body'}->{'assets'}->{'banner-image'}) === true )
{
    $sBannerImage = (string)$obj_DOM->notify->{'body'}->{'assets'}->{'banner-image'};
}
else
{
    $sBannerImage =  $actual_host.'/messaging/template/'.$client.'/assets/img/banner.png';
}

$sLogo = $actual_host.'/messaging/template/'.$client.'/assets/img/logo.jpg';
$sCssUrl = $actual_host.'/messaging/template/'.$client.'/assets/css/style.css';

$sPaymentURL = (string)$obj_DOM->notify->{'body'}->{'message'};

preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $sPaymentURL, $match);

$sURL = $match[0][0];

if(empty ($sURL) === false )
{
    $sMessageText = trim(substr($sPaymentURL, 0, strpos($sPaymentURL, $sURL) ) );
}

// Input string
$sHtmlData  = file_get_contents($actual_host.'/messaging/template/'.$client.'/email.html');

// Array containing search string
$aSearchVal = array("{CSS URL}","{LOGO IMAGE}", "{BANNER IMAGE}", "{MESSAGE TEXT}", "{PAY NOW URL}");

// Array containing replace string from search string
$aReplaceVal = array($sCssUrl, $sLogo, $sBannerImage, $sMessageText, $sURL);

// Function to replace string
$sBody = str_replace($aSearchVal, $aReplaceVal, $sHtmlData);


$sFromEmail = (string)$obj_DOM->notify->{'from'};
$sRecipientEmail = (string)$obj_DOM->notify->{'to'};
$sSubject = (string)$obj_DOM->notify->{'body'}->{'subject'};

$h = "Reply-To:" . $sFromEmail . SMTPClient::CRLF;
$h .= "Content-Type: text/plain; charset=\"UTF-8\"" . SMTPClient::CRLF;
$h .= "MIME-Version: 1.0" . SMTPClient::CRLF;
$obj_EmailMessage = new EMailMessage($sRecipientEmail, $sSubject, utf8_encode($sBody), "text/html", "UTF-8", $h);

$obj_ConnInfo = new SMTPConnInfo($sFromEmail, "CellPoint Mobile Support", "tcp", "localhost", 25, 20, "", "");
$obj_SMTP = new SMTPClient($obj_ConnInfo);
$code = $obj_SMTP->mail($obj_EmailMessage);


if ($obj_EmailMessage->getCode() == SMTPClient::iMAIL_SUCCESSFULLY_SENT_STATE)
{
    $xml .= '<status code="' . $obj_EmailMessage->getCode() . '">Message successfully sent </status>';
}
else
{
    $xml .= '<status code="' . $obj_EmailMessage->getCode() . '">Message sending failed</status>';
}

header("Content-Type: text/xml; charset=\"UTF-8\"");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo preg_replace('~\s*(<([^>]*)>[^<]*</\2>|<[^>]*>)\s*~', '$1', $xml);
echo '</root>';
?>