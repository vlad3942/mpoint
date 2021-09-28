<?php
namespace api\classes\merchantservices\Services;


use api\classes\merchantservices\MerchantConfigInfo;
use api\classes\merchantservices\Repositories\MerchantConfigRepository;

class MetaDataService
{
    private MerchantConfigRepository $merchantConfigRepository;
    private MerchantConfigInfo $merchantAggregateRoot;
    
    public function __construct(\RDB &$conn,int $iClientId)
    {
        $this->merchantConfigRepository = new MerchantConfigRepository($conn,$iClientId);
        $this->merchantAggregateRoot = new MerchantConfigInfo();
    }

    public function getSystemMetaData($request, $additionalParams = []) {

    }    

    public function getPaymentMetaData($request, $additionalParams = []) {

    }
}