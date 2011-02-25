<?php
$_SERVER['DOCUMENT_ROOT'] = str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']);
// Define system path constant
define("sSYSTEM_PATH", substr($_SERVER['DOCUMENT_ROOT'], 0, strrpos($_SERVER['DOCUMENT_ROOT'], "/") ) );

// Define path to the General API classes
define("sAPI_CLASS_PATH", substr(sSYSTEM_PATH, 0, strrpos(sSYSTEM_PATH, "/") ) ."/php5api/classes/");

// Define path to the System classes
define("sCLASS_PATH", sSYSTEM_PATH ."/api/classes/");
// Define path to the System interfaces
define("sINTERFACE_PATH", sSYSTEM_PATH ."/api/interfaces/");
// Define path to the System functions
define("sFUNCTION_PATH", sSYSTEM_PATH ."/api/functions/");
// Define path to the System Configuration
define("sCONF_PATH", sSYSTEM_PATH ."/conf/");
// Define Language Path Constant
define("sLANGUAGE_PATH", sSYSTEM_PATH ."/webroot/text/");


// Require API for Text Transalation
require_once(sAPI_CLASS_PATH ."text.php");

header("content-type: text/plain");

// Set primary language
define("sPRIMARY_LANGUAGE", "gb");

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
			// Generate text translations for global.txt from the PHP souce code files
			$obj_ParseText = new ParseText(array($_SERVER['DOCUMENT_ROOT'], sCLASS_PATH), sSYSTEM_PATH);
			$obj_ParseText->writeTranslation($_SERVER['DOCUMENT_ROOT'] ."/text/". $file ."/global.txt");
			// Synchronize translations from the custom.txt file with the primary language to ensure line numbers match 
			$obj_ParseText = new ParseText(array($_SERVER['DOCUMENT_ROOT'] ."/text/". sPRIMARY_LANGUAGE ."/custom.txt"), sSYSTEM_PATH);
			$obj_ParseText->writeTranslation($_SERVER['DOCUMENT_ROOT'] ."/text/". $file ."/custom.txt");
		}
	}
	closedir($dh);
}
?>