<?php
// Require include file for including all Shared and General APIs
require_once("inc/include.php");

header("content-type: text/plain");

// Open current directory
$dh = opendir($_SERVER['DOCUMENT_ROOT'] ."/text/");
// Directory opened successfully
if (is_resource($dh) === true)
{
	// Lopp through files in directory
	while ( ($file = readdir($dh) ) !== false)
	{
		// Current file is translation directory
		if (substr($file, 0, 1) != "." && is_dir($_SERVER['DOCUMENT_ROOT'] ."/text/". $file) === true)
		{
			echo $file ."\n";
			$obj_ParseText = new ParseText(array($_SERVER['DOCUMENT_ROOT'], sCLASS_PATH), sSYSTEM_PATH);
			$obj_ParseText->writeTranslation($_SERVER['DOCUMENT_ROOT'] ."/text/". $file ."/global.txt");
		}
	}
	closedir($dh);
}
?>