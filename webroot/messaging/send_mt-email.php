<?php
require_once("inc/include.php");

$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

$client = (integer)$obj_DOM->notify[0]["client-id"];

$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $client);

$base_url = $obj_ClientConfig->getBaseAssetURL();

if (empty($client) === true || !file_get_contents(dirname(__FILE__).'/template/' . $client . '/email.php')) {
    $client = 'default';
}

if (isset($obj_DOM->notify->{'body'}->{'assets'}) === true && isset($obj_DOM->notify->{'body'}->{'assets'}->{'banner-image'}) === true )
{
    $sBannerImage = (string)$obj_DOM->notify->{'body'}->{'assets'}->{'banner-image'};
}
else
{
    $sBannerImage =  "$base_url/$client/PBL/assets/images/banner.png";
}

$sLogo = "$base_url/$client/PBL/assets/images/logo.jpg";
$sCssUrl = "$base_url/$client./PBL/assets/style/style.css";

$sPaymentURL = (string)$obj_DOM->notify->{'body'}->{'message'};

preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $sPaymentURL, $match);

$sURL = $match[0][0];

if(empty ($sURL) === false )
{
    $sMessageText = trim(substr($sPaymentURL, 0, strpos($sPaymentURL, $sURL) ) );
}

include(dirname(__FILE__) . '/template/' . $client . '/email.php');
//order data
if (isset($obj_DOM->notify->{'body'}->{'orders'}) === true) {
    $arrayData = xmlToArray($obj_DOM->notify->{'body'}->{'orders'});
    $orderData = json_decode(json_encode($arrayData));
    $passengerData = $orderData->orders->line_item->product->airline_data->passenger_detail;
    $flightData = $orderData->orders->line_item->product->airline_data->flight_detail;
    $sHtmlData = getEmailData($passengerData, $flightData);
} else {
    $sHtmlData = getEmailData();
}
// Array containing search string
$aSearchVal = array("{CSS URL}","{LOGO IMAGE}", "{BANNER IMAGE}", "{MESSAGE TEXT}", "{PAY NOW URL}");

// Array containing replace string from search string
$aReplaceVal = array($sCssUrl, $sLogo, $sBannerImage, $sMessageText, $sURL);

// Function to replace string
$sBody = str_replace($aSearchVal, $aReplaceVal, $sHtmlData);
$sBody = utf8_encode(htmlspecialchars($sBody, ENT_QUOTES));

$sFromEmail = (string)$obj_DOM->notify->{'from'};
$sRecipientEmail = array((string)$obj_DOM->notify->{'to'});
$sSubject = (string)$obj_DOM->notify->{'body'}->{'subject'};
$requestId = "PBL";

$client =  (integer)$obj_DOM->notify[0]["client-id"];

/* ----- Construct HTTP Header Start ----- */
$h = "{METHOD} {PATH} HTTP/1.0" .HTTPClient::CRLF;
$h .= "host: {HOST}" .HTTPClient::CRLF;
$h .= "referer: {REFERER}" .HTTPClient::CRLF;
$h .= "content-length: {CONTENTLENGTH}" .HTTPClient::CRLF;
$h .= "content-type: {CONTENTTYPE}; charset=UTF-8" .HTTPClient::CRLF;
$h .= "user-agent: mPoint" .HTTPClient::CRLF;
/* ----- Construct HTTP Header End ----- */

/* ----- Construct HTTP Connection Info Start ----- */
$url= $obj_ClientConfig->getMESBURL().'/emailservice/send-email';
$aURL_Info = parse_url($url);
$aHTTP_CONN_INFO["mesb"]["protocol"] = $aURL_Info["scheme"];
$aHTTP_CONN_INFO["mesb"]["host"] = $aURL_Info["host"];
$aHTTP_CONN_INFO["mesb"]["port"] = $aURL_Info["port"];
$aHTTP_CONN_INFO["mesb"]["timeout"] = '20';
$aHTTP_CONN_INFO["mesb"]["path"] = $aURL_Info["path"];
$aHTTP_CONN_INFO["mesb"]["method"] = 'POST';
$aHTTP_CONN_INFO["mesb"]["contenttype"] = 'text/xml';
$aHTTP_CONN_INFO["mesb"]["username"] = $obj_ClientConfig->getUsername();
$aHTTP_CONN_INFO["mesb"]["password"] = $obj_ClientConfig->getPassword();

$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["mesb"]);
/* ----- Construct HTTP Connection Info End ----- */

$obj_emailService = new emailService();

$obj_emailService->produceEmail($client, $sFromEmail, $sRecipientEmail, $sSubject, $sBody, $requestId);

$response = $obj_emailService->sendEmail(simplexml_load_string($obj_emailService->toXML()), $obj_ConnInfo, $h);

if ($response->SendMailResult->code == 200 && $response->SendMailResult->status =='ACCEPTED')
{
    $xml .= '<status code="' . $response->SendMailResult->code . '">Message successfully sent </status>';
}
else
{
    $xml .= '<status code="'.$response->SendMailResult->code.'">Message sending failed</status>';
}

header("Content-Type: text/xml; charset=\"UTF-8\"");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo preg_replace('~\s*(<([^>]*)>[^<]*</\2>|<[^>]*>)\s*~', '$1', $xml);
echo '</root>';

function xmlToArray($xml, $options = array()) {
    $defaults = array(
        'namespaceSeparator' => ':',//you may want this to be something other than a colon
        'attributePrefix' => '',   //to distinguish between attributes and nodes with the same name
        'alwaysArray' => array(),   //array of xml tag names which should always become arrays
        'autoArray' => true,        //only create arrays for tags which appear more than once
        'textContent' => 'text',       //key used for the text content of elements
        'autoText' => true,         //skip textContent key if node has no attributes or child nodes
        'keySearch' => false,       //optional search and replace on tag and attribute names
        'keyReplace' => false,       //replace values for above search values (as passed to str_replace())
        'hyphenReplace' => '_',
        'rootNodeIgnore' => true,
    );
    $options = array_merge($defaults, $options);
    $namespaces = $xml->getDocNamespaces();
    $namespaces[''] = null; //add base (empty) namespace

    //get attributes from all namespaces
    $attributesArray = array();
    foreach ($namespaces as $prefix => $namespace) {
        foreach ($xml->attributes($namespace) as $attributeName => $attribute) {
            //replace characters in attribute name
            if ($options['keySearch']) $attributeName =
                str_replace($options['keySearch'], $options['keyReplace'], $attributeName);
            $attributeKey = $options['attributePrefix']
                . ($prefix ? $prefix . $options['namespaceSeparator'] : '')
                . $attributeName;

            if($options['hyphenReplace']) $attributeKey = str_replace("-",$options['hyphenReplace'],$attributeName);

            $attributesArray[$attributeKey] = (string)$attribute;
        }
    }

    //get child nodes from all namespaces
    $tagsArray = array();
    foreach ($namespaces as $prefix => $namespace) {
        foreach ($xml->children($namespace) as $childXml) {
            //recurse into child nodes

            if($namespace == "prefixes") { //To remove un-necessary recursion
                $childArray =$childXml;
            }
            else
                $childArray = xmlToArray($childXml, $options);
            list($childTagName, $childProperties) = each($childArray);
            //replace characters in tag name
            if ($options['keySearch']) $childTagName =
                str_replace($options['keySearch'], $options['keyReplace'], $childTagName);

            if($options['hyphenReplace']) $childTagName = str_replace("-",$options['hyphenReplace'],$childTagName);
            //add namespace prefix, if any
            if ($prefix) $childTagName = $prefix . $options['namespaceSeparator'] . $childTagName;

            if (!isset($tagsArray[$childTagName])) {
                //only entry with this key
                //test if tags of this type should always be arrays, no matter the element count
                $tagsArray[$childTagName] =
                    in_array($childTagName, $options['alwaysArray']) || !$options['autoArray']
                        ? array($childProperties) : $childProperties;
                if($childTagName==="card" )
                {
                    $tagsArray[$childTagName] =array($childProperties);
                }
            } elseif (
                is_array($tagsArray[$childTagName]) && array_keys($tagsArray[$childTagName])
                === range(0, count($tagsArray[$childTagName]) - 1)
            ) {
                //key already exists and is integer indexed array
                $tagsArray[$childTagName][] = $childProperties;
            } else {
                //key exists so convert to integer indexed array with previous value in position 0
                $tagsArray[$childTagName] = array($tagsArray[$childTagName], $childProperties);
            }
        }
    }

    //get text content of node
    $textContentArray = array();
    $plainText = trim((string)$xml);
    if ($plainText !== '') $textContentArray[$options['textContent']] = $plainText;

    //stick it all together
    $propertiesArray = !$options['autoText'] || $attributesArray || $tagsArray || ($plainText === '')
        ? array_merge($attributesArray, $tagsArray, $textContentArray) : $plainText;

    //return node as array
    if($xml->getName() === "root" && $options['rootNodeIgnore'])
        return $propertiesArray;
    else
        return array(
            $xml->getName() => $propertiesArray
        );
}


?>