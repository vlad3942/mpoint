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
    private bool $dcc;

    /**
     * @var bool
     */
    private bool $mcp;

    /**
     * @var bool
     */
    private bool $pcc;

    /**
     * @var bool
     */
    private bool $fraud;

    /**
     * @var bool
     */
    private bool $tokenization;

    /**
     * @var bool
     */
    private bool $splitPayment;

    /**
     * @var bool
     */
    private bool $callback;

    /**
     * @var bool
     */
    private bool $void;

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
                CS.tokenization_enabled AS tokenization, CS.splitPayment_enabled AS splitPayment, CS.callback_enabled AS callback, CS.void_enabled AS void, CS.enabled			
				FROM Client". sSCHEMA_POSTFIX .".services_tbl CS 				
				WHERE clientid = ". $clientID ." AND enabled = true";

        $aRS = $oDB->getName($sql);
        $aClientService = [];
        if(empty($aRS) === FALSE)
            $aClientService = array_merge($aClientService, $aRS);
        return self::produceFromResultSet($aClientService);
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
        $objURL = new ClientServiceStatus();
        $objURL->setCallback($rs["CALLBACK"]);
        $objURL->setDcc($rs["DCC"]);
        $objURL->setMcp($rs["MCP"]);
        $objURL->setPcc($rs["PCC"]);
        $objURL->setFraud($rs["FRAUD"]);
        $objURL->setTokenization($rs["TOKENIZATION"]);
        $objURL->setSplitPayment($rs["SPLITPAYMENT"]);
        $objURL->setVoid($rs["VOID"]);
        return $objURL;
    }
}