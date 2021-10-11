<?php

namespace api\classes\merchantservices\Services;


use api\classes\merchantservices\MerchantConfigInfo;
use api\classes\merchantservices\Repositories\MerchantConfigRepository;

/**
 * MetaData Service
 * 
 * 
 * @package    Mechantservices
 * @subpackage Service Class
 * @author     Ijaj Inamdar <ijaj.inamdar@cellpointmobile.com>
 */
class MetaDataService
{
    /**
     * Merchant Repository Object
     *
     * @var MerchantConfigRepository
     */
    private MerchantConfigRepository $merchantConfigRepository;


    /**
     * Constructor function
     *
     * @param \RDB $conn
     * @param integer $iClientId
     */
    public function __construct(\RDB &$conn, int $iClientId)
    {
        $this->merchantConfigRepository = new MerchantConfigRepository($conn, $iClientId);
        $this->merchantAggregateRoot = new MerchantConfigInfo();
    }

    /**
     * Generate System MetaData
     *
     * @param SimpleDOMElement $request     
     * @return string
     */
    public function generateSystemMetaData($request)
    {

        $xml = '';
        $aSystemMetaData = [];

        $aSystemMetaData = $this->merchantConfigRepository->getAllSystemMetaDataInfo();

        $xml = '<system_metadata>';
        $xml .= $this->generateSystemMetaXML($aSystemMetaData);
        $xml .= '</system_metadata>';

        return $xml;
    }    

    /**
     * Function to consolidate sub modules for Metadata
     *
     * @param array $aData
     * @return string
     */
    public function generateSystemMetaXML($aData): string
    {
        $xml = '';

        foreach ($aData as $key => $metadata) {
            $xml .= "<{$key}>";
            if (!empty($metadata) && is_array($metadata)) {
                foreach ($metadata as $data) {
                    if (!empty($data->getRootNode())) {
                        $sRootNode = $data->getRootNode();
                        $xml .= "<{$sRootNode}>";
                        $xml .= $data->toXML();
                        if (isset($data->additionalProp)) {
                            $xml .= $this->generateSystemMetaXML($data->additionalProp);
                        }
                        $xml .= "</{$sRootNode}>";
                    } else {
                        $xml .= $data->toXML();
                    }
                }
            }
            $xml .= "</{$key}>";
        }
        return $xml;
    }

    /**
     * Generate Payment MetaData
     *
     * @return void
     */
    public function generatePaymentMetaData()
    {

        $xml = '';
        $aPaymentMetaData = [];

        $aPaymentMetaData = $this->merchantConfigRepository->getAllPaymentMetaDataInfo();

        $xml = '<payment_metadata>';
        $xml .= $this->generateSystemMetaXML($aPaymentMetaData);
        $xml .= '</payment_metadata>';

        return $xml;
    }
}
