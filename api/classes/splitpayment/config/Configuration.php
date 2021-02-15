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
    private int $splitCount = 1;
    /**
     * @var Combinations[]
     */
    private array $combinations = [];

    /**
     * Configuration constructor.
     *
     * @param int   $splitCount Max allowed splits for a session
     * @param Combination[] $combinations Array of Split combinations
     */
    public function __construct(int $splitCount, array $combinations = NULL) {
        if($splitCount > 0)
        {
            $this->splitCount = $splitCount;
        }

        if($combinations !== NULL)
        {
            $this->combinations= $combinations;
        }
    }

    /**
     * @return int
     */
    public function getSplitCount(): int
    {
        return $this->splitCount;
    }

    /**
     * @param int $splitCount
     */
    public function setSplitCount(int $splitCount): void
    {
        $this->splitCount = $splitCount;
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

    /**
     * Return Configuration as XML string
     * @return string
     */
    public function toXML(): string
    {
        $xml = "<configuration><splitCount>$this->splitCount</splitCount>";
        if(count($this->combinations) > 0)
        {
            $xml .= "<combinations>";
            foreach ($this->combinations as $combination) {
                $xml .= $combination->toXML();
            }
            $xml .= "</combinations>";
        }
        $xml .= "</configuration>";
        return $xml;
    }

    /**
     *
     * This Methode is to produce the Payment Configuration object
     * From phase this method will not required and constructor will fetch configuration from DB directly
     *
     * @param string $config This param is string for phase 1 of split payment
     *                       From phase two this configuration will come from dedicated config tables
     *
     * @return Configuration|null
     */
    public static function ProduceConfig(string $config): ?Configuration
    {
        if (!empty($config)) {
            $objConfig = json_decode($config, TRUE, 512, JSON_THROW_ON_ERROR);

            $configuration = new Configuration((int)$objConfig['splitCount']);

            foreach ($objConfig['combinations'] as $combination)
            {
                if(array_key_exists('combination',$combination)) {
                    $objCombination = new Combination();
                    foreach ($combination['combination'] as $paymentType) {
                        $objPaymentTypes = new PaymentType((int)$paymentType["index"], (int)$paymentType["id"]);
                        $objCombination->setPaymentType($objPaymentTypes);
                    }
                    $configuration->setCombination($objCombination);
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