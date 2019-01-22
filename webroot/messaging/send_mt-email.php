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

//order data

$arrayData = xmlToArray($obj_DOM->notify->{'body'}->{'orders'});
$orderData = json_decode(json_encode($arrayData));

$passengerData = $orderData->orders->line_item->product->airline_data->passenger_detail;
$flightData = $orderData->orders->line_item->product->airline_data->flight_detail;
$passengerDetails = '';
$flightDetails = '';
if (isset($passengerData)) {
    if (count($passengerData) > 1) {
        foreach ($passengerData as $data) {
            $passengerDetails .= '<tr>
                                <td valign="top"
                                    style="color: #505050; font-size: 14px;  padding-right: 3.5em; padding-left: 3.5em; padding-bottom: 0;">
                                    <p style="text-align: left;">
                                        <b>Passenger Name:</b> <span style="color:#b9253b "> ' . $data->title . '. ' . $data->first_name . ' ' . $data->last_name . ' </span>
                                    </p>
                                </td>
                            </tr>';
        }
    } else {
        $passengerDetails .= '<tr>
                                <td valign="top"
                                    style="color: #505050; font-size: 14px;  padding-right: 3.5em; padding-left: 3.5em; padding-bottom: 0;">
                                    <p style="text-align: left;">
                                        <b>Passenger Name:</b> <span style="color:#b9253b "> ' . $passengerData->title . '. ' . $passengerData->first_name . ' ' . $passengerData->last_name . ' </span>
                                    </p>
                                </td>
                            </tr>';
    }
}
if (isset($flightData)) {
    if (count($flightData) > 1) {
        foreach ($flightData as $data) {
            $flightDetails .= '<tr>
          <td align="center" valign="top"><!-- BEGIN BODY // -->
            
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="color: #505050; font-size: 14px; line-height: 150%; text-align: center;margin-bottom: 5px">
              <tr>
                <td valign="top" style="color: #505050; font-size: 14px; line-height: 150%; padding-right: 3.5em; padding-left: 3.5em; padding-bottom: 0; text-align: center; background-color: #b9253b; color: #fff;"><h3>Flight No: '.$data->flight_number.'<br>' . $data->departure_airport . ' to '.$data->arrival_airport.'</h3></td>
              </tr>
              <tr>
                <td><table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                      <th style="border: 1px solid #dddddd; text-align: left;padding: 8px 0 8px 8px; font-weight: normal; font-size: 12px;background: #308b50; color: #fff;">Departure Date & Time</th>
                      <th style="border: 1px solid #dddddd; text-align: left;padding: 8px 0 8px 8px; font-weight: normal; font-size: 12px;background: #308b50; color: #fff;">Arrival Date & Time</th>
                      <th style="border: 1px solid #dddddd; text-align: left;padding: 8px 0 8px 8px; font-weight: normal; font-size: 12px;background: #308b50; color: #fff;">Class</th>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #dddddd; text-align: left; padding: 8px 0 8px 8px; font-size: 14px; background: #bdbbbb; color: #000;">'.$data->departure_date.'</td>
                      <td style="border: 1px solid #dddddd; text-align: left; padding: 8px 0 8px 8px; font-size: 14px; background: #bdbbbb; color: #000;">'.$data->arrival_date.'</td>
                      <td style="border: 1px solid #dddddd; text-align: left; padding: 8px 0 8px 8px; font-size: 14px; background: #bdbbbb; color: #000;">'.$data->service_class.'</td>
                    </tr>
                  </table></td>
              </tr>
            </table></td>
        </tr>';
        }
    } else {
        $flightDetails .= '<tr>
          <td align="center" valign="top"><!-- BEGIN BODY // -->
            
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="color: #505050; font-size: 14px; line-height: 150%; text-align: center;margin-bottom: 5px">
              <tr>
                <td valign="top" style="color: #505050; font-size: 14px; line-height: 150%; padding-right: 3.5em; padding-left: 3.5em; padding-bottom: 0; text-align: center; background-color: #b9253b; color: #fff;"><h3>Flight No: '.$flightData->flight_number.'<br>' . $flightData->departure_airport . ' to '.$flightData->arrival_airport.'</h3></td>
              </tr>
              <tr>
                <td><table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                      <th style="border: 1px solid #dddddd; text-align: left;padding: 8px 0 8px 8px; font-weight: normal; font-size: 12px;background: #308b50; color: #fff;">Departure Date & Time</th>
                      <th style="border: 1px solid #dddddd; text-align: left;padding: 8px 0 8px 8px; font-weight: normal; font-size: 12px;background: #308b50; color: #fff;">Arrival Date & Time</th>
                      <th style="border: 1px solid #dddddd; text-align: left;padding: 8px 0 8px 8px; font-weight: normal; font-size: 12px;background: #308b50; color: #fff;">Class</th>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #dddddd; text-align: left; padding: 8px 0 8px 8px; font-size: 14px; background: #bdbbbb; color: #000;">'.$flightData->departure_date.'</td>
                      <td style="border: 1px solid #dddddd; text-align: left; padding: 8px 0 8px 8px; font-size: 14px; background: #bdbbbb; color: #000;">'.$flightData->arrival_date.'</td>
                      <td style="border: 1px solid #dddddd; text-align: left; padding: 8px 0 8px 8px; font-size: 14px; background: #bdbbbb; color: #000;">'.$flightData->service_class.'</td>
                    </tr>
                  </table></td>
              </tr>
            </table></td>
        </tr>';
    }
}
// Input string
$sHtmlData  = file_get_contents($actual_host.'/messaging/template/'.$client.'/email.html');

// Array containing search string
$aSearchVal = array("{CSS URL}","{LOGO IMAGE}", "{BANNER IMAGE}", "{MESSAGE TEXT}", "{PAY NOW URL}", "{PASSENGER DETAIL}", "{FLIGHT DETAIL}");

// Array containing replace string from search string
$aReplaceVal = array($sCssUrl, $sLogo, $sBannerImage, $sMessageText, $sURL, $passengerDetails, $flightDetails);

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