<?php

require_once '../../../webroot/inc/include.php';
require_once(sAPI_CLASS_PATH ."/simpledom.php");


function success_response()
{
	$xml = '<?xml version="1.0" encoding="UTF-8"?>';
	$xml .= '<root>';
	$xml .= '	<parsed-challenge>';
	$xml .= '		<scheme-logo>2</scheme-logo>';
	$xml .= '		<member-logo>';
	$xml .= '			<url>http://acs4.3dsecure.no/mdpayacs/logos/netstech_small.png</url>';
	$xml .= '		</member-logo>';
	$xml .= '		<price>DKK 1.00</price>';
	$xml .= '		<date>20160623 14:08:43</date>';
	$xml .= '		<card-number-mask>XXXX XXXX XXXX 1071</card-number-mask>';
	$xml .= '		<pam type-id="1">45302XXX62</pam>';
	$xml .= '		<action type-id="1">';
	$xml .= '			<url content-type="application/x-www-form-urlencoded" method="post" type-id="1">';
	$xml .= '				https://acs4.3dsecure.no/mdpayacs/pareq;mdsessionid=6F5B8B6D6966FE27230A5F91B8D8E72F';
	$xml .= '			</url>';
	$xml .= '			<password type-id="1">otp</password>';
	$xml .= '		</action>';
	$xml .= '		<action type-id="2">';
	$xml .= '			<url method="get" type-id="1">';
	$xml .= '				https://acs4.3dsecure.no/mdpayacs/pareq;mdsessionid=6F5B8B6D6966FE27230A5F91B8D8E72F?resend=true';
	$xml .= '			</url>';
	$xml .= '		</action>';
	$xml .= '		<action type-id="3">';
	$xml .= '			<url method="get" type-id="2">';
	$xml .= '				https://acs4.3dsecure.no/mdpayacs/pareq;mdsessionid=6F5B8B6D6966FE27230A5F91B8D8E72F?ads=true';
	$xml .= '			</url>';
	$xml .= '		</action>';
	$xml .= '	</parsed-challenge>';
	$xml .= '</root>';

	return $xml;
}

$aLines = file(sERROR_LOG, FILE_IGNORE_NEW_LINES);

$aOptions = array();
foreach ($aLines as $l)
{
	$sMarker = "PARSE-3DSECURE-CHALLENGE SIMULATOR ::: ";
	$iPos = strpos($l, $sMarker);
	if ($iPos !== false)
	{
		$aOptions = unserialize(substr($l, $iPos+strlen($sMarker) ) );
		if (is_array($aOptions) === false) { trigger_error("Failed to de-serialize simulator options", E_USER_WARNING); }
		break;
	}
}

$obj_XML = simpledom_load_string(file_get_contents('php://input') );
if ($obj_XML->validate(dirname(__FILE__). '/parse-3dsecure-challenge.xsd') === false)
{
	header("HTTP/1.0 400 Bad Request");
	header("Content-Type: text/xml; charset=\"UTF-8\"");

	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<root>';

	$aObj_Errs = libxml_get_errors();

	foreach ($aObj_Errs as $err)
	{
		echo '<status code="400">'. htmlspecialchars($err->message, ENT_NOQUOTES) .'</status>';
	}

	echo '</root>';
	exit;
}

switch (intval($aOptions["error"]) )
{
	case 0:
		$xml = success_response();
		break;
	case 91:
		header(HTTP::getHTTPHeader(HTTP::NOT_IMPLEMENTED) );
		$xml = '<?xml version="1.0" encoding="UTF-8"?><root><status code="91">Unrecognized 3D Secure Provider</status></root>';
		break;
	case 92:
		header(HTTP::getHTTPHeader(HTTP::BAD_GATEWAY) );
		$xml = '<?xml version="1.0" encoding="UTF-8"?><root><status code="92">Missing required fields in parsed 3dsecure challenge: - pam - pam@type</status></root>';
		break;
	case 93:
		header(HTTP::getHTTPHeader(HTTP::BAD_GATEWAY) );
		$xml = '<?xml version="1.0" encoding="UTF-8"?><root><status code="93">Unknown response from 3dsecure provider challenge parser</status></root>';
		break;
	case 94:
		header(HTTP::getHTTPHeader(HTTP::BAD_GATEWAY) );
		$xml = '<?xml version="1.0" encoding="UTF-8"?><root><status code="9';
		break;
	default:
		trigger_error("Unknown simulator mode: ". $aOptions["error"], E_USER_WARNING);
}

header("Content-Type: text/xml; charset=\"UTF-8\"");
echo $xml;
