<?php
// Require include file for including all Shared and General APIs
require_once("inc/include.php");

header("content-type: text/plain");

$obj_ParseText = new ParseText(array($_SERVER['DOCUMENT_ROOT'], sCLASS_PATH), sSYSTEM_PATH);
$obj_ParseText->writeTranslation($_SERVER['DOCUMENT_ROOT'] ."/text/uk/global.txt");
?>