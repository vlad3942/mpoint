<?php

require_once sCLASS_PATH .'/Parser.php';

abstract class CPMPSP extends Callback implements Captureable, Refundable, Voiadable, Redeemable, Invoiceable
{
    private $_obj_ResponseXML = null;
    public function __construct(RDB $oDB, TranslateText $oTxt, TxnInfo $oTI, array $aConnInfo, PSPConfig $obj_PSPConfig=null, ClientInfo $oClientInfo = null)
    {
        parent::__construct($oDB, $oTxt, $oTI, $aConnInfo, $obj_PSPConfig, $oClientInfo);
    }

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
        $captureMethod = $this->getCaptureMethod();
        if ($captureMethod > 0 && $captureMethod % 2 === 0  && $this->getTxnInfo()->hasEitherState($this->getDBConn(), Constants::iPAYMENT_CAPTURE_INITIATED_STATE) === false  )
        {
            $this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_CAPTURE_INITIATED_STATE, $iAmount);
            return 1000; //Capture Initiated
        }
        else
        {
            $aMerchantAccountDetails = $this->genMerchantAccountDetails();
            $b  = '<?xml version="1.0" encoding="UTF-8"?>';
            $b .= '<root>';
            $b .= '<capture client-id="'. $this->getClientConfig()->getID(). '" account="'. $this->getClientConfig()->getAccountConfig()->getID(). '">';
            $b .= '<client-config>';
            $b .= '<additional-config>';

            foreach ($this->getClientConfig()->getAdditionalProperties(Constants::iPrivateProperty) as $aAdditionalProperty)
            {
                $b .= '<property name="'.$aAdditionalProperty['key'].'">'.$aAdditionalProperty['value'].'</property>';
            }

            $b .= '</additional-config>';
            $b .= '</client-config>';
            $b .= $this->getPSPConfig()->toXML(Constants::iPrivateProperty, $aMerchantAccountDetails);

            if(strtolower($this->getClientConfig()->getAdditionalProperties(Constants::iInternalProperty, 'IS_LEGACY')) === 'false')
            {
                $b .= $this->getPSPConfig()->toRouteConfigXML();
            }

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
                    $this->_obj_ResponseXML =$obj_XML;
                    // Expect there is only one transaction in the reply
                    $obj_Txn = $obj_XML->transactions->transaction;
                    if ( (integer)$obj_Txn["id"] == $this->getTxnInfo()->getID() )
                    {
                        $iStatusCode = (integer)$obj_Txn->status["code"];
                        if ($iStatusCode == 1000) { $this->completeCapture($iAmount, 0, array($obj_HTTP->getReplyBody() ) ); }
                        else if ($iStatusCode == 1100)
                        {
                            $this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_CAPTURE_INITIATED_STATE, utf8_encode($obj_HTTP->getReplyBody() ) );
                            $iStatusCode = 1000;
                        }
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
	public function refund($iAmount=-1,$iStatus=null)
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
		    $aMerchantAccountDetails = $this->genMerchantAccountDetails();
			$b  = '<?xml version="1.0" encoding="UTF-8"?>';
			$b .= '<root>';
			$b .= '<refund client-id="'. $this->getClientConfig()->getID(). '" account="'. $this->getClientConfig()->getAccountConfig()->getID(). '">';
            $b .= '<client-config>';
            $b .= '<additional-config>';

            foreach ($this->getClientConfig()->getAdditionalProperties(Constants::iPrivateProperty) as $aAdditionalProperty)
            {
                $b .= '<property name="'.$aAdditionalProperty['key'].'">'.$aAdditionalProperty['value'].'</property>';
            }

            $b .= '</additional-config>';
            $b .= '</client-config>';
			$b .= $this->getPSPConfig()->toXML(Constants::iPrivateProperty, $aMerchantAccountDetails);

            if(strtolower($this->getClientConfig()->getAdditionalProperties(Constants::iInternalProperty, 'IS_LEGACY')) === 'false')
            {
                $b .= $this->getPSPConfig()->toRouteConfigXML();
            }

			$b .= '<transactions>';
			$b .= $this->_constTxnXML($iAmount);
			$b .= '</transactions>';
			$b .= '</refund>';
			$b .= '</root>';
			$txnPassbookObj = TxnPassbook::Get($this->getDBConn(), $this->getTxnInfo()->getID(), $this->getTxnInfo()->getClientConfig()->getID());

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
                    $this->_obj_ResponseXML =$obj_XML;
					// Expect there is only one transaction in the reply
					$obj_Txn = $obj_XML->transactions->transaction;
					if ( (integer)$obj_Txn["id"] == $this->getTxnInfo()->getID() )
					{
						$iStatusCode = (integer)$obj_Txn->status["code"];
						if ($iStatusCode == 1000)
						{
							$this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_REFUNDED_STATE, utf8_encode($obj_HTTP->getReplyBody() ) );
							$txnPassbookObj->updateInProgressOperations($iAmount, Constants::iPAYMENT_REFUNDED_STATE, Constants::sPassbookStatusDone);
						}
						else if ($iStatusCode == 1100)
						{
							$this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_REFUND_INITIATED_STATE, utf8_encode($obj_HTTP->getReplyBody() ) );
							$txnPassbookObj->updateInProgressOperations($iAmount, Constants::iPAYMENT_REFUNDED_STATE, Constants::sPassbookStatusError);
						}
						//Update Refund amount in txn table
						if((int)$iAmount === -1)
						{
							//get auth amount
							$iAmount = $this->getTxnInfo()->getAmount();
						}
						$this->getTxnInfo()->updateRefundedAmount($this->getDBConn(), $iAmount);
						return $iStatusCode;
					}
					else
					{
						$txnPassbookObj->updateInProgressOperations($iAmount, Constants::iPAYMENT_REFUNDED_STATE, Constants::sPassbookStatusError);
						throw new RefundException("The PSP gateway did not respond with a status document related to the transaction we want: ". $obj_HTTP->getReplyBody(). " for txn: ". $this->getTxnInfo()->getID(), 999); 
					}
				}
				else
				{
					$txnPassbookObj->updateInProgressOperations($iAmount, Constants::iPAYMENT_REFUNDED_STATE, Constants::sPassbookStatusError);
					throw new RefundException("PSP gateway responded with HTTP status code: ". $code. " and body: ". $obj_HTTP->getReplyBody(), $code ); 
				}
			}
			catch (RefundException $e)
			{
				$txnPassbookObj->updateInProgressOperations($iAmount, Constants::iPAYMENT_REFUNDED_STATE, Constants::sPassbookStatusError);
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
	    $aMerchantAccountDetails = $this->genMerchantAccountDetails();
		$b  = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<void client-id="'. $this->getClientConfig()->getID(). '" account="'. $this->getClientConfig()->getAccountConfig()->getID(). '">';
        $b .= '<client-config>';
        $b .= '<additional-config>';

        foreach ($this->getClientConfig()->getAdditionalProperties(Constants::iPrivateProperty) as $aAdditionalProperty)
        {
            $b .= '<property name="'.$aAdditionalProperty['key'].'">'.$aAdditionalProperty['value'].'</property>';
        }

        $b .= '</additional-config>';
        $b .= '</client-config>';
		$b .= $this->getPSPConfig()->toXML(Constants::iPrivateProperty, $aMerchantAccountDetails);

        if(strtolower($this->getClientConfig()->getAdditionalProperties(Constants::iInternalProperty, 'IS_LEGACY')) === 'false')
        {
            $b .= $this->getPSPConfig()->toRouteConfigXML();
        }

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
                $this->_obj_ResponseXML =$obj_XML;
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
	public function cancel($amount = -1)
	{
	    $aMerchantAccountDetails = $this->genMerchantAccountDetails();
		$b  = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<cancel client-id="'. $this->getClientConfig()->getID(). '" account="'. $this->getClientConfig()->getAccountConfig()->getID(). '">';
        $b .= '<client-config>';
        $b .= '<additional-config>';

        foreach ($this->getClientConfig()->getAdditionalProperties(Constants::iPrivateProperty) as $aAdditionalProperty)
        {
            $b .= '<property name="'.$aAdditionalProperty['key'].'">'.$aAdditionalProperty['value'].'</property>';
        }

        $b .= '</additional-config>';
        $b .= '</client-config>';
		$b .= $this->getPSPConfig()->toXML(Constants::iPrivateProperty, $aMerchantAccountDetails);

        if(strtolower($this->getClientConfig()->getAdditionalProperties(Constants::iInternalProperty, 'IS_LEGACY')) === 'false')
        {
            $b .= $this->getPSPConfig()->toRouteConfigXML();
        }

		$b .= '<transactions>';
		if($amount <= 0) {
            $amount = $this->getTxnInfo()->getAmount();
        }
		$b .= $this->_constTxnXML($amount);
		$b .= '</transactions>';
		$b .= '</cancel>';
		$b .= '</root>';
        $txnPassbookObj = TxnPassbook::Get($this->getDBConn(), $this->getTxnInfo()->getID(), $this->getTxnInfo()->getClientConfig()->getID());
		try
		{
			$iUpdateStatusCode = Constants::iPAYMENT_CANCELLED_STATE;
			$obj_ConnInfo = $this->_constConnInfo($this->aCONN_INFO["paths"]["cancel"]);
			$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
			$obj_HTTP->connect();
			$code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
			$obj_HTTP->disConnect();
			
			if ($code == 200)
			{
				$obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );

				if($this->getPSPConfig()->getProcessorType() === 8)
                {
                    return (int)$obj_XML["code"];
                }

                $this->_obj_ResponseXML =$obj_XML;
				// Expect there is only one transaction in the reply
				$obj_Txn = $obj_XML->transactions->transaction;
				if ( (integer)$obj_Txn["id"] == $this->getTxnInfo()->getID() )
				{
					$iStatusCode = (integer)$obj_Txn->status["code"];
					if($iStatus != null)
					{
						$iUpdateStatusCode = $iStatus;
					}

					$paymentState = Constants::iPAYMENT_DECLINED_STATE;
					$passbookState = Constants::sPassbookStatusError;
					$updateStatusCode = Constants::iPAYMENT_DECLINED_STATE;
					$retStatusCode = $iStatusCode;
					$args = array('amount'=>$this->getTxnInfo()->getAmount(),
							'transact'=>$this->getTxnInfo()->getExternalID(),
							'cardid'=>0);

					if ($iStatusCode == 1000)
					{
						$paymentState = Constants::iPAYMENT_CANCELLED_STATE;
						$retStatusCode = 1001;
						$updateStatusCode = $iUpdateStatusCode;
                        $passbookState = Constants::sPassbookStatusDone;
                    }

					$txnPassbookObj->updateInProgressOperations($amount, Constants::iPAYMENT_CANCELLED_STATE, $passbookState);
					$this->newMessage($this->getTxnInfo()->getID(),$updateStatusCode, utf8_encode($obj_HTTP->getReplyBody() ) );
					$this->notifyClient($paymentState, $args, $this->getTxnInfo()->getClientConfig()->getSurePayConfig($this->getDBConn()));
					return $retStatusCode;
				}
				else {
				    $txnPassbookObj->updateInProgressOperations($amount, Constants::iPAYMENT_CANCELLED_STATE, Constants::sPassbookStatusError);
				    throw new mPointException("The PSP gateway did not respond with a status document related to the transaction we want: ". $obj_HTTP->getReplyBody(). " for txn: ". $this->getTxnInfo()->getID(), 999); }
			}
			else {
			    $txnPassbookObj->updateInProgressOperations($amount, Constants::iPAYMENT_CANCELLED_STATE, Constants::sPassbookStatusError);
			    throw new mPointException("PSP gateway responded with HTTP status code: ". $code. " and body: ". $obj_HTTP->getReplyBody(), $code ); }
		}
		catch (mPointException $e)
		{
		    $txnPassbookObj->updateInProgressOperations($amount, Constants::iPAYMENT_CANCELLED_STATE, Constants::sPassbookStatusError);
			trigger_error("Cancel of txn: ". $this->getTxnInfo()->getID(). " failed with code: ". $e->getCode(). " and message: ". $e->getMessage(), E_USER_ERROR);
			return $e->getCode();
		}
	}

	public function status()
	{
		$aMerchantAccountDetails = $this->genMerchantAccountDetails();
		$b  = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<status client-id="'. $this->getClientConfig()->getID(). '" account="'. $this->getClientConfig()->getAccountConfig()->getID(). '">';
        $b .= '<client-config>';
        $b .= '<additional-config>';

        foreach ($this->getClientConfig()->getAdditionalProperties(Constants::iPrivateProperty) as $aAdditionalProperty)
        {
            $b .= '<property name="'.$aAdditionalProperty['key'].'">'.$aAdditionalProperty['value'].'</property>';
        }

        $b .= '</additional-config>';
        $b .= '</client-config>';
		$b .= $this->getPSPConfig()->toXML(Constants::iPrivateProperty, $aMerchantAccountDetails);

        if(strtolower($this->getClientConfig()->getAdditionalProperties(Constants::iInternalProperty, 'IS_LEGACY')) === 'false')
        {
            $b .= $this->getPSPConfig()->toRouteConfigXML();
        }

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
            $this->_obj_ResponseXML =$obj_XML;
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

	public function initialize(PSPConfig $obj_PSPConfig, $euaid=-1, $sc=false, $card_type_id=-1, $card_token='', $obj_BillingAddress = NULL, ClientInfo $obj_ClientInfo = NULL, $authToken = NULL)
	{
	    // save ext id in database
        if($card_type_id !== -1)
        {
            $sql = "UPDATE Log" . sSCHEMA_POSTFIX . ".Transaction_Tbl
                    SET pspid = " . $obj_PSPConfig->getID() . ", 
                        cardid = " . intval($card_type_id) . ",
                        routeconfigid = " . $this->getTxnInfo()->getRouteConfigID() . "
                    WHERE id = " . $this->getTxnInfo()->getID();
            $this->getDBConn()->query($sql);
        }

        $this->updateTxnInfoObject();

	    $this->genInvoiceId($obj_ClientInfo);
	    $aMerchantAccountDetails = $this->genMerchantAccountDetails();
		$obj_XML = simplexml_load_string($this->getClientConfig()->toFullXML($this->getDBConn(), Constants::iPrivateProperty) );
		unset ($obj_XML->password);
		unset ($obj_XML->{'payment-service-providers'});
		$b  = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<initialize client-id="'. $this->getClientConfig()->getID(). '" account="'. $this->getClientConfig()->getAccountConfig()->getID(). '" store-card="'. parent::bool2xml($sc) .'">';
        $b .= str_replace('<?xml version="1.0"?>', '', $obj_XML->asXML() );
        $b .= $obj_PSPConfig->toXML(Constants::iPrivateProperty, $aMerchantAccountDetails);

        if(strtolower($this->getClientConfig()->getAdditionalProperties(Constants::iInternalProperty, 'IS_LEGACY')) === 'false')
        {
            $b .= $obj_PSPConfig->toRouteConfigXML();
        }

        $b .= $this->_constTxnXML();
		$b .= $this->_constOrderDetails($this->getTxnInfo()) ;
		if ($authToken !== null) { $b .= '<auth-token>'.$authToken.'</auth-token>'; }
		if ($euaid > 0) { $b .= $this->getAccountInfo($euaid); }
		if($card_type_id > 0) 
		{ 
			 if($card_token == '')
			 {
			 	$b .= '<card type-id="'.$card_type_id.'"></card>';
			 }
			 else
			 {
			 	$b .= '<card type-id="'.$card_type_id.'">
			 			  <token>'.$card_token.'</token>
			 		   </card>';
			 }
		}
		if(is_null($obj_BillingAddress) == false)
		{
		    //Produce Country config based on the country id
            CountryConfig::setISO3166Attributes($obj_BillingAddress, $this->getDBConn(), intval($obj_BillingAddress["country-id"]));
            $b .= $obj_BillingAddress->asXML();
		}
		if(is_null($obj_ClientInfo) == false)
		{
		    $b .= $obj_ClientInfo->toXML();
		}
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
                $this->_obj_ResponseXML =$obj_XML;
				$this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_INIT_WITH_PSP_STATE, $obj_HTTP->getReplyBody());

               /* if(count($obj_XML->{"hidden-fields"}) > 0){
                    $obj_XML->{"hidden-fields"}->{"store-card"} = parent::bool2xml($sc);
                    $obj_XML->{"hidden-fields"}->{"requested_currency_id"} = $this->getTxnInfo()->getCurrencyConfig()->getID() ;
                } */

                $obj_XML->name = 'card_holderName';
			}
			else { throw new mPointException("Could not construct  XML for initializing payment with PSP: ". $obj_PSPConfig->getName() ." responded with HTTP status code: ". $code. " and body: ". $obj_HTTP->getReplyBody(), $code ); }
		}
		catch (mPointException $e)
		{
			trigger_error("construct  XML of txn: ". $this->getTxnInfo()->getID(). " failed with code: ". $e->getCode(). " and message: ". $e->getMessage(), E_USER_ERROR);
			//re-throw the exception to the calling controller.
			throw $e;
		}
		return $obj_XML;
	}

	public function authorize(PSPConfig $obj_PSPConfig, $obj_Card, $clientInfo=null)
	{
	    $code = 0;
	    $subCode = 0;
		$body = '';
	    try
        {
            $mask =NULL;
            if(isset($obj_Card->{'card-number'}))
            {
                $mask = self::getMaskCardNumber($obj_Card->{'card-number'});
            }
            else if(isset($obj_Card->mask) && empty($obj_Card->mask) === false)
            {
                $mask=str_replace(" ", "", $obj_Card->mask);
            }
            //In case of wallet payment flow mPoint get real card and card id in authorization
            $this->getTxnInfo()->updateCardDetails($this->getDBConn(), $obj_Card['type-id'], $mask, $obj_Card->expiry, $obj_PSPConfig->getID());
            $this->updateTxnInfoObject();
        }
        catch (Exception $e)
        {
            trigger_error("Failed to update card details", E_USER_ERROR);
        }
        $aMerchantAccountDetails = $this->genMerchantAccountDetails();
		$code = 0;
		$b  = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<authorize client-id="'. $this->getClientConfig()->getID(). '" account="'. $this->getClientConfig()->getAccountConfig()->getID(). '">';
        $b .= '<client-config business-type="' .$this->getClientConfig()->getAccountConfig()->getBusinessType(). '">';
        $b .= '<additional-config>';

        foreach ($this->getClientConfig()->getAdditionalProperties(Constants::iPrivateProperty) as $aAdditionalProperty)
        {
            $b .= '<property name="'.$aAdditionalProperty['key'].'">'.$aAdditionalProperty['value'].'</property>';
        }

        $b .= '</additional-config>';
        $b .= '</client-config>';

        $b .= $obj_PSPConfig->toXML(Constants::iPrivateProperty, $aMerchantAccountDetails);

        if(strtolower($this->getClientConfig()->getAdditionalProperties(Constants::iInternalProperty, 'IS_LEGACY')) == 'false')
        {
            $b .= $obj_PSPConfig->toRouteConfigXML();
        }

        $txnXML = $this->_constTxnXML();
        $b .= $txnXML;
        $b .= $this->_constOrderDetails($this->getTxnInfo()) ;

        if ($obj_Card->ticket == '')
        {
            $b .=  $this->_constNewCardAuthorizationRequest($obj_Card);
        }
        else
        {
            $b.= $this->_constStoredCardAuthorizationRequest($obj_Card);
            $obj_txnXML = simpledom_load_string($txnXML);
            $euaid = intval($obj_txnXML->xpath("/transaction/@eua-id")->{'eua-id'});
            if ($euaid > 0) { $b .= $this->getAccountInfo($euaid); }
        }

        if($clientInfo !== null && $clientInfo instanceof ClientInfo)
        {
            $b .= $clientInfo->toXML();
        }
        $b .= '</authorize>';
        $b .= '</root>';

		try
		{
			$obj_ConnInfo = $this->_constConnInfo($this->aCONN_INFO["paths"]["auth"]);

			$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
			$obj_HTTP->connect();
			$code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
			$obj_HTTP->disConnect();
			PostAuthAction::updateTxnVolume($this->getTxnInfo(),$obj_PSPConfig->getID() ,$this->getDBConn());
			
			if ($code == 200 || $code == 303)
			{
				$obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
                $this->_obj_ResponseXML =$obj_XML;
				$sql = "";
				
				if(count($obj_XML->transaction) > 0)
				{
					if(isset($obj_XML->transaction["external-id"]) === true)
					{
						$txnid = $obj_XML->transaction["external-id"];
						$sql = ",extid = '". $this->getDBConn()->escStr($txnid) ."'";
					}
				 $code = $obj_XML->transaction->status["code"];
                 $subCode = $obj_XML->transaction->status["sub-code"];
				} 
				else {
				    $code = $obj_XML->status["code"];
                    $subCode = $obj_XML->status["sub-code"];
				}
				
				$approvalCode = $obj_XML->{'approval-code'};
				
				if($approvalCode != ''){
					$sql .= ",approval_action_code = '".$approvalCode."'";
				}

                if($code == Constants::iPAYMENT_REJECTED_STATE && $this->getTxnInfo()->hasEitherSoftDeclinedState($subCode) === true){
                    $code = Constants::iPAYMENT_SOFT_DECLINED_STATE;
                }

				// In case of 3D verification status code 2005 will be received
				if($code == 2005)
				{
				    $obj_XML= simplexml_load_string($obj_HTTP->getReplyBody() );
				    $obj_XML->{'parsed-challenge'}->action->{'hidden-fields'} = '***** REMOVED *****';
                    $this->newMessage($this->getTxnInfo()->getID(), $code, $obj_XML->asXML());
                    //$this->newMessage($this->getTxnInfo()->getID(), $code, $obj_HTTP->getReplyBody());
					$body = str_replace("<?xml version=\"1.0\" encoding=\"UTF-8\"?>","",$obj_HTTP->getReplyBody());
					$body = str_replace("<root>","",$body);
					$body = str_replace("</root>","",$body);
				}

				$sql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
						SET pspid = ". $obj_PSPConfig->getID() . $sql ;

                if(empty($obj_Card->ticket) === false)
                {
                    $sql .=" ,token='" . $obj_Card->ticket . "'";
                }
                $sql .= " WHERE id = ". $this->getTxnInfo()->getID();
				//echo $sql ."\n";
				$this->getDBConn()->query($sql);
			}
			else if($code == 504){
                trigger_error("Authorization failed of txn: ". $this->getTxnInfo()->getID(). " failed with code: ". $e->getCode(). " and message: ". $e->getMessage(), E_USER_ERROR);
            }
			else { throw new mPointException("Authorization failed with PSP: ". $obj_PSPConfig->getName() ." responded with HTTP status code: ". $code. " and body: ". $obj_HTTP->getReplyBody(), $code ); }
		}
		catch (mPointException $e)
		{
			trigger_error("Authorization failed of txn: ". $this->getTxnInfo()->getID(). " failed with code: ". $e->getCode(). " and message: ". $e->getMessage(), E_USER_ERROR);
		}

		$response = new stdClass();
		$response->code = $code;
		$response->body = $body;
		$response->sub_code = $subCode;
		return $response;
	}

    public function tokenize(array $aConnInfo, PSPConfig $obj_PSPConfig, $obj_Card)
    {
		$sc = false;
        $aMerchantAccountDetails = $this->genMerchantAccountDetails();
        $b  = '<?xml version="1.0" encoding="UTF-8"?>';
        $b .= '<root>';
        $b .= '<tokenize client-id="'. $this->getClientConfig()->getID(). '" account="'. $this->getClientConfig()->getAccountConfig()->getID(). '" store-card="'. parent::bool2xml($sc) .'">';
        $b .= $obj_PSPConfig->toXML(Constants::iPrivateProperty, $aMerchantAccountDetails);

        if(strtolower($this->getClientConfig()->getAdditionalProperties(Constants::iInternalProperty, 'IS_LEGACY')) === 'false')
        {
            $b .= $obj_PSPConfig->toRouteConfigXML();
        }

        $b .= $this->_constTxnXML();
        $b .= $this->_constNewCardAuthorizationRequest($obj_Card);
        $b .= '</tokenize>';
        $b .= '</root>';
        $obj_XML = null;
        try
        {
            $obj_ConnInfo = $this->_constConnInfo($this->aCONN_INFO["paths"]["tokenize"]);
            $sToken = "";
            $sResponseXML = "";
            $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
            $obj_HTTP->connect();
            $code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
            $obj_HTTP->disConnect();
            if ($code == 200)
            {
                $obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
                if($obj_XML->status['code'] == '100')
                {
                    $sToken = $obj_XML->status->card->{'card-number'};
                    $sql = "INSERT INTO Log".sSCHEMA_POSTFIX.".ExternalReference_Tbl
					        (txnid, externalid, pspid,type)
				                VALUES
					        (".$this->getTxnInfo()->getID().", ".$sToken.", ".$obj_PSPConfig->getID().",".$obj_PSPConfig->getID().")";
                    //echo $sql ."\n";
                    $this->getDBConn()->query($sql);
                    $this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_TOKENIZATION_COMPLETE_STATE, $sToken. " generated for transactionID ". $this->getTxnInfo()->getID());
                    $sResponseXML = $obj_XML->status->card->asXML();
                }
            }
            else
            {
                $obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
                $this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_TOKENIZATION_FAILURE_STATE, $obj_HTTP->getReplyBody());
                //Rollback transaction

                //$obj_PaymentProcessor = PaymentProcessor::produceConfig($this->getDBConn(), $this->getText(), $this->getTxnInfo(), $this->getTxnInfo()->getPSPID(), $aConnInfo);
                $txnPassbookObj = TxnPassbook::Get($this->getDBConn(), $this->getTxnInfo()->getID(), $this->getTxnInfo()->getClientConfig()->getID());
                $passbookEntry = new PassbookEntry
                (
                    NULL,
                    $this->getTxnInfo()->getAmount(),
                    $this->getTxnInfo()->getCurrencyConfig()->getID(),
                    Constants::iCancelRequested,
                    '',
                    ''
                );
                if ($txnPassbookObj instanceof TxnPassbook) {
                    $txnPassbookObj->addEntry($passbookEntry);
                    try {
                        $codes = $txnPassbookObj->performPendingOperations($this->getText(), $aConnInfo);
                        $code = reset($codes);
                    } catch (Exception $e) {
                        trigger_error($e, E_USER_WARNING);
                    }
                }
                //$obj_PaymentProcessor->cancel($this->getTxnInfo()->getAmount());
                throw new mPointException("Could not construct  XML for tokenizing with PSP: ". $obj_PSPConfig->getName() ." responded with HTTP status code: ". $code. " and body: ". $obj_XML->status, $code );
            }
        }
        catch (mPointException $e)
        {
            trigger_error("construct  XML of txn: ". $this->getTxnInfo()->getID(). " failed with code: ". $e->getCode(). " and message: ". $e->getMessage(), E_USER_ERROR);
            //re-throw the exception to the calling controller.
            throw $e;
        }
        return $sResponseXML;
    }

	public function redeem($iVoucherID, $iAmount=-1, $sessionToken=null)
	{
		$code = 0;
		$b  = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<redeem-voucher id="'. $iVoucherID .'">';
		$b .= '<transaction order-no="'. $this->getTxnInfo()->getOrderID() .'" id="'. $this->getTxnInfo()->getID() .'">';
		$b .= '<amount country-id="'. $this->getTxnInfo()->getCountryConfig()->getID() .'" decimals="'. $this->getTxnInfo()->getCurrencyConfig()->getDecimals() .'" currency-id="'. $this->getTxnInfo()->getCurrencyConfig()->getID() .'" currency="'. $this->getTxnInfo()->getCurrencyConfig()->getCode() .'">'. $iAmount .'</amount>';
		$b .= '</transaction>';
		$b .= '<session-token>'. $sessionToken .'</session-token>';
		$b .= '</redeem-voucher>';
		$b .= '</root>';

		$obj_ConnInfo = $this->_constConnInfo($this->aCONN_INFO["paths"]["redeem"]);

		$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
		$obj_HTTP->connect();
		$code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
		$obj_HTTP->disConnect();
		if ($code == 200)
		{
			$obj_XML = simplexml_load_string($obj_HTTP->getReplyBody());
			if (isset($obj_XML->voucher->status["code"]) === true && strlen($obj_XML->voucher->status["code"]) > 0) { $code = (string)$obj_XML->voucher->transaction; }
			else { throw new mPointException("Invalid response from voucher issuer: ". $this->getPSPConfig()->getName() .", Body: ". $obj_HTTP->getReplyBody(), $code); }
		}
		else if ($code == 400)
		{
			$obj_XML = simplexml_load_string($obj_HTTP->getReplyBody());
				
			if (isset($obj_XML->voucher->status["code"]) === true && strlen($obj_XML->voucher->status["code"]) > 0) {  throw new UnexpectedValueException("Redeem failed in validation", (integer) $obj_XML->voucher->status["code"] ); }
			else { throw new mPointException("Invalid response from voucher issuer: ". $this->getPSPConfig()->getName() .", Body: ". $obj_HTTP->getReplyBody(), $code); }
		}
		else if ($code == 402) { throw new UnexpectedValueException("Insufficient balance on voucher", 43); }
		else if ($code == 423) { throw new UnexpectedValueException("Voucher usage is temporarily locked", 48); }
		else { throw new mPointException("Redemption failed with PSP: ". $this->getPSPConfig()->getName() .", Txn: ". $this->getTxnInfo()->getID() ."\n\n". $obj_HTTP->getReplyBody(), $code); }

		return $code;
	}

	public function initCallback(PSPConfig $obj_PSPConfig, TxnInfo $obj_TxnInfo, $iStateID, $sStateName, $iCardid)
	{
	    $aMerchantAccountDetails = $this->genMerchantAccountDetails();
		$code = 0;
		$xml  = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<root>';
		$xml .= '<callback>';
		$xml .= $obj_PSPConfig->toXML(Constants::iPrivateProperty, $aMerchantAccountDetails);
		$xml .= '	<transaction id="'. $obj_TxnInfo->getID() .'" order-no="'. $obj_TxnInfo->getOrderID() .'" external-id="'. $obj_TxnInfo->getExternalID() .'">';
		$xml .= '     	<amount country-id="'. $obj_TxnInfo->getCountryConfig()->getID(). '" currency="'.$obj_TxnInfo->getCountryConfig()->getCurrency().'">'. $obj_TxnInfo->getAmount(). '</amount>';
		$xml .= '		<card type-id="'.$iCardid.'" psp-id="'. $obj_TxnInfo->getPSPID() .'">';
		$xml .= '		</card>';
		$xml .= '		<description>'. $obj_TxnInfo->getDescription() .'</description>';
		$xml .= '	</transaction>';
		
		$xml .= '	<status code="'. $iStateID .'">'. $sStateName .'</status>';
		$xml .= '</callback>';
		$xml .= '</root>';
		
		try
		{
			$obj_ConnInfo = $this->_constConnInfo($this->aCONN_INFO["paths"]["callback"]);

			$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
			$obj_HTTP->connect();
			$code = $obj_HTTP->send($this->constHTTPHeaders(), $xml);
			$obj_HTTP->disConnect();

			if($code == 202)
            {
                $code = 1000;
            }
            else if ($code == 200)
			{
				$obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
				if (isset($obj_XML->status["code"]) === true && strlen($obj_XML->status["code"]) > 0) { $code = $obj_XML->status["code"]; }
				else { throw new mPointException("Invalid response from callback controller: ". $this->getPSPConfig()->getName() .", Body: ". $obj_HTTP->getReplyBody(), $code); }
			}
			else { throw new mPointException("Callback to mPoint callback controller: ". $this->getPSPConfig()->getName() ." responded with HTTP status code: ". $code. " and body: ". $obj_HTTP->getReplyBody(), $code ); }
		}
		catch (mPointException $e)
		{
			trigger_error("Callback to mPoint for txn: ". $this->getTxnInfo()->getID(). " failed with code: ". $e->getCode(). " and message: ". $e->getMessage(), E_USER_ERROR);
			$code = -1*abs($code);
		}
		return $code;
	}

	protected  function _constTxnXML($actionAmount=null)
	{
		$obj_XML = simplexml_load_string($this->getTxnInfo()->toXML() );
		$obj_XML->{'authorized-amount'} = (integer) $obj_XML->amount;
		// Add all attributes from "amount" element
		if(count($obj_XML->amount->attributes() ) > 0 && $obj_XML->amount->attributes() instanceof SimpleXMLElement)
		{
			foreach($obj_XML->amount->attributes() as $name => $value)
			{
				$obj_XML->{'authorized-amount'}->addAttribute($name, $value);
			}
		}
		if (isset($actionAmount) === true && is_null($actionAmount) === false)
		{
			$obj_XML->amount = (integer) $actionAmount;
		}
		else { unset($obj_XML->amount); }

		return str_replace('<?xml version="1.0"?>', '', $obj_XML->asXML() );
	}

	protected function _constConnInfo($path)
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
	public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL)
	{
        //If token is returned in the authorize call, we should update the wallet ID in mPoint's Log.Transaction_Tbl
	    if($obj_PSPConfig->getID() > 0 )
        {
            $sql = "UPDATE Log" . sSCHEMA_POSTFIX . ".Transaction_Tbl
						SET walletid = " . $obj_PSPConfig->getID() . "
						WHERE id = " . $this->getTxnInfo()->getID();
            $this->getDBConn()->query($sql);
        }
        $aMerchantAccountDetails = $this->genMerchantAccountDetails();
	    $obj_XML = simplexml_load_string($this->getClientConfig()->toFullXML($this->getDBConn()) );
		unset ($obj_XML->password);
		unset ($obj_XML->{'payment-service-providers'});
		$b  = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<get-payment-data mode="'. $mode .'">';
		$b .= $obj_PSPConfig->toXML(Constants::iPrivateProperty, $aMerchantAccountDetails);

        if(strtolower($this->getClientConfig()->getAdditionalProperties(Constants::iInternalProperty, 'IS_LEGACY')) === 'false')
        {
            $b .= $obj_PSPConfig->toRouteConfigXML();
        }

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
	public function callback(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, SimpleXMLElement $obj_Status, $purchaseDate = null)
	{
		$purchaseDateNode = "";
		
		if($purchaseDate != null)
		{
			$purchaseDateNode = "<PurchaseDate>".$purchaseDate."</PurchaseDate>";
		}
		$aMerchantAccountDetails = $this->genMerchantAccountDetails();
		$obj_XML = simplexml_load_string($this->getClientConfig()->toFullXML($this->getDBConn(), Constants::iPrivateProperty) );
		unset ($obj_XML->password);
		unset ($obj_XML->{'payment-service-providers'});
		$b  = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<callback>';
		$b .= $obj_PSPConfig->toXML(Constants::iPrivateProperty, $aMerchantAccountDetails);
		$b .= str_replace('<?xml version="1.0"?>', '', $obj_XML->asXML() );
		$b .= str_replace("</transaction>", str_replace('<?xml version="1.0"?>', '', $obj_Card->asXML().$purchaseDateNode ). "</transaction>", $this->_constTxnXML() );
		$b .= str_replace('<?xml version="1.0"?>', '', $obj_Status->asXML() ) ;
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
     * Function used to make a process the callback received for a given transaction from an external system and
     * hence provide a specific handling for the same.
     *
     * @param PSPConfig $obj_PSPConfig		The configuration for the Wallet which the payment data should be retrieved from
     * @param SimpleXMLElement $obj_Request	Details for the token that should be used to retrieve the payment data from the 3rd Party Wallet.
     * @return string
     */
    public function processCallback(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Request)
    {
        $b = '';
        $b  = '<?xml version="1.0" encoding="UTF-8"?>';
        $b .= $obj_Request->asXML();


        $obj_ConnInfo = $this->_constConnInfo($this->aCONN_INFO["paths"]["callback"]);

        $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
        $obj_HTTP->connect();
        $code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
        $obj_HTTP->disConnect();

        if (!in_array(intval($code), array(200, 202), true ))
        {
            trigger_error("Callback failed to ". $obj_PSPConfig->getName() ." for the transaction : ". $this->getTxnInfo()->getID(). " failed with code: ". $code ." and body: ". $obj_HTTP->getReplyBody(), E_USER_WARNING);
            throw new mPointException("Callback failed to ". $obj_PSPConfig->getName() ." for the transaction : ". $this->getTxnInfo()->getID(). " failed with code: ". $code ." and body: ". $obj_HTTP->getReplyBody(), $code);
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
		$sql = "SELECT DISTINCT PSP.id, PSP.name, PSP.system_type,
					MA.name AS ma, MA.username, MA.passwd AS password, MSA.name AS msa, CA.countryid
				FROM System".sSCHEMA_POSTFIX.".PSP_Tbl PSP
				INNER JOIN Client".sSCHEMA_POSTFIX.".MerchantAccount_Tbl MA ON PSP.id = MA.pspid AND MA.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".Client_Tbl CL ON MA.clientid = CL.id AND CL.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".Account_Tbl Acc ON CL.id = Acc.clientid AND Acc.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".MerchantSubAccount_Tbl MSA ON Acc.id = MSA.accountid AND PSP.id = MSA.pspid AND MSA.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".CardAccess_Tbl CA ON PSP.id = CA.pspid AND CL.id = CA.clientid AND CA.enabled = '1' 
				WHERE CL.id = ". intval($this->getClientConfig()->getID() ) ." AND CA.cardid = ". intval($cardid) ."
					AND (CA.countryid = ". intval($countryid) ." OR CA.countryid IS NULL)
					AND PSP.system_type IN (".Constants::iPROCESSOR_TYPE_PSP.", ".Constants::iPROCESSOR_TYPE_ACQUIRER.")
				ORDER BY CA.countryid ASC";

		$RS = $this->getDBConn()->getName($sql);
		if (is_array($RS) === true && count($RS) > 1) {	return new PSPConfig($RS["ID"], $RS["NAME"], $RS["SYSTEM_TYPE"], $RS["MA"], $RS["MSA"], $RS["USERNAME"], $RS["PASSWORD"], array()); }
		else { return null; }
	}	
	
	/*
	 *  Fetches an updated list of Payment methods from the client.
	 *  On any issue the provided Card XML will be returned unchanged
	 * 
	 * @param integer $sCards		String og cards XML with all allowed cards on the client 
	 * @return Updated String XML
	 */
	public function getExternalPaymentMethods($sCards)
	{
		$obj_XML = $sCards;
		$b  = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<get-external-payment-methods>';
		$b .= '<transaction id="'. $this->getTxnInfo()->getID() .'" order-no="'. $this->getTxnInfo()->getOrderID() .'">';
		$b .= $sCards;
		$b .= '</transaction>';
		$b .= '</get-external-payment-methods>';
		$b .= '</root>';

		try
		{
			$obj_ConnInfo = $this->_constConnInfo($this->aCONN_INFO["paths"]["get-external-payment-methods"]);
		
			$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
			$obj_HTTP->connect();
			$code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
			$obj_HTTP->disConnect();
			if ($code == 200)
			{
				$obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
				$obj_XML = $obj_XML->transaction->cards->asXML();
			}
			else { throw new mPointException("Could not fetch updated payment card list responded with HTTP status code: ". $code. " and body: ". $obj_HTTP->getReplyBody(), $code ); }
		}
		catch (mPointException $e)
		{
			trigger_error("construct  XML of txn: ". $this->getTxnInfo()->getID(). " failed with code: ". $e->getCode(). " and message: ". $e->getMessage(), E_USER_ERROR);
		}
		
		return $obj_XML;
	}
	public function invoice($sMsg = "" ,$iAmount = -1) { return -1; }
	
	protected function _constNewCardAuthorizationRequest($obj_Card)
	{
		$expiry_month = '';
		$expiry_year = '';

		if($obj_Card->expiry)
		{
			list($expiry_month, $expiry_year) = explode("/", $obj_Card->expiry);
			$expiry_year = substr_replace(date('Y'), $expiry_year, -2);
		}

		$b = '<card type-id="'.intval($obj_Card['type-id']).'">';
		
		if($obj_Card->{'card-holder-name'}) { $b .= '<card-holder-name>'. $obj_Card->{'card-holder-name'} .'</card-holder-name>'; }
				
		$b .= '<card-number>'. $obj_Card->{'card-number'} .'</card-number>';
		$b .= '<expiry-month>'. $expiry_month .'</expiry-month>';
		$b .= '<expiry-year>'. $expiry_year .'</expiry-year>';
                
        if($obj_Card->{'valid-from'}) {
            list($valid_from_month, $valid_from_year) = explode("/", $obj_Card->{'valid-from'});
            $valid_from_year = substr_replace(date('Y'), $valid_from_year, -2);
            $b .= '<valid-from-month>'. $valid_from_month .'</valid-from-month>';
            $b .= '<valid-from-year>'. $valid_from_year .'</valid-from-year>';
        }
                
		if($obj_Card->cvc) { $b .= '<cvc>'. $obj_Card->cvc .'</cvc>'; }

		if($obj_Card->{'info-3d-secure'})
        {
            $b .= $obj_Card->{'info-3d-secure'}->asXML();
        }

		$b .= '</card>';
		
		if($obj_Card->address)
		{
		    //Produce Country config based on the country id
            CountryConfig::setISO3166Attributes($obj_Card->address, $this->getDBConn(), (int)$obj_Card->address["country-id"]);

            if(empty($obj_Card->address->{'state'}) === false)
            {
                $pos = strrpos($obj_Card->address->{'state'}, "[");
                if ($pos > 0)
                {
                    $obj_Card->address->{'state'} = trim(substr($obj_Card->address->{'state'}, 0, $pos) );
                }
                else
                {
                    $obj_Card->address->{'state'} = trim($obj_Card->address->{'state'});
                }
            }

	        $b .= $obj_Card->address->asXML();
		}
		
		return $b;
	}
	
	protected function _constOrderDetails(TxnInfo $obj_txnInfo)
	{
		
		//$sql = "SELECT COUNT(id) FROM Log".sSCHEMA_POSTFIX.".Transaction_Tbl WHERE orderid = '".$obj_txnInfo->getOrderID()."'";
		//		echo $sql ."\n";
		//$RS = $this->getDBConn()->getName($sql);
		$xml = '<order-attempt>'.$obj_txnInfo->getAttemptNumber().'</order-attempt>' ;
		return $xml ;
	}
	
    protected function _constStoredCardAuthorizationRequest($obj_Card)
	{
		list($expiry_month, $expiry_year) = explode("/", $obj_Card->expiry);
		
		$b = '<card type-id="'.intval($obj_Card['type-id']).'">';
		$b .= '<masked_account_number>'. $obj_Card->mask .'</masked_account_number>';
		$b .= '<expiry-month>'. $expiry_month .'</expiry-month>';
		$b .= '<expiry-year>'. $expiry_year .'</expiry-year>';
		$b .= '<token>'. $obj_Card->ticket .'</token>';
                
        if($obj_Card->{'valid-from'}) {
            list($valid_from_month, $valid_from_year) = explode("/", $obj_Card->{'valid-from'});
            $valid_from_year = substr_replace(date('Y'), $valid_from_year, -2);
            $b .= '<valid-from-month>'. $valid_from_month .'</valid-from-month>';
            $b .= '<valid-from-year>'. $valid_from_year .'</valid-from-year>';
        }
                
		if($obj_Card->cvc) { $b .= '<cvc>'. $obj_Card->cvc .'</cvc>'; }

		$b .= '</card>';

		if($obj_Card->address)
		{
		    //Produce Country config based on the country id
            CountryConfig::setISO3166Attributes($obj_Card->address, $this->getDBConn(), (int)$obj_Card->address["country-id"]);
	        $b .= $obj_Card->address->asXML();
		}

		return $b;
	}
    
	protected function _getResponse(){ return $this->_obj_ResponseXML;}

    /**
     * Performs a post status operation with CPM PSP for the provided transaction.
     *
     * @param object $obj_Elem
     * @return int
     */
    public function postStatus($obj_Elem)
    {
        $code = 0;
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<callback>';
        $xml .= '<psp-config id="'.$obj_Elem->{'psp-config'}['id'].'">';
        $xml .= '<name>'.$obj_Elem->{'psp-config'}->name.'</name>';
        $xml .= '</psp-config>';
        $xml .= '<transaction id="'.$obj_Elem->transaction["id"].'" order-no="'.$obj_Elem->transaction["order-no"].'" external-id="">';
        $xml .= '<amount country-id="'.$obj_Elem->transaction->amount['country-id'].'" currency="'.$obj_Elem->transaction->amount['currency'].'">';
        $xml .=  $obj_Elem->transaction->amount;
        $xml .= '</amount>';
        $xml .= '<card type-id="'.$obj_Elem->transaction->card['type-id'].'" />';
        $xml .= '</transaction>';
        $xml .= '<status code="'.$obj_Elem->status['code'].'" />';
        $xml .= '<url>'.$obj_Elem->url.'</url>';
        $xml .= '</callback>';
        $xml .= '</root>';

        try
        {
            if (isset($this->aCONN_INFO["paths"]["post-status"])) {
                $obj_ConnInfo = $this->_constConnInfo($this->aCONN_INFO["paths"]["post-status"]);

                $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
                $obj_HTTP->connect();

                $code = $obj_HTTP->send($this->constHTTPHeaders(), $xml);
                $obj_HTTP->disConnect();
                if ($code != 200) {
                    trigger_error("PostStatus failed for the transaction : " . $this->getTxnInfo()->getID() . " failed with code: " . $code . " and body: " . $obj_HTTP->getReplyBody(), E_USER_WARNING);
                }
            } else {
                trigger_error("PostStatus failed - Endpoint not configured for the PSP: ".$obj_Elem->{'psp-config'}['id'], E_USER_WARNING);
            }
        }
        catch (mPointException $e)
        {
            trigger_error("PostStatus failed for the transaction : ". $this->getTxnInfo()->getID(). " failed with code: ". $code, E_USER_WARNING);
        }
        return $code;
    }

    private function genInvoiceId(?ClientInfo $objClientInfo)
    {
        if($this->getTxnInfo()->getAdditionalData('invoiceid') === null)
        {
            $aMerchantAccountDetails = $this->genMerchantAccountDetails();
            $context = '<root>';
            $context .= $this->getPSPConfig()->toXML(Constants::iInternalProperty, $aMerchantAccountDetails);

            $context .= str_replace('<?xml version="1.0"?>', '', $this->getClientConfig()->toXML(Constants::iPrivateProperty));
            $context .= $this->_constTxnXML();
            $context .= $this->_constOrderDetails($this->getTxnInfo()) ;
            if($objClientInfo !== null and $objClientInfo instanceof ClientInfo)
            {
                $context .= $objClientInfo->toXML();
            }
            $context .= '</root>';
            $parser = new  \mPoint\Core\Parser();
            $parser->setContext($context);

            $rules = $this->getClientConfig()->getAdditionalProperties(Constants::iInternalProperty);

            foreach ($rules as $value )
            {
                if($value['scope'] == 0 && strpos($value['key'], 'rule') !== false)
                {
                    $parser->setRules($value['value']);
                }
            }

            $parser->parse();
            //Get value of invoiceid. $parser->parse() will return the value of first variable whose usage is less or none
            $output = $parser->getValue('invoiceid');
            $this->getTxnInfo()->setInvoiceId($this->getDBConn(),$output);
        }
    }

    protected function genMerchantAccountDetails()
    {
        $context = '<root>';
        $context .= $this->getPSPConfig()->toXML(Constants::iInternalProperty);

        $context .= str_replace('<?xml version="1.0"?>', '', $this->getClientConfig()->toXML(Constants::iPrivateProperty));
        $context .= $this->_constTxnXML();
        $context .= $this->_constOrderDetails($this->getTxnInfo());
        $context .= '</root>';
        $parser = new  \mPoint\Core\Parser();
        $parser->setContext($context);

        $rules = $this->getPSPConfig()->getAdditionalProperties(Constants::iInternalProperty);

        foreach ($rules as $value) {
            if ($value['scope'] == 0 && strpos($value['key'], 'rule') !== false) {
                $parser->setRules($value['value']);
            }
        }

        $parser->parse();
        $merchantaccount = $parser->getValue('merchantaccount');
        $username = $parser->getValue('username');
        $password = $parser->getValue('password');
        $aMerchantAccountDetails = array();
        if(isset($merchantaccount) && $merchantaccount !== false && $merchantaccount !== '')
        {
            $aMerchantAccountDetails['merchantaccount'] = $merchantaccount;
        }
        if(isset($username) && $username !== false && $username !== '')
        {
            $aMerchantAccountDetails['username'] = $username;
        }
        if(isset($password) && $password !== false && $password !== '')
        {
            $aMerchantAccountDetails['password'] = $password;
        }

        return $aMerchantAccountDetails;

    }

    public function getPaymentMethods(PSPConfig $obj_PSPConfig)
    {
		$sc = false;
        $getPaymentMethodsURL = $this->aCONN_INFO["paths"]["get-payment-methods"];
        if(isset($getPaymentMethodsURL)) {
            $obj_XML = simplexml_load_string($this->getClientConfig()->toXML(Constants::iPrivateProperty));
            $b = '<?xml version="1.0" encoding="UTF-8"?>';
            $b .= '<root>';
            $b .= '<get-payment-method client-id="' . $this->getClientConfig()->getID() . '" account="' . $this->getClientConfig()->getAccountConfig()->getID() . '" store-card="' . parent::bool2xml($sc) . '">';
            $b .= str_replace('<?xml version="1.0"?>', '', $obj_XML->asXML());
            $b .= $obj_PSPConfig->toXML(Constants::iPrivateProperty);
            $b .= $this->_constTxnXML();
            $b .= '</get-payment-method>';
            $b .= '</root>';
            $obj_XML = null;
            try {
                $obj_ConnInfo = $this->_constConnInfo($this->aCONN_INFO["paths"]["get-payment-methods"]);

                $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
                $obj_HTTP->connect();
                $code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
                $obj_HTTP->disConnect();
                if ($code == 200) {
                    $obj_XML = simplexml_load_string($obj_HTTP->getReplyBody());
                    $this->_obj_ResponseXML = $obj_XML;
                } else {
                    trigger_error("Error is get-payment-method for psp - " . $obj_PSPConfig->getID(), E_USER_ERROR);
                }
            } catch (mPointException $e) {
                trigger_error("construct  XML of txn: " . $this->getTxnInfo()->getID() . " failed with code: " . $e->getCode() . " and message: " . $e->getMessage(), E_USER_ERROR);
            }
            return $obj_XML;
        }
        return null;
    }

    public function getStatisticalData($attribute)
    {
        $query  = "SELECT key, value
                   FROM client.additionalproperty_tbl
                   WHERE key like '".$attribute."'
                     AND externalid = ".$this->getClientConfig()->getID() ."
                     AND enabled";

        $resultObj = $this->getDBConn()->query($query);

        $aStatisticalData = [];
        while ($rs = $this->getDBConn()->fetchName($resultObj)) {
            $aStatisticalData[$rs['KEY']] = $rs['VALUE'];
        }
        return $aStatisticalData;
    }

    public function authenticate($xml,$obj_Card, $obj_ClientInfo= null)
    {
        $response = new stdClass();
        try
        {
            $this->getTxnInfo()->updateCardDetails($this->getDBConn(), $obj_Card['type-id'], null, $obj_Card->expiry, $this->getPSPConfig()->getID());
            $this->updateTxnInfoObject();

			$code = 0;
			$body = '';

            $obj_ConnInfo = $this->_constConnInfo($this->aCONN_INFO["paths"]["authenticate"]);

            $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
            $obj_HTTP->connect();
            $code = $obj_HTTP->send($this->constHTTPHeaders(), $xml);
            $obj_HTTP->disConnect();

            if ($code == 200 || $code == 303)
            {
                $obj_XML = simplexml_load_string($obj_HTTP->getReplyBody());
                $code = (int)$obj_XML->status["code"];
            }


            $this->newMessage($this->getTxnInfo()->getID(), $code, $obj_HTTP->getReplyBody());
            $iSubCodeID = 0;
            // In case of 3D verification status code 2005 will be received
            if ($code == Constants::iPAYMENT_3DS_VERIFICATION_STATE || $code == Constants::iPAYMENT_3DS_CARD_NOT_ENROLLED || $code == Constants::iPAYMENT_3DS_FAILURE_STATE)
            {
                $body = str_replace("<?xml version=\"1.0\" encoding=\"UTF-8\"?>", "", $obj_HTTP->getReplyBody());
                $body = str_replace("<root>", "", $body);
                $body = str_replace("</root>", "", $body);
                $iSubCodeID = (integer) $obj_XML->status["sub-code"];
                if($iSubCodeID > 0) { $this->newMessage($this->getTxnInfo()->getID(), $iSubCodeID, ''); }

                if((int)$obj_XML->status["code"] !== Constants::iPAYMENT_3DS_VERIFICATION_STATE )
                {
                    $response->sub_code = $iSubCodeID;

                    if($obj_XML->{'info-3d-secure'})
                    {
                        $paymentSecureInfo = PaymentSecureInfo::produceInfo($obj_XML->{'info-3d-secure'},$this->getPSPConfig()->getID(),$this->getTxnInfo()->getID());
                        if($paymentSecureInfo !== null) $this->storePaymentSecureInfo($paymentSecureInfo);

                    }

                    if($this->getPSPConfig()->getAdditionalProperties(Constants::iInternalProperty,"mpi_rule") !== false)
                    {
                        $aRules = $this->getPSPConfig()->getAdditionalProperties(Constants::iInternalProperty);
                        foreach ($aRules as $value)
                        {
                            if (strpos($value['key'], 'mpi_rule') !== false)
                            {
                                $aMpiRule[] = $value['value'];
                            }
                        }
                    }
                    else if($this->getTxnInfo()->getClientConfig()->getAdditionalProperties(Constants::iInternalProperty,"mpi_rule") !== false)
                    {
                        $aRules = $this->$this->getTxnInfo()->getClientConfig()->getAdditionalProperties(Constants::iInternalProperty);
                        foreach ($aRules as $value)
                        {
                            if (strpos($value['key'], 'mpi_rule') !== false)
                            {
                                $aMpiRule[] = $value['value'];
                            }
                        }
                    }
                    $bIsProceedAuth = false;
                    if(empty($aMpiRule) === false)
                    {
                        $bIsProceedAuth = $this->applyRule($obj_XML->{'info-3d-secure'},$aMpiRule);
                    }
                    if($bIsProceedAuth === true) { return $this->authorize($this->getPSPConfig(),$obj_Card,$obj_ClientInfo); }


                }

            }
            else
            {
                throw new mPointException("Authenticate failed with PSP: " . $this->obj_PSPConfig->getName() . " responded with HTTP status code: " . $code . " and body: " . $obj_HTTP->getReplyBody(), $code);
            }
        }
        catch (mPointException $e)
        {
            trigger_error("Authenticate failed of txn: " . $this->getTxnInfo()->getID() . " failed with code: " . $e->getCode() . " and message: " . $e->getMessage(), E_USER_ERROR);
        }

		$response->code = $code;
		$response->body = $body;
        return $response;
    }
}
