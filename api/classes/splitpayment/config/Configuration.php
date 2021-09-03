<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package: api\.classes.splitpayment.config
 * File Name: Configuration
 */

namespace api\classes\splitpayment\config;


/**
 * Class Configuration
 *
 * @package api\classes\splitpayment\config
 */
class Configuration
{
    /**
     * @var Combinations[]
     */
    private array $combinations = [];
    private array $activeSplit = [];
    private ?int $currentSplitSeq;

    /**
     * Configuration constructor.
     *
     * @param Combination[] $combinations Array of Split combinations
     */
    public function __construct(array $combinations = NULL) {

        if($combinations !== NULL)
        {
            $this->combinations= $combinations;
        }
    }

    /**
     * @return array
     */
    public function getCombinations(): array
    {
        return $this->combinations;
    }

    public function setCombination(Combination $combination): void
    {
        $this->combinations[] = $combination;
    }

    public function setActiveSplit($activeSplit): void
    {
        $this->activeSplit[] = $activeSplit;
    }

    public function setCurrentSplitSeq($currentSplitSeq): void
    {
        $this->currentSplitSeq = $currentSplitSeq;
    }

    /**
     * Return Configuration as XML string
     * @return string
     */
    public function toXML(): string
    {
        $xml = "";
        if(count($this->activeSplit) > 0){
            $xml .= "<active_split>";
            $xml .= "<current_split_sequence>".$this->currentSplitSeq."</current_split_sequence>";
            $xml .= "<transactions>";
            foreach ($this->activeSplit as $activeSplit) {
                foreach ($activeSplit as $transaction) {
                    $xml .= "<transaction>";
                    $xml .= "<payment_type>" . $transaction['PAYMENTTYPE'] . "</payment_type>";
                    $xml .= "<id>" . $transaction['TRANSACTION_ID'] . "</id>";
                    $xml .= "<sequence>" . $transaction['SEQUENCE_NO'] . "</sequence>";
                    $xml .= "</transaction>";
                }
            }
            $xml .= "</transactions>";
            $xml .= "</active_split>";
        }
        if(count($this->combinations) > 0)
        {
            $xml .= "<configuration>";
            $xml .= "<applicable_combinations>";
            foreach ($this->combinations as $combination) {
                $xml .= $combination->toXML();
            }
            $xml .= "</applicable_combinations>";
            $xml .= "</configuration>";
        }
        return $xml;
    }

    /**
     *
     * This Methode is to produce the Payment Configuration object
     * From phase this method will not required and constructor will fetch configuration from DB directly
     *
     */
    public static function ProduceConfig($_OBJ_DB, int $clientId, array $paymentTypes,string $sessionId)
    {
        if (!empty($clientId)) {
            $configuration = new Configuration();
            if(!empty($sessionId)){
                $currentSplit = 1;
                $sql = "SELECT MAX(SD.sequence_no) as sequence_no FROM LOG".sSCHEMA_POSTFIX.".split_details_tbl SD 
                        INNER JOIN LOG".sSCHEMA_POSTFIX.".split_session_tbl SS on SS.id = SD.split_session_id
                        WHERE SS.sessionid = ".$sessionId." AND SD.payment_status='Success'";
                $res = $_OBJ_DB->getName($sql);
                if (is_array($res) === true)
                {
                    $currentSplit += (int)$res['SEQUENCE_NO'];
                }
                $configuration->setCurrentSplitSeq($currentSplit);
                $sql = "SELECT SD.transaction_id,SD.sequence_no,C.paymenttype FROM LOG".sSCHEMA_POSTFIX.".split_details_tbl SD
                INNER JOIN LOG".sSCHEMA_POSTFIX.".split_session_tbl SS ON SS.id = SD.split_session_id
                INNER JOIN LOG".sSCHEMA_POSTFIX.".transaction_tbl T ON T.id = SD.transaction_id 
                INNER JOIN SYSTEM".sSCHEMA_POSTFIX.".card_tbl C ON  C.id = T.cardid
                WHERE SS.sessionid = ".$sessionId." AND SD.payment_status='Success'";
                $aRS = $_OBJ_DB->getAllNames($sql);
                $activeSplit= array();
                if (is_array($aRS) === true && count($aRS) > 0) {
                    foreach ($aRS as $rs) {
                        $activeSplit[] = $rs;
                    }
                    $configuration->setActiveSplit($activeSplit);
                }
            }
            $paymentTypeString = implode(", ", $paymentTypes);
            $sql = "with q1 as (
                     SELECT split_config_id, count(split_config_id) as allcount 
                     FROM Client". sSCHEMA_POSTFIX .".Split_Combination_Tbl GROUP BY split_config_id),";
            $sql .=  "q2 as (
                        SELECT split_config_id, count(split_config_id) as matchcount
                        FROM Client". sSCHEMA_POSTFIX .".Split_Combination_Tbl WHERE payment_type IN (".$paymentTypeString.") GROUP BY split_config_id)";
            $sql .=  " SELECT q1.split_config_id FROM q1 INNER JOIN q2 on q1.split_config_id = q2.split_config_id and q1.allcount = q2.matchcount;";
            $aRS = $_OBJ_DB->getAllNames($sql);
            $objConfig = array();
            if (is_array($aRS) === true && count($aRS) > 0)
            {
                for($i=0; $i<count($aRS); $i++) {
                    $sqlS  = "SELECT CM.payment_type,CM.sequence_no,CF.is_one_step_auth
                             FROM Client". sSCHEMA_POSTFIX .".Split_Combination_Tbl CM
                             INNER JOIN Client". sSCHEMA_POSTFIX .".Split_Configuration_Tbl CF ON CF.id= CM.split_config_id
                             WHERE CM.split_config_id = ". $aRS[$i]["SPLIT_CONFIG_ID"] ." AND CF.enabled =true ORDER BY CM.sequence_no ASC";
                    $RS = $_OBJ_DB->getAllNames($sqlS);
                    $K=0;
                    if (is_array($RS) === true && count($RS) > 0) {
                        for ($j=0; $j<count($RS); $j++){
                            $objConfig["applicable_combinations"][$i]['payment_type'][$K]['id'] = $RS[$j]["PAYMENT_TYPE"];
                            $objConfig["applicable_combinations"][$i]['payment_type'][$K]['sequence'] = $RS[$j]["SEQUENCE_NO"];
                            $objConfig["applicable_combinations"][$i]['is_one_step_authorization'] = $RS[$j]["IS_ONE_STEP_AUTH"];
                            $K++;
                        }
                    }
                }
            }else{
                return null;
            }
            if (array_key_exists('applicable_combinations', $objConfig)) {
                foreach ($objConfig['applicable_combinations'] as $combinations) {
                    if (array_key_exists('payment_type', $combinations)) {
                        $objCombination = new Combination();
                        foreach ($combinations['payment_type'] as $paymentType) {
                            $objPaymentTypes = new PaymentType((int)$paymentType["id"], (int)$paymentType["sequence"]);
                            $objCombination->setPaymentType($objPaymentTypes);
                        }
                        $objCombination->setIsOneStepAuth($combinations["is_one_step_authorization"]);
                        $configuration->setCombination($objCombination);
                    }
                }
            }
            return $configuration;
        }
        else
        {
            return null;
        }
    }
}