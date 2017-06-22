<?php
/**
 * This file contains the loading screen displaying a spinner until the apropriate page is ready serverside.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage CreditCard
 * @version 1.10
 */

// Require Global Include File
require_once("/include.php");

$xmlData = '<title>'. $_OBJ_TXT->_("Loading..") .'</title>';
$xmlData .= '<post>'. json_encode($_POST) .'</post>';
$xmlData .= '<transaction>';
$xmlData .= '<css-url>'. $_POST['css-url'] .'</css-url>';
$xmlData .= '<language>'. $_POST['language'] .'</language>';
$xmlData .= '<logo><url>'. $_POST['logo-url'] .'</url></logo>';
$xmlData .= '</transaction>';

$xml = '<?xml version="1.0" encoding="UTF-8"?>';
$xml .= '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/html5/load.xsl"?>';

$xml .= '<root>';

$xml .= $xmlData;

$xml .= '</root>';

echo $xml;
exit;
?>