<?php
namespace api\classes\merchantservices\MetaData;

/**
   * ClientServiceStatus
   * 
   * 
   * @package    Mechantservices
   * @subpackage ClientServiceStatus Class
   * @author     Vikas Gupta <vikas.gupta@cellpointmobile.com>
 */
class ClientServiceStatus
{

    /**
     * @var bool
     */
    private bool $dcc = FALSE;

    /**
     * @var bool
     */
    private bool $mcp = FALSE;

    /**
     * @var bool
     */
    private bool $pcc = FALSE;

    /**
     * @var bool
     */
    private bool $fraud = FALSE;

    /**
     * @var bool
     */
    private bool $tokenization = FALSE;

    /**
     * @var bool
     */
    private bool $splitPayment = FALSE;

    /**
     * @var bool
     */
    private bool $callback = FALSE;

    /**
     * @var bool
     */
    private bool $void = FALSE;
    /**
     * @var bool
     */
    private bool $mpi = FALSE;

    /**
     * @return bool
     */
    public function isMpi(): bool
    {
        return $this->mpi;
    }

    /**
     * @param bool $mpi
     * @return ClientServiceStatus
     */
    public function setMpi(bool $mpi): ClientServiceStatus
    {
        $this->mpi = $mpi;
        return $this;
    }
    /**
     * @var bool
     */
    private bool $isLegacyFlowEnabled = TRUE;

    /**
     * @return bool
     */
    public function isLegacyFlow(): bool
    {
        return $this->isLegacyFlowEnabled;
    }

    /**
     * @param bool $dcc
     *
     * @return ClientServiceStatus
     */
    public function setLegacyFlow(bool $isLegacyFlowEnabled): ClientServiceStatus
    {
        $this->isLegacyFlowEnabled = $isLegacyFlowEnabled;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDcc(): bool
    {
        return $this->dcc;
    }

    /**
     * @param bool $dcc
     *
     * @return ClientServiceStatus
     */
    public function setDcc(bool $dcc): ClientServiceStatus
    {
        $this->dcc = $dcc;
        return $this;
    }

    /**
     * @return bool
     */
    public function isMcp(): bool
    {
        return $this->mcp;
    }

    /**
     * @param bool $mcp
     *
     * @return ClientServiceStatus
     */
    public function setMcp(bool $mcp): ClientServiceStatus
    {
        $this->mcp = $mcp;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPcc(): bool
    {
        return $this->pcc;
    }

    /**
     * @param bool $pcc
     *
     * @return ClientServiceStatus
     */
    public function setPcc(bool $pcc): ClientServiceStatus
    {
        $this->pcc = $pcc;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFraud(): bool
    {
        return $this->fraud;
    }

    /**
     * @param bool $fraud
     *
     * @return ClientServiceStatus
     */
    public function setFraud(bool $fraud): ClientServiceStatus
    {
        $this->fraud = $fraud;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTokenization(): bool
    {
        return $this->tokenization;
    }

    /**
     * @param bool $tokenization
     *
     * @return ClientServiceStatus
     */
    public function setTokenization(bool $tokenization): ClientServiceStatus
    {
        $this->tokenization = $tokenization;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSplitPayment(): bool
    {
        return $this->splitPayment;
    }

    /**
     * @param bool $splitPayment
     *
     * @return ClientServiceStatus
     */
    public function setSplitPayment(bool $splitPayment): ClientServiceStatus
    {
        $this->splitPayment = $splitPayment;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCallback(): bool
    {
        return $this->callback;
    }

    /**
     * @param bool $callback
     *
     * @return ClientServiceStatus
     */
    public function setCallback(bool $callback): ClientServiceStatus
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVoid(): bool
    {
        return $this->void;
    }

    /**
     * @param bool $void
     *
     * @return ClientServiceStatus
     */
    public function setVoid(bool $void): ClientServiceStatus
    {
        $this->void = $void;
        return $this;
    }

    /**
     * @return string
     */
    public function toXML() : string
    {
        $xml = '<services>';
        $xml .= sprintf("<dcc>%s</dcc>", \General::bool2xml($this->isDcc()));
        $xml .= sprintf("<fraud>%s</fraud>", \General::bool2xml($this->isFraud()));
        $xml .= sprintf("<callback>%s</callback>", \General::bool2xml($this->isCallback()));
        $xml .= sprintf("<mcp>%s</mcp>", \General::bool2xml($this->isMcp()));
        $xml .= sprintf("<pcc>%s</pcc>", \General::bool2xml($this->isPcc()));
        $xml .= sprintf("<split_payment>%s</split_payment>", \General::bool2xml($this->isSplitPayment()));
        $xml .= sprintf("<tokenization>%s</tokenization>", \General::bool2xml($this->isTokenization()));
        $xml .= sprintf("<void>%s</void>", \General::bool2xml($this->isVoid()));
        $xml .= '</services>';
        return $xml;
    }

    public static function produceConfig(\RDB $oDB, int $clientID) {
        $sql = "SELECT CS.id, CS.dcc_enabled AS dcc, CS.mcp_enabled AS mcp, CS.pcc_enabled AS pcc, CS.fraud_enabled AS fraud,
                CS.tokenization_enabled AS tokenization, CS.splitpayment_enabled AS splitPayment, CS.callback_enabled AS callback, CS.void_enabled AS void,CS.legacy_flow_enabled as legacyflow,mpi_enabled as mpi, CS.enabled			
				FROM Client". sSCHEMA_POSTFIX .".services_tbl CS 				
				WHERE clientid = ". $clientID ." AND enabled = true";

        $aRS = $oDB->getName($sql);
        $aClientService = [];
        if(empty($aRS) === FALSE)
            $aClientService = array_merge($aClientService, $aRS);
        return self::produceFromResultSet($aClientService);
    }

    public static function produceFromXML( &$oXML , $oClientInfo = NULL):ClientServiceStatus
    {
        $oClientServices = !empty($oClientInfo) ? $oClientInfo->getClientServices() : NULL;

        $clService = new ClientServiceStatus();

        $clService->setCallback(\General::xml2bool($oXML->callback ?? (!empty($oClientServices) ? $oClientServices->isCallback() : FALSE)));
        $clService->setDcc(\General::xml2bool( $oXML->dcc ?? (!empty($oClientServices) ? $oClientServices->isDCC() : FALSE)));
        $clService->setMcp(\General::xml2bool($oXML->mcp ?? (!empty($oClientServices) ? $oClientServices->isMCP() : FALSE )));
        $clService->setPcc(\General::xml2bool($oXML->pcc  ?? (!empty($oClientServices) ?  $oClientServices->isPCC() : FALSE )));
        $clService->setFraud(\General::xml2bool($oXML->fraud  ?? (!empty($oClientServices) ?  $oClientServices->isFraud() : FALSE )));
        $clService->setTokenization(\General::xml2bool($oXML->tokenization  ?? (!empty($oClientServices) ? $oClientServices->isTokenization() : FALSE )));
        $clService->setSplitPayment(\General::xml2bool($oXML->split_payment ?? (!empty($oClientServices) ?  $oClientServices->isSplitPayment() : FALSE )));
        $clService->setVoid(\General::xml2bool($oXML->void  ?? (!empty($oClientServices) ? $oClientServices->isVoid() : FALSE )));

        return $clService;
    }
    /**
     * Create Object of Class and set data in member variable
     *
     * @param array $rs
     *
     * @return \api\classes\merchantservices\MetaData\ClientServiceStatus
     */
    public static function produceFromResultSet(array $rs): ClientServiceStatus
    {
       $clService = new ClientServiceStatus();
       if(empty($rs) === TRUE)  { return $clService; }
       $clService->setCallback($rs["CALLBACK"]);
       $clService->setDcc($rs["DCC"]);
       $clService->setMcp($rs["MCP"]);
       $clService->setPcc($rs["PCC"]);
       $clService->setFraud($rs["FRAUD"]);
       $clService->setTokenization($rs["TOKENIZATION"]);
       $clService->setSplitPayment($rs["SPLITPAYMENT"]);
       $clService->setVoid($rs["VOID"]);
       $clService->setLegacyFlow($rs["LEGACYFLOW"]);
       $clService->setMpi($rs["MPI"]);
        return $clService;
    }
}