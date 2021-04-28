<?php

require_once(dirname(__FILE__). '/../../../../inc/include.php');
require_once(sAPI_CLASS_PATH ."/simpledom.php");

$obj_XML = simpledom_load_string(file_get_contents("php://input") );
// $anonymous = ($obj_XML->login->password === 'member')?('false'):('true');

header("Content-Type: text/plain");
$xml = '<?xml version="1.0" encoding="UTF-8"?>';
$xml .= '<root>';
$xml .= '<profile>';
$xml .= '<lastName>Yadav</lastName>';
$xml .= '<NofName>false</NofName>';
$xml .= '<secondEmail>null</secondEmail>';
$xml .= '<login>jona@oismail.com</login>';
$xml .= '<firstName>Chaitenya</firstName>';
$xml .= '<mobilePhone>null</mobilePhone>';
$xml .= '<anonymous>false</anonymous>';
$xml .= '<email>jona@oismail.com</email>';
$xml .= '<status>ACTIVE</status>';
$xml .= '</profile>';
$xml .= '</root>';
echo $xml;