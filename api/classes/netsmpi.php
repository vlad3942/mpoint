<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:netsmpi.php
 */


class NetsMpi extends CPMMPI
{
    public function getPSPID()
    {
        Constants::iNETS_MPI;
    }

    public function authenticate()
    {
        $cvv= strrev(base64_encode($this->obj_Card->cvc)) ;

        $sql = "UPDATE Log" . sSCHEMA_POSTFIX . ".Transaction_Tbl
                            SET extid='" . $cvv . "'
                            WHERE id = " . $this->getTxnInfo()->getID();
        //echo $sql ."\n";
        $this->getDBConn()->query($sql);
        $aMerchantAccountDetails = $this->genMerchantAccountDetails();
        $code = 0;
        $b  = '<?xml version="1.0" encoding="UTF-8"?>';
        $b .= '<root>';
        $b .= '<authenticate client-id="'. $this->getClientConfig()->getID(). '" account="'. $this->getClientConfig()->getAccountConfig()->getID(). '">';
        $b .= '<client-config>';
        $b .= '<additional-config>';

        foreach ($this->getClientConfig()->getAdditionalProperties(Constants::iPrivateProperty) as $aAdditionalProperty)
        {
            $b .= '<property name="'.$aAdditionalProperty['key'].'">'.$aAdditionalProperty['value'].'</property>';
        }

        $b .= '</additional-config>';
        $b .= '</client-config>';

        $b .= $this->getPSPConfig()->toXML(Constants::iPrivateProperty,$aMerchantAccountDetails);

        $txnXML = $this->_constTxnXML();
        $b .= $txnXML;

        if (count($this->obj_Card->ticket) == 0)
        {
            $b .=  $this->_constNewCardAuthorizationRequest($this->obj_Card);
        }
        else
        {
            $b.= $this->_constStoredCardAuthorizationRequest($this->obj_Card);
            $obj_txnXML = simpledom_load_string($txnXML);
            $euaid = intval($obj_txnXML->xpath("/transaction/@eua-id")->{'eua-id'});
            if ($euaid > 0) { $b .= $this->getAccountInfo($euaid); }
        }


        $b .= '</authenticate>';
        $b .= '</root>';
        $this->sRequstBody = $b;
        return parent::authenticate();
    }
}