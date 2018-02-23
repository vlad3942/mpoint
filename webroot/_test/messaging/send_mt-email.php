<?php
require_once("inc/include.php");


$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

$actual_host = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

$xml = $h = '';

$sPaymentURL = (string)$obj_DOM->notify->{'body'}->{'message'};

preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $sPaymentURL, $match);

$sURL = $match[0][0];



$sBody = '
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Payment Notification</title> 

  <style type="text/css">
    .ReadMsgBody{width:100%;} .ExternalClass{width:100%;} /* Force Hotmail to display emails at full width */
    .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} /* Force Hotmail to display normal line spacing */
    body, table, td, p, a, li, blockquote{-webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;} /* Prevent WebKit and Windows mobile changing default text sizes */
    table, td{mso-table-lspace:0pt; mso-table-rspace:0pt;} /* Remove spacing between tables in Outlook 2007 and up */
    td ul li {font-size: 16px;}
    body{margin:0; padding:0;}
    img{max-width:100%;border:0;line-height:100%;outline:none;text-decoration:none;}
    table{border-collapse:collapse !important;}
    .content {width: 100%; max-width: 600px;}
    .content img { height: auto; min-height: 1px; }
    #bodyTable{margin:0; padding:0; width:100% !important;}
    #bodyCell{margin:0; padding:0;}
    #bodyCellFooter{margin:0; padding:0; width:100% !important;padding-top:39px;padding-bottom:15px;}
    body {margin: 0; padding: 0; min-width: 100%!important;}

    #templateContainerFootBrd{
      border-bottom:1px solid #e2e2e2;
      border-left:1px solid #e2e2e2;
      border-right:1px solid #e2e2e2;
      border-radius: 0 0 4px 4px;
      background-clip: padding-box;
      border-spacing: 0;
      height: 10px;
      width:100% !important;
    }
    #templateContainer{
      border-top:1px solid #e2e2e2;
      border-left:1px solid #e2e2e2;
      border-right:1px solid #e2e2e2;
      border-radius: 4px 4px 0 0 ;
      background-clip: padding-box;
      border-spacing: 0;
    }
    #templateContainerMiddle {
      border-left:1px solid #e2e2e2;
      border-right:1px solid #e2e2e2;
    }
    #templateContainerMiddleBtm {
      border-left:1px solid #e2e2e2;
      border-right:1px solid #e2e2e2;
      border-bottom:1px solid #e2e2e2;
      border-radius: 0 0 4px 4px;
      background-clip: padding-box;
      border-spacing: 0;
    }

    h2{
      color:#2e2e2e;
      display:block;
      font-family:Helvetica;
      font-size:22px;
      line-height:1.455em;
      font-style:normal;
      font-weight:normal;
      letter-spacing:normal;
      margin-top:0;
      margin-right:0;
      margin-bottom:15px;
      margin-left:0;
      text-align:left;
    }

    h5{
      color:#545454;
      display:block;
      font-family:Helvetica;
      font-size:13px;
      line-height:1.538em;
      font-style:normal;
      font-weight:normal;
      letter-spacing:normal;
      margin-top:0;
      margin-right:0;
      margin-bottom:15px;
      margin-left:0;
      text-align:left;
    }


    h6{
      color:#545454;
      display:block;
      font-family:Helvetica;
      font-size:12px;
      line-height:2.000em;
      font-style:normal;
      font-weight:normal;
      letter-spacing:normal;
      margin-top:0;
      margin-right:0;
      margin-bottom:15px;
      margin-left:0;
      text-align:left;
    }

    p {
      color:#545454;
      display:block;
      font-family:Helvetica;
      font-size:16px;
      line-height:1.500em;
      font-style:normal;
      font-weight:normal;
      letter-spacing:normal;
      margin-top:0;
      margin-right:0;
      margin-bottom:15px;
      margin-left:0;
      text-align:left;
    }


    .unSubContent h6 {
      color: #a1a1a1;
      font-size: 12px;
      line-height: 1.5em;
      margin-bottom: 0;
    }

    .bodyContent{
      color:#505050;
      font-family:Helvetica;
      font-size:14px;
      line-height:150%;
      padding-top:3.143em;
      padding-right:3.5em;
      padding-left:3.5em;
      padding-bottom:0.714em;
      text-align:left;
    }
    .bodyContentImage {
      color:#505050;
      font-family:Helvetica;
      font-size:14px;
      line-height:150%;
      padding-top:0;
      padding-right:3.571em;
      padding-left:3.571em;
      padding-bottom:2em;
      text-align:left;
    }

    .bodyContentImage h5 {
      color: #828282;
      font-size: 12px;
      line-height: 1.667em;
      margin-bottom: 0;
    }

    a:visited ,a:focus,a:hover,  a:link{ color: #3386e4; text-decoration:none;}
    .bodyContent img {  height:auto; max-width:498px;}
    .footerContent{color:#808080;font-family:Helvetica;font-size:10px;line-height:150%;padding-top:2.000em;padding-right:2.000em;padding-bottom:2.000em;padding-left:2.000em;text-align:left; }
    .footerContent a:link, .footerContent a:visited, /* Yahoo! Mail Override */ .footerContent a .yshortcuts, .footerContent a span /* Yahoo! Mail Override */{color:#606060;font-weight:normal;text-decoration:underline;}

    #templateContainerImageFull { border-left:1px solid #e2e2e2; border-right:1px solid #e2e2e2; }
    .bodyContentImageFull p { font-size:0 !important; margin-bottom: 0 !important; }
    .brdBottomPadd-lg { border-bottom: 1px solid #f0f0f0; }
    .brdBottomPadd-lg .bodyContent{ padding-bottom: 2.286em; }
    .brdBottomPadd { border-bottom: 1px solid #f0f0f0; }
    .brdBottomPadd .bodyContent{ padding-bottom: 0em; }
    a.blue-btn { background: #5098ea;display: inline-block;color: #FFFFFF; border-top:10px solid #5098ea;border-bottom:10px solid #5098ea; border-left:20px solid #5098ea;border-right:20px solid #5098ea;text-decoration: none;font-size: 14px; margin-top: 1.0em; border-radius: 3px 3px 3px 3px; background-clip: padding-box;}
    .bodyContentNewsLetterDate {color:#505050;font-family:Helvetica;font-size:14px;line-height:150%;padding-top:1.571em;padding-right:1.714em;padding-left:1.714em; padding-bottom:0;}
    .bodyContentNewsLetter {color:#505050; font-family:Helvetica; font-size:14px;line-height:150%;padding-top:0em; padding-right:1.714em;padding-left:1.714em;padding-bottom:0.714em;text-align:left;}

    @media only screen and (max-width: 480px), screen and (max-device-width: 480px) {
      h2{font-size:30px !important;}
      h5{font-size:16px !important;}
      h6{font-size:14px !important;}
      p {font-size: 18px !important;}
      .brdBottomPadd .bodyContent { padding-bottom: 0em !important; }
      .brdBottomPadd-lg .bodyContent { padding-bottom: 2.286em !important; }
      .bodyContent{ padding: 6% 5% 1% 6% !important; }
      .bodyContentNewsLetter { padding: 6% 5% 2% 6% !important; }
      .bodyContent img{ max-width: 100% !important; }
      .bodyContentImage {padding: 3% 6% 6% 6% !important; }
      .bodyContentImage img {max-width: 100% !important;}
      .bodyContentImage h5 {font-size: 15px !important; margin-top:0;}
      table.column { width: 100% !important;max-width: 100% !important; margin-bottom: 2em !important;}
      table.column img { width: 100%;height: auto;}
      table.columnspace {height: 0em !important;}
      .bodyContentNewsLetterDate h5 {font-size: 14px !important;}
      .hide {display:none !important;}
    }

    .ii a[href] {color: inherit !important;}
    span > a, span > a[href] {color: inherit !important;}
    a > span, .ii a[href] > span {text-decoration: inherit !important;}
  </style>

</head>

<body bgcolor="#ffffff">
<table width="100%" bgcolor="#ffffff" border="0" cellpadding="10" cellspacing="0">
<tr>
  <td>
    <!--[if (gte mso 9)|(IE)]>
      <table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td>
    <![endif]-->
    <table bgcolor="#ffffff" class="content" align="center" cellpadding="0" cellspacing="0" border="0">
      <tr>
				<td align="center" valign="top">
					<table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateContainer">
            <tr>
              <td valign="top" class="bodyContentNewsLetter">
                <p style="text-align:left;margin:0;padding:0;">
                  <img src="'.$actual_host.'/img/CellPoint-Mobile-Logo.jpg" />
                </p> 
              </td>
              <td valign="top" class="bodyContentNewsLetterDate">              
                
              </td>
            </tr>

					</table>
				</td>
			</tr>
      <tr>
				<td align="center" valign="top">
					<table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateContainerImageFull" style="min-height:15px;">
						<tr>
							<td valign="top" class="bodyContentImageFull">
                <p style="text-align:center;margin:0;padding:0;">
      						<img src="'.$actual_host.'/img/Mobile-Payments-Imperative-NG.jpg" style="display:block; margin:0; padding:0; border:0; max-width: 100%;" />
      					</p>
							</td>
						</tr>
					</table>
				</td>
			</tr>
      <tr>
				<td align="center" valign="top">
						<!-- BEGIN BODY // -->
						<table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateContainerMiddle" class="brdBottomPadd-lg">
							<tr>
								<td valign="top" class="bodyContent">
									<h2><strong>Please find the link in the email below to complete your booking.</strong></h2>   
										<a class="blue-btn" href="'.$sURL.'"><strong>Pay now</strong></a>
								</td>
							</tr>
						</table>
						<!-- // END BODY -->
					</td>
			</tr>		

    </table>    
    </td>
  </tr>
</table>
</body>
</html>';


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