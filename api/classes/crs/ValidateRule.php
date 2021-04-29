<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Digital
 * Link: http://www.cellpointdigital.com
 * Project: mPoint
 * File Name:ValidateRule.php
 */

class ValidateRule
{

	/**
	 * Hold Unique ID of Route Config
	 *
	 * @var int
	 */
	private int $_iRouteConfigId;

    /**
     * Hold Route Configuration count
     *
     * @var int
     */
    private int $_iRouteConfigCount;

	public function __construct(int $routeConfigId, int $routeConfigCount)
	{
        $this->_iRouteConfigId = $routeConfigId;
        $this->_iRouteConfigCount = $routeConfigCount;
	}

	public function getRouteConfigCount() : int
    {
        return $this->_iRouteConfigCount;
    }

    public function getRouteConfigId() : int
    {
        return $this->_iRouteConfigId;
    }

    public function toXML(array $aMissingRouteConfiguration) : string
    {
        $xml = '';
        $xml .= '<status>';
       if(empty($aMissingRouteConfiguration) === true) {
           $xml .= '<code>200</code>';
           $xml .= '<description>Success</description>';
       } else {
           $xml .= '<code>404</code>';
           $xml .= '<description>Configuration Not Found For Route ID : '.implode(',', $aMissingRouteConfiguration).'</description>';
       }
        $xml .= '</status>';
       return $xml;
    }
    public function produceConfig(RDB $oDB, int $routeId, array $aCards, array $aCountries, array $aCurrencies)
    {
        $obj_RuleValidation = null;
        try {
            $sql = "SELECT COUNT (*) AS count 
                        FROM Client" . sSCHEMA_POSTFIX . ".Routeconfig_Tbl RC
                            INNER JOIN Client" . sSCHEMA_POSTFIX . ".RouteCountry_Tbl RCON ON RC.id = RCON.routeconfigid AND RCON.enabled = '1'
                            INNER JOIN Client" . sSCHEMA_POSTFIX . ".RouteCurrency_Tbl RCUR ON RC.id = RCUR.routeconfigid AND RCUR.enabled = '1' 
                            INNER JOIN Client" . sSCHEMA_POSTFIX . ".Route_Tbl R ON RC.routeid = R.id AND R.enabled = '1'
                            INNER JOIN Client" . sSCHEMA_POSTFIX . ".Account_Tbl A ON R.clientid = A.clientid AND A.enabled = '1'
                            INNER JOIN Client" . sSCHEMA_POSTFIX . ".MerchantSubAccount_Tbl MSA ON A.id = MSA.accountid AND MSA.enabled = '1'
                            INNER JOIN System" . sSCHEMA_POSTFIX . ".PSP_Tbl PSP ON R.providerid = PSP.id AND MSA.pspid = PSP.id AND PSP.system_type NOT IN (" . Constants::iPROCESSOR_TYPE_TOKENIZATION . "," . Constants::iPROCESSOR_TYPE_PRE_FRAUD_GATEWAY . "," . Constants::iPROCESSOR_TYPE_POST_FRAUD_GATEWAY . ") AND PSP.enabled = '1'
                            INNER JOIN System" . sSCHEMA_POSTFIX . ".PSPCurrency_Tbl PC ON PSP.id = PC.pspid  AND PC.enabled = '1'
                            INNER JOIN System" . sSCHEMA_POSTFIX . ".PSPCard_Tbl PCD ON PSP.id = PCD.pspid AND PCD.enabled = '1'
                            INNER JOIN System" . sSCHEMA_POSTFIX . ".Card_Tbl C ON C.id = PCD.cardid AND C.enabled = '1'
                            INNER JOIN System" . sSCHEMA_POSTFIX . ".CardPricing_Tbl CP ON C.id = CP.cardid AND CP.enabled = '1'
                            INNER JOIN System" . sSCHEMA_POSTFIX . ".PricePoint_Tbl PP ON CP.pricepointid = PP.id AND PC.currencyid = PP.currencyid AND PP.enabled = '1'
                        WHERE RC.id = " . $routeId;
            if (empty($aCards) === false) {
                $sql .= " AND C.id IN (" . implode(",", $aCards) . ")";
            }
            if (empty($aCountries) === false) {
                $sql .= " AND (RCON.countryid IN ( " . implode(",", $aCountries) . ") OR RCON.countryid IS NULL)";
            }
            if (empty($aCurrencies) === false) {
                $sCurrencies = implode(",", $aCurrencies);
                $sql .= " AND PP.currencyid IN (" . $sCurrencies . ")";
                $sql .= " AND PC.currencyid IN (" . $sCurrencies . ")";
                $sql .= " AND (RCUR.currencyid IN(" . $sCurrencies . ") OR RCUR.currencyid IS NULL)";
            }
            $sql .= "AND RC.enabled = '1'";

            $result = $oDB->getName($sql);
            if(count($result) > 0)
            {
                $obj_RuleValidation = new ValidateRule($routeId, $result['COUNT']);
            }
            else
            {
                trigger_error("Query Produce Empty Result For Route Id : $routeId", E_USER_NOTICE);
            }
        }catch (SQLQueryException $e){
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
        return $obj_RuleValidation;
    }

}
?>