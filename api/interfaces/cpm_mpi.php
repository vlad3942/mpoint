<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:cpm_mpi.php
 */


abstract class CPMMPI extends CPMACQUIRER
{
    public $obj_Card = null;

    public $sRequstBody = "";

    public function __construct(RDB $oDB, TranslateText $oTxt, TxnInfo $oTI, array $aConnInfo, PSPConfig $obj_PSPConfig=null,$obj_Card=null)
    {
        parent::__construct($oDB, $oTxt, $oTI, $aConnInfo, $obj_PSPConfig);
        $this->obj_Card = $obj_Card;
    }

    public function authenticate(){
        $obj_XML  ="";
        $code = 0;
        try
        {
            $obj_ConnInfo = $this->_constConnInfo($this->aCONN_INFO["paths"]["authenticate"]);
            $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
            $obj_HTTP->connect();
            $code = $obj_HTTP->send($this->constHTTPHeaders(), $this->sRequstBody);
            $obj_HTTP->disConnect();

            if ($code == 200 || $code == 303 )
            {
                $obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );

                $code = $obj_XML->status["code"];
                // In case of 3D verification status code 2005 will be received
                if($code == 2005)
                {
                    $str = str_replace("<?xml version=\"1.0\" encoding=\"UTF-8\"?>","",$obj_HTTP->getReplyBody());
                    $str = str_replace("<root>","",$str);
                    $code = str_replace("</root>","",$str);

                    $obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
                    $mask = $obj_XML->{'card-mask'};
                    $expiry = $obj_XML->expiry;
                    $token = $obj_XML->token;

                    if ( count($mask) != 0 && count($expiry) != 0 && count($token) != 0 ) {

                        $sql = "UPDATE Log" . sSCHEMA_POSTFIX . ".Transaction_Tbl
                            SET mask='" . $mask . "' , expiry='" . $expiry . "',token='" . $token . "'   
                            WHERE id = " . $this->getTxnInfo()->getID();
                        //echo $sql ."\n";
                        $this->getDBConn()->query($sql);
                    }
                }
            }

            else { throw new mPointException("Authentication failed with PSP: ". $this->obj_PSPConfig->getName() ." responded with HTTP status code: ". $code. " and body: ". $obj_HTTP->getReplyBody(), $code ); }
        }
        catch (mPointException $e)
        {
            trigger_error("Authentication  failed with code: ". $e->getCode(). " and message: ". $e->getMessage(), E_USER_ERROR);
        }

        return $code;
    }
}