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

    public function authorize(PSPConfig $obj_PSPConfig, $obj_Card, $obj_ClientInfo= null)
    {
       $code= parent::authorize($obj_PSPConfig, $obj_Card, $obj_ClientInfo);
        if($code == "100" || $code == "2000" || $code == "2005" ||$code == "2009") {
            $obj_XML = $this->_getResponse();
            $approvalCode = $obj_XML->{'approval-code'};
            $actionCode = $obj_XML->{'action-code'};
            $mask = $obj_XML->{'card-mask'};
            $expiry = $obj_XML->expiry;
            $token = $obj_XML->token;
            $authOriginalData = $obj_XML->{'auth-original-data'};

            if ( count($mask) != 0 && count($expiry) != 0 && count($token) != 0 ) {

                $sql = "UPDATE Log" . sSCHEMA_POSTFIX . ".Transaction_Tbl
                            SET pspid = " . $obj_PSPConfig->getID() . " , mask='" . $mask . "' , expiry='" . $expiry . "',token='" . $token . "'";

                if(count($approvalCode) != 0 && count($actionCode) != 0) {
                    $approval_action_code = $approvalCode . ":" . $actionCode;
                    $sql .= ", approval_action_code='" . $approval_action_code . "'";
                }

                if( count($authOriginalData) != 0) {
                    $extId = $approvalCode . ":" . $actionCode;
                    $sql .= ", authOriginalData='" . $authOriginalData . "'";
                }

                $sql .= "  WHERE id = " . $this->getTxnInfo()->getID();
                //echo $sql ."\n";
                $this->getDBConn()->query($sql);
            }
        }
        else{
            $sql = "UPDATE Log" . sSCHEMA_POSTFIX . ".Transaction_Tbl
                            SET  extid=''   
                            WHERE id = " . $this->getTxnInfo()->getID();
            $this->getDBConn()->query($sql);
        }
        return $code;
    }

    public function authenticate($xml)
    {
        try {
            $obj_ConnInfo = $this->_constConnInfo($this->aCONN_INFO["paths"]["authenticate"]);

            $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
            $obj_HTTP->connect();
            $code = $obj_HTTP->send($this->constHTTPHeaders(), $xml);
            $obj_HTTP->disConnect();

            if ($code == 200 || $code == 303) {
                $obj_XML = simplexml_load_string($obj_HTTP->getReplyBody());
                $code = $obj_XML->status["code"];
            }

            $this->newMessage($this->getTxnInfo()->getID(), $code, $obj_HTTP->getReplyBody());
            // In case of 3D verification status code 2005 will be received
            if ($code == 2005) {
                $str = str_replace("<?xml version=\"1.0\" encoding=\"UTF-8\"?>", "", $obj_HTTP->getReplyBody());
                $str = str_replace("<root>", "", $str);
                $code = str_replace("</root>", "", $str);

            }
            else {
                throw new mPointException("Authenticate failed with PSP: " . $this->obj_PSPConfig->getName() . " responded with HTTP status code: " . $code . " and body: " . $obj_HTTP->getReplyBody(), $code);
            }
        } catch (mPointException $e) {
            trigger_error("Authenticate failed of txn: " . $this->getTxnInfo()->getID() . " failed with code: " . $e->getCode() . " and message: " . $e->getMessage(), E_USER_ERROR);
        }

        return $code;
    }
}