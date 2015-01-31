<?php
/**
 * The Template package is part of the general HTTP package and provides methods parsing
 * the HTTP header template to construct the final HTTP header.
 *
 * @author Jonatan Evald Buus
 * @package HTTP
 * @subpackage Template
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 1.00
 *
 */

class Template
{
	private $_iCounter = 0;
	private $_sTemplate;

	public function Template($sTemplate="na")
	{
		if ($sTemplate != "na") { $this->setTemplate($sTemplate); }
	}

	public function setTemplate($sTemplate)
	{
		// Template file not found, try in Web Root
		if (file_exists($sTemplate) === false && isset($_SERVER['DOCUMENT_ROOT']) === true) { $sLoc = $_SERVER['DOCUMENT_ROOT'] ."/". $sTemplate; }
		else { $sLoc = $sTemplate; }

		// Template file found, read into internal memory
		if (file_exists($sLoc) === true && @filesize($sLoc) > 0)
		{
			$fp = fopen($sLoc, "r");
			$this->_sTemplate = fread($fp, filesize($sLoc) );
			$res = fclose($fp);
		}
		// Template file not found, assume template has been given as argument
		else
		{
			$this->_sTemplate = $sTemplate;
		}
	}

	private function parse($sTemplate, array $aData)
	{
		$aData = array_change_key_case($aData, CASE_UPPER);
		// Input data contains condition, format to template syntax
		if (array_key_exists("IF", $aData) === true)
		{
			$aData["IFSTART"] = $aData["IF"];
			$aData["IF"] = "\n". "<?php" ."\n". "if (". $aData["IF"] .")" ."\n". "{" ."\n". "?>";
		}
		// Input data contains condition, append end conditions for use in template
		if (array_key_exists("IFSTART", $aData) === true)
		{
			$aData["IFSTART"] = "\n". "<?php" ."\n". "if (". $aData["IFSTART"] .")" ."\n". "{" ."\n". "?>";
			$aData["ELSE"] = "\n". "<?php" ."\n". "}". "else" ."\n". "{" ."\n". "?>";
			$aData["IFEND"] = "\n". "<?php" ."\n". "}" ."\n". "?>";
		}
		reset($aData);

		while (list($key, $val) = each($aData) )
		{
			$sTemplate = str_replace("{". $key ."}", $val, $sTemplate);
		}

		return $sTemplate;
	}

	public function create(array $aData)
	{
		if (is_array($aData) === true)
		{
			$sTemplate = $this->parse($this->_sTemplate, $aData);

			$sTemplate = str_replace("{LOOP", "{LOOP{", $sTemplate);
			$tTemplate = explode("{LOOP", $sTemplate);

			$sReturn = "";
			for ($i=0; $i<count($tTemplate); $i++)
			{
				$sTemp = $tTemplate[$i];

				$sTemp = str_replace("{END}", "", $sTemp);
				if (stristr($sTemp, "{START}") == true)
				{
					$sTemp = str_replace("{START}", "", $sTemp);

					reset($aData);
					// Loop through first dimension of the array
					while (list($key, $val) = each($aData) )
					{
						// Array is 1-Dimensional, has already been parsed
						if (is_array($val) === true)
						{
							// Array is 3-Dimensional
							if (is_array(current($val) ) === true)
							{
								// Loop through the second dimension of the array
								while (list($k, $v) = each($val) )
								{
									$sReturn .= $this->parse($sTemp, $v);
								}
							}
							// Array is 2-Dimensional
							else
							{
								$sReturn .= $this->parse($sTemp, $val);
							}
						}
					}
				}
				else
				{
					$sReturn .= $sTemp;
				}
			}
		}
		else
		{
			$sReturn = $this->_sTemplate;
		}

		return $sReturn;
	}
}
?>