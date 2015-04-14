<?php

abstract class CPMPSP extends Callback implements Captureable
{

    public function __construct(RDB $oDB, TranslateText $oTxt, TxnInfo $oTI)
    {
        parent::__construct($oDB, $oTxt, $oTI);
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
        $b .= $this->getTxnInfo()->toXML();
        $b .= '</transactions>';
        $b .= '</capture>';
        $b .= '</root>';

        try
        {
            $aConnInfo = $this->getConnectionInfo();
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
                    if ($iStatusCode == 1000) { $this->completeCapture($iAmount, 0, $obj_HTTP->getReplyBody() ); }
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

    protected abstract function getConnectionInfo();

}
