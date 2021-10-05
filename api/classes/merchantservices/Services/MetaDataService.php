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
     * Generate System Meta Data
     *
     * @param SimpleDOMElement $request     
     * @return string
     */
    public function generateSystemMetaData($request)
    {

        $xml = '';
        $aSystemMetaData = [];

        $aSystemMetaData['psps'] = $this->merchantConfigRepository->getPSPInfo();
        $aSystemMetaData['payment_methods'] = $this->merchantConfigRepository->getPaymentMethods();
        $aSystemMetaData['countries'] = $this->merchantConfigRepository->getCountries();
        $aSystemMetaData['currencies'] = $this->merchantConfigRepository->getCurrencies();
        $aSystemMetaData['provider_details'] = $this->merchantConfigRepository->getProviderDetails();
        $aSystemMetaData['capture_types'] = $this->merchantConfigRepository->getCaptureTypes();
        $aSystemMetaData['services'] = $this->merchantConfigRepository->getServices();
        $aSystemMetaData['urls'] = $this->merchantConfigRepository->getUrlInfo();
        $aSystemMetaData['property_details'] = $this->merchantConfigRepository->getProperties();

        $xml = '<system_metadata>';
        $xml .= $this->generateSystemMetaXML($aSystemMetaData);
        $xml .= '</system_metadata>';

        return $xml;
    }

    public function generateSystemMetaXML($aData): string
    {
        $xml = '';

        foreach ($aData as $key => $metadata) {
            $xml .= "<{$key}>";
            foreach ($metadata as $data) {
                $xml .= $data->toXML();
            }
            $xml .= "</{$key}>";
        }

        return $xml;
    }

    public function getPaymentMetaData()
    {
    }
}
