<?php
namespace api\classes\merchantservices\Controllers;

use api\classes\merchantservices\Repositories\IRepository;
use api\classes\merchantservices\Services\MetaDataService;

class MetaDataController
{

    // Define Repository object
    private $merchantConfigRepository;

    // Define Service class objects
    private $objMetaDataService;

    public function __construct(IRepository $merchantConfigRepository)
    {
        $this->merchantConfigRepository = $merchantConfigRepository;
        $this->objConfigurationService = new MetaDataService($merchantConfigRepository);
    }

    public function getSystemMetaData($request, $additionalParams = []) {

    }    

    public function getPaymentMetaData($request, $additionalParams = []) {

    }

}