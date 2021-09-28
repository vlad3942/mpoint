<?php
namespace api\classes\merchantservices\Controllers;

use api\classes\merchantservices\Repositories\IRepository;
use api\classes\merchantservices\Services\MetaDataService;

class MetaDataController
{


    // Define Service class objects
    private $objMetaDataService;

    public function __construct(\RDB &$conn,int $iClientId)
    {
        $this->objConfigurationService = new MetaDataService($conn,$iClientId);
    }
    public function getSystemMetaData( $additionalParams = []) {

    }    

    public function getPaymentMetaData($additionalParams = []) {

    }

}