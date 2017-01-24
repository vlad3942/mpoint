<?php

/**
 * Model class for retrieving transaction status in mPoint
 * This class is similar to classes Refund and Capture in that it represent a core mPoint payment feature,
 * and thus should contain the internal mPoint logic for that operation.
 * - That is; every logical operation which is _not_ PSP specific.
 * - PSP-specific logic should be located in the respective PSP-classes
 *
 * @todo: Move other getTxnStatus logic to this class
 */
class Status extends General
{
	private static $aFinalTxnStates = array(Constants::iPAYMENT_CANCELLED_STATE,
											Constants::iPAYMENT_CAPTURED_STATE,
											Constants::iPAYMENT_REFUNDED_STATE);

	public function getActiveTransactions($from, $to, $enduser=0, $serialize=false, $limit=0, $clients=array() )
	{
		$sFrom = $this->getDBConn()->escStr(date("Y-m-d H:i:s", $from) );
		$sTo = $this->getDBConn()->escStr(date("Y-m-d H:i:s", $to) );

		//Using AND NOT EXISTS with subselect instead of outer join on Log.Message_Tbl due to "FOR UPDATE" clause
		$sql = "SELECT Txn.id
			    	 FROM Log".sSCHEMA_POSTFIX.".Transaction_Tbl Txn
					 INNER JOIN Log".sSCHEMA_POSTFIX.".Message_Tbl Msg1 ON Msg1.txnid = Txn.id AND Msg1.stateid = ". Constants::iPAYMENT_ACCEPTED_STATE . " AND Msg1.enabled = '1'
					 WHERE Txn.created >= '". $sFrom ."' AND Txn.created <= '". $sTo ."'
					 AND NOT EXISTS
					 	(SELECT Msg2.stateid
					 	 FROM Log".sSCHEMA_POSTFIX.".Message_Tbl Msg2
					 	 WHERE Msg2.txnid = Txn.id AND Msg2.stateid IN (". implode(',', self::$aFinalTxnStates) .") AND Msg2.enabled = '1')";

		$enduser = intval($enduser);
		$limit = intval($limit);
		if ($enduser > 0) { $sql .= " AND Txn.euaid = ". $enduser; }
		if (count($clients) > 0) { $sql .= " AND Txn.clientid IN (". implode(",", $clients) .")"; }
		if ($limit > 0) { $sql .= " LIMIT ". $limit; }
		if ($serialize === true) { $sql .= " FOR UPDATE NOWAIT"; }
//		echo $sql ."\n";

		// Supress errors due to NOWAIT clause
		$res = @$this->getDBConn()->query($sql);
		if (is_resource($res) === false) { throw new mPointException("Fetching active transactions failed: ". print_r($this->getDBConn()->getError(), true) ); }

		$aRS = array();
		while ($RS = $this->getDBConn()->fetchName($res) ) { $aRS[] = $RS; }

		return $aRS;
	}

	public function getTransactionInStatus($status=null)
	{
		$sql = "SELECT distinct Txn.id
			    	 FROM Log" . sSCHEMA_POSTFIX . ".Transaction_Tbl Txn
					 INNER JOIN Log" . sSCHEMA_POSTFIX . ".Message_Tbl Msg1 ON Msg1.txnid = Txn.id AND Msg1.stateid = " . $status . " AND Msg1.enabled = '1'";
	
		$res = $this->getDBConn()->query($sql);
		if (is_resource($res) === false) { throw new mPointException("Fetching  transactions failed: ". print_r($this->getDBConn()->getError(), true) ); }
	
		$aRS = array();
		while ($RS = $this->getDBConn()->fetchName($res) ) { $aRS[] = $RS; }
		
		return $aRS;
	}
	
	
	/**
	 * @param Callback $obj_PSP
	 * @param TxnInfo $obj_TxnInfo
	 * @return int PSP transaction status normalized into an mPoint payment status code @see class Constants
	 *			   -1 Unknown or unmappable transaction status return by PSP
	 *			   -2 getPSPStatus is not supported for PSP
	 */
	public function getPSPStatus(Callback $obj_PSP, TxnInfo $obj_TxnInfo)
	{
		switch ($obj_TxnInfo->getPSPID() )
		{
		case Constants::iDIBS_PSP:
			return $obj_PSP->normalizeStatusCode($obj_PSP->status($obj_TxnInfo->getExternalID() ) );
		case Constants::iMOBILEPAY_PSP:
			return $obj_PSP->status();
		default:
			return -2;
		}
	}

}
