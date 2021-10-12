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
    public function generateSystemMetaData(array $request): array 
    {
        return $this->merchantConfigRepository->getAllSystemMetaDataInfo();
    }    

    /**
     * Generate Payment MetaData
     *
     * @return void
     */
    public function generatePaymentMetaData(array $request): array 
    {
        return $this->merchantConfigRepository->getAllPaymentMetaDataInfo();
    }
}
