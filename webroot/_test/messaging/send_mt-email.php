<?php
require_once("inc/include.php");


$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

$xml = $h = '';

$sBody = (string)$obj_DOM->notify->{'body'}->{'message'};
$sFromEmail = (string)$obj_DOM->notify->{'from'};
$sRecipientEmail = (string)$obj_DOM->notify->{'to'};
$sSubject = (string)$obj_DOM->notify->{'body'}->{'subject'};

$h = "Reply-To:" . $sFromEmail . SMTPClient::CRLF;
$h .= "Content-Type: text/plain; charset=\"UTF-8\"" . SMTPClient::CRLF;
$h .= "MIME-Version: 1.0" . SMTPClient::CRLF;
$obj_EmailMessage = new EMailMessage($sRecipientEmail, $sSubject, utf8_encode($sBody), "text/plain", "UTF-8", $h);

$obj_ConnInfo = new SMTPConnInfo($sFromEmail, "CellPoint Mobile Support", "SMTP", "", 0, 20, "", "");
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