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

       $code= parent::authorize($obj_PSPConfig, $obj_Card);
        if($code == "100" || $code == "2000" || $code == "2005" ||$code == "2009") {
            $obj_XML = $this->_getResponse();
            $approvalCode = $obj_XML->{'approval-code'};
            $actionCode = $obj_XML->{'action-code'};
            $mask = $obj_XML->{'card-mask'};
            $expiry = $obj_XML->expiry;
            $token = $obj_XML->token;
            $authOriginalData = $obj_XML->{'auth-original-data'};

            if (count($approvalCode) == 0 && count($actionCode) == 0 && count($mask) == 0 && count($expiry) == 0 && count($token) == 0 && count($authOriginalData) == 0) {
                $extId = $approvalCode . ":" . $actionCode;

                $sql = "UPDATE Log" . sSCHEMA_POSTFIX . ".Transaction_Tbl
                            SET pspid = " . $obj_PSPConfig->getID() . $sql . " , mask='" . $mask . "' , expiry='" . $expiry . "',token='" . $token . "', extid='" . $extId . "', authOriginalData='" . $authOriginalData . "'   
                            WHERE id = " . $this->getTxnInfo()->getID();
                //echo $sql ."\n";
                $this->getDBConn()->query($sql);
            }
        }
        return $code;
    }



}