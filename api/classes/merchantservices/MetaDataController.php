<?php
require_once('MetaDataService.php');

class MetaDataController
{

    // Define Repository object
    private $merchantConfigRepositry;

    // Define Service class objects
    private $objMetaDataService;

    public function __construct($merchantConfigRepositry)
    {
        $this->merchantConfigRepositry = $merchantConfigRepositry;
        $this->objConfigurationService = new MetaDataService($merchantConfigRepositry);
    }

    public function getSystemMetaData($request, $additionalParams = []) {

    }    

    public function getPaymentMetaData($request, $additionalParams = []) {

    }

}