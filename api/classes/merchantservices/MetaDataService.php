<?php

class MetaDataService 
{
    private $merchantConfigRepositry;
    
    public function __construct($merchantConfigRepositry)
    {
        $this->merchantConfigRepositry = $merchantConfigRepositry;
    }

    public function getSystemMetaData($request, $additionalParams = []) {

    }    

    public function getPaymentMetaData($request, $additionalParams = []) {

    }
}