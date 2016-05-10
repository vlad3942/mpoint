<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol. 
 *
 * @author Abhishek Sawant
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage GlobalCollect
 * @version 1.00
 */

/* ==================== GlobalCollect Exception Classes Start ==================== */
/**
 * Super class for all Data Cash Exceptions
 */
class GlobalCollectException extends CallbackException { }
/* ==================== GlobalCollect Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: GlobalCollect
 *
 */
class GlobalCollect extends CPMPSP
{
	public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new GlobalCollectException("Method: getPaymentData is not supported by GlobalCollect"); }

	public function getPSPID() { return Constants::iGLOBAL_COLLECT_PSP; }
	
	public function authTicket($obj_PSPConfig, $obj_Elem, $token = null)
	{

		$attributes = $obj_Elem->attributes();
		
		$code = 0;
		$b  = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<authorize client-id="'. $this->getClientConfig()->getID(). '" account="'. $this->getClientConfig()->getAccountConfig()->getID(). '">';
		$b .= $obj_PSPConfig->toXML();		
		$b .= $this->_constTxnXML();				
		$b .= '<card id="'.$attributes['type-id'].'">';
		
		if(is_null($token) == true)
		{
			$b .= '<token>'.$obj_Elem->ticket.'</token>';
		} 
		else 
		{
			
			$b .= '<token>'.$token.'</token>';
		}
		
		if(count($obj_Elem->cvc) == 1)
		{
			$b .= '<cvc>'.intval($obj_Elem->cvc).'</cvc>';
			$obj_ConnInfo = $this->_constConnInfo($this->aCONN_INFO["paths"]["auth"]);
		}
		else
		{
			$obj_ConnInfo = $this->_constConnInfo($this->aCONN_INFO["paths"]["auth-complete"]);
		}		
		
		$b .= '</card>';
		$b .= '</authorize>';
		$b .= '</root>';
					
		try
		{
			
	
			$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
			$obj_HTTP->connect();
			$code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
			$obj_HTTP->disConnect();
			$id = 0;
			if ($code == 200)
			{
				$obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
				$txnid = $obj_XML["external-id"];
				$code = $obj_XML->status['code'];
				// save ext id in database
				$sql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
						SET pspid = ". $obj_PSPConfig->getID() .",extid = '". $this->getDBConn()->escStr($txnid) ."' 
						WHERE id = ". $this->getTxnInfo()->getID();
				//				echo $sql ."\n";
				$this->getDBConn()->query($sql);
			}
			else { throw new mPointException("Authorization failed with PSP: ". $obj_PSPConfig->getName() ." responded with HTTP status code: ". $code. " and body: ". $obj_HTTP->getReplyBody(), $code ); }
		}
		catch (mPointException $e)
		{
			trigger_error("Authorization failed of txn: ". $this->getTxnInfo()->getID(). " failed with code: ". $e->getCode(). " and message: ". $e->getMessage(), E_USER_ERROR);
		}
	
		return $code;
	}
}
