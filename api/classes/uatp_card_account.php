<?php
/* ==================== UATP Exception Classes Start ==================== */
/**
 * Super class for all UATP Exceptions
 */
class UATPCarfdAccountException extends CallbackException { }
/* ==================== UATP Exception Classes End ==================== */

class UATPCardAccount extends CPMPSP
{

    public function getPSPID()
    {
       return Constants::iUATP_CARD_ACCOUNT;
    }

    public function initCallback(PSPConfig $obj_PSPConfig, TxnInfo $obj_TxnInfo, $iStateID, $sStateName, $iCardid)
    {
        $aMerchantAccountDetails = $this->genMerchantAccountDetails();
        $code = 0;
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<callback client-id = "'.$obj_TxnInfo->getClientConfig()->getID().'" account-id = "'.$obj_TxnInfo->getAccountID().'">';
        $xml .= $obj_PSPConfig->toXML(Constants::iPrivateProperty, $aMerchantAccountDetails);
        $xml .= $this->_constTxnXML();
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

            if ($code == 200)
            {
                $obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
                foreach ($obj_XML->status as $statusCode)
                {
                    if(intval($statusCode["code"]) === 1000)
                    {
                        $code = intval($statusCode["code"]);
                    }
                    else
                    {
                        $code = intval($statusCode["code"]);
                        throw new mPointException("Invalid response from callback controller: ". $this->getPSPConfig()->getName() .", Body: ". $obj_HTTP->getReplyBody(), $code);
                    }
                }
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
}