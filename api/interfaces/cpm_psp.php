<?php
abstract class CPMPSP extends Callback implements Captureable, Refundable, Voiadable
{
    public function __construct(RDB $oDB, TranslateText $oTxt, TxnInfo $oTI, array $aConnInfo)
    {
        parent::__construct($oDB, $oTxt, $oTI, $aConnInfo);
    }

	public function notifyClient($iStateId, array $vars) { parent::notifyClient($iStateId, $vars["transact"], $vars["amount"], $vars["card-id"]); }

	/**
     * Performs a capture operation with CPM PSP for the provided transaction.
     * The method will return one the following status codes:
     *    >=1000 Capture succeeded
     *    <1000 Capture failed
     *
     * @param int $iAmount
     * @return int
     */
    public function capture($iAmount=-1)
    {
        $b  = '<?xml version="1.0" encoding="UTF-8"?>';
        $b .= '<root>';
        $b .= '<capture client-id="'. $this->getClientConfig()->getID(). '" account="'. $this->getClientConfig()->getAccountConfig()->getID(). '">';
        $b .= $this->getPSPConfig()->toXML();
        $b .= '<transactions>';
        $b .= $this->_constTxnXML($iAmount);
        $b .= '</transactions>';
        $b .= '</capture>';
        $b .= '</root>';

        try
        {        	 
            $obj_ConnInfo = $this->_constConnInfo($this->aCONN_INFO["paths"]["capture"]);

            $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
            $obj_HTTP->connect();
            $code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
            $obj_HTTP->disConnect();
            
            if ($code == 200)
            {
                $obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
                // Expect there is only one transaction in the reply
                $obj_Txn = $obj_XML->transactions->transaction;
                if ( (integer)$obj_Txn["id"] == $this->getTxnInfo()->getID() )
                {
                    $iStatusCode = (integer)$obj_Txn->status["code"];
                    if ($iStatusCode == 1000) { $this->completeCapture($iAmount, 0, array($obj_HTTP->getReplyBody() ) ); }
                    return $iStatusCode;
                }
                else { throw new CaptureException("The PSP gateway did not respond with a status document related to the transaction we want: ". $obj_HTTP->getReplyBody(). " for txn: ". $this->getTxnInfo()->getID(), 999); }
            }
            else { throw new CaptureException("PSP gateway responded with HTTP status code: ". $code. " and body: ". $obj_HTTP->getReplyBody(), $code ); }
        }
        catch (CaptureException $e)
        {
            trigger_error("Capture of txn: ". $this->getTxnInfo()->getID(). " failed with code: ". $e->getCode(). " and message: ". $e->getMessage(), E_USER_ERROR);
            $this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_DECLINED_STATE, $e->getMessage() );
            return $e->getCode();
        }
    }

	/**
	 * Performs a refund operation with CPM PSP for the provided transaction.
	 * The method will return one the following status codes:
	 *    >=1000 Refund succeeded
	 *    <1000 Refund failed
	 *
	 * @param int $iAmount
	 * @return int
	 */
	public function refund($iAmount=-1)
	{
		// if the user 
		if (strlen($this->aCONN_INFO["paths"]["status"]) > 0) $status = $this->status();
		
		if ($status == Constants::iPAYMENT_ACCEPTED_STATE)
		{
			return $this->cancel();
		}
		// If the PSP does not support a status call we will do a Cancel call if our status of the transaction does not have a capture message
		elseif (count($this->getMessageData($this->getTxnInfo()->getID(), Constants::iPAYMENT_CAPTURED_STATE, false) ) == 0)
		{
			return $this->cancel();
		}
		else
		{
			$b  = '<?xml version="1.0" encoding="UTF-8"?>';
			$b .= '<root>';
			$b .= '<refund client-id="'. $this->getClientConfig()->getID(). '" account="'. $this->getClientConfig()->getAccountConfig()->getID(). '">';
			$b .= $this->getPSPConfig()->toXML();
			$b .= '<transactions>';
			$b .= $this->_constTxnXML($iAmount);
			$b .= '</transactions>';
			$b .= '</refund>';
			$b .= '</root>';

			try
			{
				$obj_ConnInfo = $this->_constConnInfo($this->aCONN_INFO["paths"]["refund"]);
				
				$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
				$obj_HTTP->connect();
				$code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
				
				$obj_HTTP->disConnect();
				if ($code == 200)
				{
					$obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
					// Expect there is only one transaction in the reply
					$obj_Txn = $obj_XML->transactions->transaction;
					if ( (integer)$obj_Txn["id"] == $this->getTxnInfo()->getID() )
					{
						$iStatusCode = (integer)$obj_Txn->status["code"];
						if ($iStatusCode == 1000) { $this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_REFUNDED_STATE, utf8_encode($obj_HTTP->getReplyBody() ) ); }
						return $iStatusCode;
					}
					else { throw new RefundException("The PSP gateway did not respond with a status document related to the transaction we want: ". $obj_HTTP->getReplyBody(). " for txn: ". $this->getTxnInfo()->getID(), 999); }
				}
				else { throw new RefundException("PSP gateway responded with HTTP status code: ". $code. " and body: ". $obj_HTTP->getReplyBody(), $code ); }
			}
			catch (RefundException $e)
			{
				trigger_error("Refund of txn: ". $this->getTxnInfo()->getID(). " failed with code: ". $e->getCode(). " and message: ". $e->getMessage(), E_USER_ERROR);
				return $e->getCode();
			}

		}
	}
	/**
	 * Performs a VOID (Refund or cancel) operation with CPM PSP for the provided transaction.
	 * The method will return one the following status codes:
	 *    >=1000 Refund succeeded
	 *    = 1001 Cancel succeded
	 *    <1000 Refund failed
	 *
	 * @param int $iAmount
	 * @return int
	 */
	public function void($iAmount=-1)
	{
		$b  = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<void client-id="'. $this->getClientConfig()->getID(). '" account="'. $this->getClientConfig()->getAccountConfig()->getID(). '">';
		$b .= $this->getPSPConfig()->toXML();
		$b .= '<transactions>';
		$b .= $this->_constTxnXML($iAmount);
		$b .= '</transactions>';
		$b .= '</void>';
		$b .= '</root>';
	
		try
		{
			$obj_ConnInfo = $this->_constConnInfo($this->aCONN_INFO["paths"]["void"]);
			
			$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
			$obj_HTTP->connect();
			$code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
			$obj_HTTP->disConnect();
			if ($code == 200)
			{
				$obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
				// Expect there is only one transaction in the reply
				$obj_Txn = $obj_XML->transactions->transaction;
				if ( (integer)$obj_Txn["id"] == $this->getTxnInfo()->getID() )
				{
					$iStatusCode = (integer)$obj_Txn->status["code"];
					if ($iStatusCode == 1000) { $this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_REFUNDED_STATE, utf8_encode($obj_HTTP->getReplyBody() ) ); }
					elseif ($iStatusCode == 1001) { $this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_CANCELLED_STATE, utf8_encode($obj_HTTP->getReplyBody() ) ); }
					
					return $iStatusCode;
				}
				else { throw new RefundException("The PSP gateway did not respond with a status document related to the transaction we want: ". $obj_HTTP->getReplyBody(). " for txn: ". $this->getTxnInfo()->getID(), 999); }
			}
			else { throw new RefundException("PSP gateway responded with HTTP status code: ". $code. " and body: ". $obj_HTTP->getReplyBody(), $code ); }
		}
		catch (RefundException $e)
		{
			trigger_error("Void of txn: ". $this->getTxnInfo()->getID(). " failed with code: ". $e->getCode(). " and message: ". $e->getMessage(), E_USER_ERROR);
			return $e->getCode();
		}
	}

	/**
	 * Performs a cancel operation with CPM PSP for the provided transaction.
	 * The method will return one the following status codes:
	 *    >=1000 Cancel succeeded
	 *    <1000 Cancel failed
	 *
	 * @return int
	 */
	public function cancel()
	{
		$b  = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<cancel client-id="'. $this->getClientConfig()->getID(). '" account="'. $this->getClientConfig()->getAccountConfig()->getID(). '">';
		$b .= $this->getPSPConfig()->toXML();
		$b .= '<transactions>';
		$b .= $this->_constTxnXML();
		$b .= '</transactions>';
		$b .= '</cancel>';
		$b .= '</root>';

		try
		{
			$obj_ConnInfo = $this->_constConnInfo($this->aCONN_INFO["paths"]["cancel"]);

			$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
			$obj_HTTP->connect();
			$code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
			$obj_HTTP->disConnect();
			if ($code == 200)
			{
				$obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
				// Expect there is only one transaction in the reply
				$obj_Txn = $obj_XML->transactions->transaction;
				if ( (integer)$obj_Txn["id"] == $this->getTxnInfo()->getID() )
				{
					$iStatusCode = (integer)$obj_Txn->status["code"];
					if ($iStatusCode == 1000)
					{
						//TODO: Move DB update and Client notification to Model layer, once this is created
						$this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_CANCELLED_STATE, utf8_encode($obj_HTTP->getReplyBody() ) );

						$args = array('amount'=>$this->getTxnInfo()->getAmount(),
							          'transact'=>$this->getTxnInfo()->getExternalID(),
							          'card-id'=>0);
						$this->notifyClient(Constants::iPAYMENT_CANCELLED_STATE, $args);
						return 1001;
					}
					return $iStatusCode;
				}
				else { throw new mPointException("The PSP gateway did not respond with a status document related to the transaction we want: ". $obj_HTTP->getReplyBody(). " for txn: ". $this->getTxnInfo()->getID(), 999); }
			}
			else { throw new mPointException("PSP gateway responded with HTTP status code: ". $code. " and body: ". $obj_HTTP->getReplyBody(), $code ); }
		}
		catch (mPointException $e)
		{
			trigger_error("Cancel of txn: ". $this->getTxnInfo()->getID(). " failed with code: ". $e->getCode(). " and message: ". $e->getMessage(), E_USER_ERROR);
			return $e->getCode();
		}
	}

	public function status()
	{
		$b  = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<status client-id="'. $this->getClientConfig()->getID(). '" account="'. $this->getClientConfig()->getAccountConfig()->getID(). '">';
		$b .= $this->getPSPConfig()->toXML();
		$b .= '<transactions>';
		$b .= $this->_constTxnXML();
		$b .= '</transactions>';
		$b .= '</status>';
		$b .= '</root>';

		$obj_ConnInfo = $this->_constConnInfo($this->aCONN_INFO["paths"]["status"]);

		$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
		$obj_HTTP->connect();
		$code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
		$obj_HTTP->disConnect();
		if ($code == 200)
		{
			$obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
			// Expect there is only one transaction in the reply
			$obj_Txn = $obj_XML->transactions->transaction;
			if (intval($obj_Txn["id"]) == $this->getTxnInfo()->getID() )
			{
				$iState = (integer) $obj_Txn->status["code"];
				switch ($iState)
				{
				case Constants::iPAYMENT_ACCEPTED_STATE:
				case Constants::iPAYMENT_CAPTURED_STATE:
				case Constants::iPAYMENT_REFUNDED_STATE:
				case Constants::iPAYMENT_CANCELLED_STATE:
					return $iState;

				case 404:
					throw new TxnInfoException("Transaction not found received from PSP", 404);

				default:
					throw new UnexpectedValueException("The PSP gateway responded with an unexpected payment status: ". $iState. ", http body: ". $obj_HTTP->getReplyBody() );
				}
			}
			else { throw new UnexpectedValueException("The PSP gateway did not respond with a status document related to the transaction we want: ". $obj_HTTP->getReplyBody(). " for txn: ". $this->getTxnInfo()->getID(), 999); }
		}
		else { throw new UnexpectedValueException("PSP gateway responded with HTTP status code: ". $code. " and body: ". $obj_HTTP->getReplyBody(), $code ); }
	}

	public function initialize(PSPConfig $obj_PSPConfig, $euaid=-1, $sc=false)
	{
		$obj_XML = simplexml_load_string($this->getClientConfig()->toFullXML() );
		unset ($obj_XML->password);
		unset ($obj_XML->{'payment-service-providers'});
		$b  = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<initialize client-id="'. $this->getClientConfig()->getID(). '" account="'. $this->getClientConfig()->getAccountConfig()->getID(). '" store-card="'. parent::bool2xml($sc) .'">';
		$b .= $obj_PSPConfig->toXML();
		$b .= str_replace('<?xml version="1.0"?>', '', $obj_XML->asXML() );
		$b .= $this->_constTxnXML();
		if ($euaid > 0) { $b .= $this->getAccountInfo($euaid); }
		$b .= '</initialize>';
		$b .= '</root>';
		
		$obj_XML = null;
		try
		{
			$obj_ConnInfo = $this->_constConnInfo($this->aCONN_INFO["paths"]["initialize"]);
	
			$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
			$obj_HTTP->connect();
			$code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
			$obj_HTTP->disConnect();
			if ($code == 200)
			{
				$obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
				// save ext id in database
				$sql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
						SET pspid = ". $obj_PSPConfig->getID() ."
						WHERE id = ". $this->getTxnInfo()->getID();
//				echo $sql ."\n";
				$this->getDBConn()->query($sql);
			}
			else { throw new mPointException("Could not construct  XML for initializing payment with PSP: ". $this->getPSPConfig()->getName() ." responded with HTTP status code: ". $code. " and body: ". $obj_HTTP->getReplyBody(), $code ); }
		}
		catch (mPointException $e)
		{
			trigger_error("construct  XML of txn: ". $this->getTxnInfo()->getID(). " failed with code: ". $e->getCode(). " and message: ". $e->getMessage(), E_USER_ERROR);
		}
		return $obj_XML;
	}
	public function authTicket(PSPConfig $obj_PSPConfig, $ticket)
	{
		$code = 0;
		$b  = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<authorize client-id="'. $this->getClientConfig()->getID(). '" account="'. $this->getClientConfig()->getAccountConfig()->getID(). '">';
		$b .= $obj_PSPConfig->toXML();
		$b .= $this->_constTxnXML();
		$b .= '<card>';
		$b .= '<token>'. $ticket .'</token>';
		$b .= '</card>';
		$b .= '</authorize>';
		$b .= '</root>';
	
		try
		{
			$obj_ConnInfo = $this->_constConnInfo($this->aCONN_INFO["paths"]["auth"]);
	
			$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
			$obj_HTTP->connect();
			$code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
			$obj_HTTP->disConnect();
			if ($code == 200)
			{
				$obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
				$code = $obj_XML->status["code"];
				// save ext id in database
				$sql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
						SET pspid = ". $obj_PSPConfig->getID() ."
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
	
	private function _constTxnXML($actionAmount=null)
	{
		$obj_TxnInfo = $this->getTxnInfo();
		
		$obj_XML = simplexml_load_string($this->getTxnInfo()->toXML() );
		$obj_XML->{'authorized-amount'} = (integer) $obj_XML->amount;
		// Add all attributes from "amount" element
		foreach($obj_XML->amount->attributes() as $name => $value)
		{
			$obj_XML->{'authorized-amount'}->addAttribute($name, $value);
		}
		if (isset($actionAmount) === true && is_null($actionAmount) === false)
		{
			$obj_XML->amount = (integer) $actionAmount;
		}
		else { unset($obj_XML->amount); } 
		
		return str_replace('<?xml version="1.0"?>', '', $obj_XML->asXML() );
	}

	private function _constConnInfo($path)
	{
		$aCI = $this->aCONN_INFO;
		$aURLInfo = parse_url($this->getClientConfig()->getMESBURL() );
		
		return new HTTPConnInfo($aCI["protocol"], $aURLInfo["host"], $aCI["port"], $aCI["timeout"], $path, $aCI["method"], $aCI["contenttype"], $this->getClientConfig()->getUsername(), $this->getClientConfig()->getPassword() );
	}
	
	/**
	 * Retrieves the payment data stored in a 3rd Party Wallet such as Apple Pay or VISA Checkout.
	 * The method may return an array containing the following status codes:
	 * 	 90. Not Found: Specified token is not available
	 * 	 91. Unauthorized: Token Validation Failed
	 * 	 92. Forbidden: Merchant not authorized for specified data level
	 * 	 98. Communication Error
	 * 	 99. Unknown error.
	 * 
	 * @param PSPConfig $obj_PSPConfig		The configuration for the Wallet which the payment data should be retrieved from
	 * @param SimpleXMLElement $obj_Card	Details for the token that should be used to retrieve the payment data from the 3rd Party Wallet.
	 * @return string
	 */
	public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card)
	{
		$obj_XML = simplexml_load_string($this->getClientConfig()->toFullXML() );
		unset ($obj_XML->password);
		unset ($obj_XML->{'payment-service-providers'});
		$b  = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<get-payment-data>';
		$b .= $obj_PSPConfig->toXML();
		$b .= str_replace('<?xml version="1.0"?>', '', $obj_XML->asXML() );
		$b .= str_replace("</transaction>", str_replace('<?xml version="1.0"?>', '', $obj_Card->asXML() ). "</transaction>", $this->_constTxnXML() );
		$b .= '</get-payment-data>';
		$b .= '</root>';
		$obj_ConnInfo = $this->_constConnInfo($this->aCONN_INFO["paths"]["get-payment-data"]);

		$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
		$obj_HTTP->connect();
		$code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
		$obj_HTTP->disConnect();			
		if ($code != 200)
		{
			trigger_error("Could not fetch Payment Data from ". $obj_PSPConfig->getName() ." for the transaction : ". $this->getTxnInfo()->getID(). " failed with code: ". $code ." and body: ". $obj_HTTP->getReplyBody(), E_USER_WARNING);
		}
		
		return $obj_HTTP->getReplyBody();
	}

	/**
	 * Function used to make a callback to the wallet instance for updating it with the transaction status.
	 * 
	 * @param PSPConfig $obj_PSPConfig		The configuration for the Wallet which the payment data should be retrieved from
	 * @param SimpleXMLElement $obj_Card	Details for the token that should be used to retrieve the payment data from the 3rd Party Wallet.
	 * @return string
	 */
	public function callback(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card)
	{
		$obj_XML = simplexml_load_string($this->getClientConfig()->toFullXML() );
		unset ($obj_XML->password);
		unset ($obj_XML->{'payment-service-providers'});
		$b  = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<callback>';
		$b .= $obj_PSPConfig->toXML();
		$b .= str_replace('<?xml version="1.0"?>', '', $obj_XML->asXML() );
		$b .= str_replace("</transaction>", str_replace('<?xml version="1.0"?>', '', $obj_Card->asXML() ). "</transaction>", $this->_constTxnXML() );
		$b .= '</callback>';
		$b .= '</root>';
		
		$obj_ConnInfo = $this->_constConnInfo($this->aCONN_INFO["paths"]["callback"]);

		$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
		$obj_HTTP->connect();
		$code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
		$obj_HTTP->disConnect();			
		if ($code != 200)
		{
			trigger_error("Callback failed to ". $obj_PSPConfig->getName() ." for the transaction : ". $this->getTxnInfo()->getID(). " failed with code: ". $code ." and body: ". $obj_HTTP->getReplyBody(), E_USER_WARNING);
		}
		
		return $code;
	}
	
	
	/**
	 * Instantiates the Configuration for the Payment Service Provider that the client has configured the a static route for using
	 * the specified Payment Method (card) in the provided Country.
	 * 	
	 * @param integer $cardid		The unique ID of the Payment Method (Card) that the customer is paying with
	 * @param integer $countryid	The unique ID of the Country that the customer is paying in
	 * @return PSPConfig
	 */
	public function getPSPConfigForRoute($cardid, $countryid)
	{
		$sql = "SELECT DISTINCT PSP.id, PSP.name,
					MA.name AS ma, MA.username, MA.passwd AS password, MSA.name AS msa, CA.countryid
				FROM System".sSCHEMA_POSTFIX.".PSP_Tbl PSP
				INNER JOIN Client".sSCHEMA_POSTFIX.".MerchantAccount_Tbl MA ON PSP.id = MA.pspid AND MA.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".Client_Tbl CL ON MA.clientid = CL.id AND CL.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".Account_Tbl Acc ON CL.id = Acc.clientid AND Acc.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".MerchantSubAccount_Tbl MSA ON Acc.id = MSA.accountid AND PSP.id = MSA.pspid AND MSA.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".CardAccess_Tbl CA ON PSP.id = CA.pspid AND CL.id = CA.clientid AND CA.enabled = '1' 
				WHERE CL.id = ". intval($this->getClientConfig()->getID() ) ." AND CA.cardid = ". intval($cardid) ."
					AND (CA.countryid = ". intval($countryid) ." OR CA.countryid IS NULL)
				ORDER BY CA.countryid ASC";
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);	

		if (is_array($RS) === true && count($RS) > 1) {	return new PSPConfig($RS["ID"], $RS["NAME"], $RS["MA"], $RS["MSA"], $RS["USERNAME"], $RS["PASSWORD"], array()); }
		else { return null; }
	}	
}
