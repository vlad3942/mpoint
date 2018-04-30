<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
* Callbacks can be performed by using mPoint's own Callback protocol.
*
* @author Rohit M
* @copyright Cellpoint Mobile
* @link http://www.cellpointmobile.com
* @package Callback
* @subpackage CHUBB
* @version 1.00
*/

/* ==================== PublicBank Exception Classes Start ==================== */
/**
 * Super class for all PublicBank Exceptions
*/
class CHUBBException extends CallbackException { }

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: CHUBB
 *
 */
Class CHUBB extends CPMPSP
{
    private $_obj_ResponseXML = null;
    public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new CHUBBException("Method: getPaymentData is not supported by CHUBB"); }
	public function getPSPID() { return Constants::iCHUBB_PSP;}
    public function authorize(PSPConfig $obj_PSPConfig, $obj_Card, $obj_ClientInfo = null)
    {
        $code = 0;
        $b  = '<?xml version="1.0" encoding="UTF-8"?>';
        $b .= '<root>';
        $b .= '<authorize client-id="'. $this->getClientConfig()->getID(). '" account="'. $this->getClientConfig()->getAccountConfig()->getID(). '">';
        $b .= '<client-config>';
        $b .= '<additional-config>';

        foreach ($this->getClientConfig()->getAdditionalProperties() as $aAdditionalProperty)
        {
            $b .= '<property name="'.$aAdditionalProperty['key'].'">'.$aAdditionalProperty['value'].'</property>';
        }

        $b .= '</additional-config>';
        $b .= '</client-config>';

        $b .= $obj_PSPConfig->toXML();

        $txnXML = $this->_constTxnXML();
        $b .= $txnXML;

        if(empty($obj_ClientInfo) === false)
        {
            $b .= $obj_ClientInfo->asXML();
        }

        if (count($obj_Card->ticket) == 0)
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


        $b .= '</authorize>';
        $b .= '</root>';

        try
        {
            $obj_ConnInfo = $this->_constConnInfo($this->aCONN_INFO["paths"]["auth"]);

            $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
            $obj_HTTP->connect();
            $code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
            $obj_HTTP->disConnect();

            //call post auth actions

            PostAuthAction::updateTxnVolume($this->getTxnInfo(),$obj_PSPConfig->getID() ,$this->getDBConn());

            if ($code == 200 || $code == 303 )
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
                }
                else { $code = $obj_XML->status["code"]; }

                if($code == 2005)
                    $this->newMessage($this->getTxnInfo()->getID(), $code, $obj_HTTP->getReplyBody());
                $this->getTxnInfo()->getPaymentSession()->updateState();
                // In case of 3D verification status code 2005 will be received
                if($code == 2005)
                {
                    $str = str_replace("<?xml version=\"1.0\" encoding=\"UTF-8\"?>","",$obj_HTTP->getReplyBody());
                    $str = str_replace("<root>","",$str);
                    $code = str_replace("</root>","",$str);
                }

                $sql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
						SET pspid = ". $obj_PSPConfig->getID() . $sql."
						WHERE id = ". $this->getTxnInfo()->getID();
                //echo $sql ."\n";
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