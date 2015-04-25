<?php

abstract class CPMPSP extends Callback implements Captureable, Refundable
{

    public function __construct(RDB $oDB, TranslateText $oTxt, TxnInfo $oTI, array $aConnInfo)
    {
        parent::__construct($oDB, $oTxt, $oTI, $aConnInfo);
    }

    /**
     * Performs a capture operation with CPM PSP for the provided transaction.
     * The method will return one the following status codes:
     *    >=1000 Capture succeeded
     *    <1000 Capture failed
     *
     * @param int $iAmount
     * @return int
     * @throws mPointException
     */
    public function capture($iAmount = -1)
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
            $aConnInfo = $this->aCONN_INFO;
            $obj_ConnInfo = new HTTPConnInfo($aConnInfo["protocol"], $aConnInfo["host"], $aConnInfo["port"], $aConnInfo["timeout"], $aConnInfo["paths"]["capture"], $aConnInfo["method"], $aConnInfo["contenttype"]);

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
	 * @throws mPointException
	 */
	public function refund($iAmount = -1)
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
			$aConnInfo = $this->aCONN_INFO;
			$obj_ConnInfo = new HTTPConnInfo($aConnInfo["protocol"], $aConnInfo["host"], $aConnInfo["port"], $aConnInfo["timeout"], $aConnInfo["paths"]["refund"], $aConnInfo["method"], $aConnInfo["contenttype"]);

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
			$this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_DECLINED_STATE, $e->getMessage() );
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


		$aConnInfo = $this->aCONN_INFO;
		$obj_ConnInfo = new HTTPConnInfo($aConnInfo["protocol"], $aConnInfo["host"], $aConnInfo["port"], $aConnInfo["timeout"], $aConnInfo["paths"]["status"], $aConnInfo["method"], $aConnInfo["contenttype"]);

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
				$iState = (integer)$obj_Txn->status["code"];
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

	private function _constTxnXML($actionAmount=null)
	{
		$obj_TxnInfo = $this->getTxnInfo();

		$xml  = '<transaction id="'. $obj_TxnInfo->getID() .'" type="'. $obj_TxnInfo->getTypeID() .'" gmid="'. $obj_TxnInfo->getGoMobileID() .'" mode="'. $obj_TxnInfo->getMode() .'" eua-id="'. $obj_TxnInfo->getAccountID() .'" psp-id="'. $obj_TxnInfo->getPSPID() .'" external-id="'. htmlspecialchars($obj_TxnInfo->getExternalID(), ENT_NOQUOTES) .'">';
		$xml .= '<authorized-amount country-id="'. $obj_TxnInfo->getCountryConfig()->getID() .'" currency="'. $obj_TxnInfo->getCountryConfig()->getCurrency() .'" symbol="'. $obj_TxnInfo->getCountryConfig()->getSymbol() .'" format="'. $obj_TxnInfo->getCountryConfig()->getPriceFormat() .'">'. $obj_TxnInfo->getAmount() .'</authorized-amount>';
		$xml .= '<captured-amount country-id="'. $obj_TxnInfo->getCountryConfig()->getID() .'" currency="'. $obj_TxnInfo->getCountryConfig()->getCurrency() .'" symbol="'. $obj_TxnInfo->getCountryConfig()->getSymbol() .'" format="'. $obj_TxnInfo->getCountryConfig()->getPriceFormat() .'">'. $obj_TxnInfo->getCapturedAmount() .'</captured-amount>';
		$xml .= '<refunded-amount country-id="'. $obj_TxnInfo->getCountryConfig()->getID() .'" currency="'. $obj_TxnInfo->getCountryConfig()->getCurrency() .'" symbol="'. $obj_TxnInfo->getCountryConfig()->getSymbol() .'" format="'. $obj_TxnInfo->getCountryConfig()->getPriceFormat() .'">'. $obj_TxnInfo->getRefund() .'</refunded-amount>';
		$xml .= '<fee-amount country-id="'. $obj_TxnInfo->getCountryConfig()->getID() .'" currency="'. $obj_TxnInfo->getCountryConfig()->getCurrency() .'" symbol="'. $obj_TxnInfo->getCountryConfig()->getSymbol() .'" format="'. $obj_TxnInfo->getCountryConfig()->getPriceFormat() .'">'. $obj_TxnInfo->getFee() .'</fee-amount>';
		$xml .= '<orderid>'. $obj_TxnInfo->getOrderID() .'</orderid>';
		$xml .= '<mobile country-id="'. intval($obj_TxnInfo->getOperator()/100) .'">'. $obj_TxnInfo->getMobile() .'</mobile>';
		$xml .= '<email>'. $obj_TxnInfo->getEMail() .'</email>';
		$xml .= '<language>'. $obj_TxnInfo->getLanguage() .'</language>';

		if (isset($actionAmount) === true)
		{
			$xml .= '<amount country-id="'. $obj_TxnInfo->getCountryConfig()->getID() .'" currency="'. $obj_TxnInfo->getCountryConfig()->getCurrency() .'" symbol="'. $obj_TxnInfo->getCountryConfig()->getSymbol() .'" format="'. $obj_TxnInfo->getCountryConfig()->getPriceFormat() .'">'. $actionAmount .'</amount>';
		}

		$xml .= '</transaction>';

		return $xml;
	}
	
}
