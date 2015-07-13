<?php
/**
 * The MConsole Administration package provides the required business logic for administering mPoint.
 *
 * @author Rohit Malhotra
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Admin
 * @version 1.00
 */

/**
 * 
 *
 */
//Including the parent class for mPoint Admin
require_once(sCLASS_PATH ."admin.php");

class MConsoleAdmin extends Admin
{	
	
	public function saveClient(&$clientid, $cc , $storecard, $autocapture, $name, $username, $password, 
									$lang, $smsrcpt, $emailrcpt, $mode, $method, $send_pspid, $identification, $transaction_ttl)
	{
        $newclient = false;
		if ($clientid > 0)
		{
			$sql = "UPDATE Client".sSCHEMA_POSTFIX.".Client_Tbl
					SET store_card = ".intval($storecard) .", auto_capture = '". intval($autocapture)."', name = '". $this->getDBConn()->escStr($name)."', username='". $this->getDBConn()->escStr($username)."', passwd='". $this->getDBConn()->escStr($password)."', countryid = ".$cc .",
					lang = '".$this->getDBConn()->escStr($lang)."', smsrcpt = '". intval($smsrcpt) ."', emailrcpt = '". intval($emailrcpt) ."' , mode = ". intval($mode) .", method = '".$this->getDBConn()->escStr($method)."', send_pspid = '".intval($send_pspid)."',
					identification = ".intval($identification).", transaction_ttl = ".intval($transaction_ttl)."
					WHERE id = ". intval($clientid)."";
			$res = $this->getDBConn()->query($sql);
		}
		else
		{
			$sql = "INSERT INTO Client".sSCHEMA_POSTFIX.".Client_Tbl
						(store_card, auto_capture, name, username, passwd, countryid, flowid, lang, smsrcpt, emailrcpt, mode, method, send_pspid, identification, transaction_ttl)
					VALUES
						(". intval($storecard).",'". intval($autocapture)."', '". $this->getDBConn()->escStr($name)."' , '". $this->getDBConn()->escStr($username)."', '". $this->getDBConn()->escStr($password)."',". intval($cc).", ".intval(1).",
						 '".$this->getDBConn()->escStr($lang)."', '". intval($smsrcpt) ."', '". intval($emailrcpt) ."' ,". intval($mode) .",'".$this->getDBConn()->escStr($method)."','".intval($send_pspid)."',".intval($identification).",".intval($transaction_ttl).")";
//			echo $sql ."\n";		
			$res = $this->getDBConn()->query($sql);
			if (is_resource($res))
			{
				$sql = "SELECT MAX(id) AS ID
						FROM Client".sSCHEMA_POSTFIX.".Client_Tbl";
//				echo $sql ."\n";
				$RS = $this->getDBConn()->getName($sql);
								
				if (is_array($RS) === true)
				{
					$clientid = $RS["ID"];						
					$newclient = true;				
				}
			}
		}
		return $newclient == true ? true : is_resource($res);
	}
	
	public function saveClientCardData($clientid, $storecard, $showallcards, $maxcards)
	{
		$sql = "UPDATE Client".sSCHEMA_POSTFIX.".Client_Tbl
				SET store_card = ".intval($storecard) .", show_all_cards = '". intval($showallcards)."', max_cards = ". intval($maxcards)."
				WHERE id = ". intval($clientid)."";
		
		return is_resource($this->getDBConn()->query($sql));	
	}	
	
}
?>