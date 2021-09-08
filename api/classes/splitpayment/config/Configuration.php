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
            $applicableCombinations = \General::getApplicableCombinations($_OBJ_DB,$paymentTypes,$clientId,$sessionId);
            if($applicableCombinations) {
                $objConfig      = $applicableCombinations['objConfig'];
                $configuration  = $applicableCombinations['configuration'];
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
        }
        return null;
    }
}