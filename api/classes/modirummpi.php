<?php
/**
 * Created by IntelliJ IDEA.
 * User: Rohit Malhotra
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:modirummpi.php
 */


class ModirumMPI extends CPMMPI
{
    public function getPSPID()
    {
        Constants::iMODIRUM_MPI;
    }

    public function authenticate()
    {
        $cvv= strrev(base64_encode($this->obj_Card->cvc)) ;

        $sql = "UPDATE Log" . sSCHEMA_POSTFIX . ".Transaction_Tbl
                            SET extid='" . $cvv . "'
                            WHERE id = " . $this->getTxnInfo()->getID();
        //echo $sql ."\n";
        $this->getDBConn()->query($sql);
        $code = 0;
        $b  = '<?xml version="1.0" encoding="UTF-8"?>';
        $b .= '<root>';
        $b .= '<authenticate client-id="'. $this->getClientConfig()->getID(). '" account="'. $this->getClientConfig()->getAccountConfig()->getID(). '">';
        $b .= '<client-config>';
        $b .= '<additional-config>';

        foreach ($this->getClientConfig()->getAdditionalProperties() as $aAdditionalProperty)
        {
            $b .= '<property name="'.$aAdditionalProperty['key'].'">'.$aAdditionalProperty['value'].'</property>';
        }

        $b .= '</additional-config>';
        $b .= '</client-config>';

        $b .= $this->getPSPConfig()->toXML();

        $txnXML = $this->_constTxnXML();
        $b .= $txnXML;

        $b .=  $this->_constNewCardAuthorizationRequest($this->obj_Card);

        $b .= '</authenticate>';
        $b .= '</root>';
        $this->sRequstBody = $b;
        return parent::authenticate();
    }
}