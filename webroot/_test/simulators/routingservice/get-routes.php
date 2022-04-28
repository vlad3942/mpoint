<?php
// Require Global Include File
require_once '../../../../webroot/inc/include.php';
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."/simpledom.php");

$obj_DOM = simpledom_load_string(file_get_contents('php://input') );

$xml = '';
if(empty($obj_DOM->client_info->client_id) === false && empty($obj_DOM->transaction->card->amount->country_id) === false && empty($obj_DOM->transaction->card->amount->currency_id) == false)
{
    header('HTTP/1.1 200 OK');
    header("Content-Type: text/xml; charset=\"UTF-8\"");

    $xml = '<payment_route_search_response>';
    $xml .= '<routes>';
    $xml .= '<route>';
    $xml .= '<id>18</id>';
    $xml .= '<preference>1</preference>';
    $xml .= '</route>';
    $xml .= '<route>';
    $xml .= '<id>17</id>';
    $xml .= '<preference>2</preference>';
    $xml .= '</route>';
    $xml .= '</routes>';
    if($obj_DOM->transaction->id == '1001002') {
        $xml .= '<kpi_used>true</kpi_used>';
    } else {
        $xml .= '<kpi_used>false</kpi_used>';
    }
    $xml .= '<rule_id>5</rule_id>';
    $xml .= '</payment_route_search_response>';

    echo $xml;
}else {
    header('HTTP/1.1 200 OK');
    header("Content-Type: text/xml; charset=\"UTF-8\"");

    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<root>';
    echo '</root>';
}



