<?php
namespace api\classes\merchantservices\Services;

use api\classes\merchantservices\Repositories\IRepository;

class MetaDataService 
{
    private $merchantConfigRepository;
    
    public function __construct(IRepository $merchantConfigRepository)
    {
        $this->merchantConfigRepository = $merchantConfigRepository;
    }

    public function getSystemMetaData($request, $additionalParams = []) {

    }    

    public function getPaymentMetaData($request, $additionalParams = []) {

    }
}