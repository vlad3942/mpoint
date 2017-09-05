<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name:cpm_acquirer.php
 */

abstract class CPMACQUIRER extends CPMPSP
{
    public function __construct(RDB $oDB, TranslateText $oTxt, TxnInfo $oTI, array $aConnInfo, PSPConfig $obj_PSPConfig=null)
    {
        parent::__construct($oDB, $oTxt, $oTI, $aConnInfo, $obj_PSPConfig);
    }

    public function authorize(PSPConfig $obj_PSPConfig, $obj_Card)
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

            if ($code == 200 || $code == 303 )
            {
                $obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );

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