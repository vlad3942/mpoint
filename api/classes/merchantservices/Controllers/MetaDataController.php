<?php

namespace api\classes\merchantservices\Controllers;

use api\classes\merchantservices\Services\MetaDataService;

/**
 * MetaData Configuration
 * 
 * 
 * @package    Mechantservices
 * @subpackage Controller
 * @author     Ijaj Inamdar <ijaj.inamdar@cellpointmobile.com>
 */
class MetaDataController
{

    /**
     * Database Connection Object 
     *
     * @var RDB
     */
    private $_conn;

    /**
     * Merchant Repository Object
     *
     * @var MerchantConfigRepository
     */
    private $_merchantConfigRepository;

    /**
     * Meta Service Data Object
     *
     * @var MetaDataService
     */
    private $_objMetaDataService;


    /**
     * Client Configuration object
     *
     * @var ClientConfig
     */
    //    private $_objClientConfig;

    /**
     * Constructor function
     *
     * @param MerchantConfigRepository $merchantConfigRepository
     */
    public function __construct(\RDB $conn, int $iClientId)
    {
        $this->_objMetaDataService = new MetaDataService($conn, $iClientId);
    }

    /**
     * Get Service Object
     *
     * @return MetaDataService
     */
    private function getMetaDataService(): MetaDataService
    {
        return $this->_objMetaDataService;
    }

    /**
     * Handle getSystemMetaData request
     *
     * @param array $request
     * @return string
     */
    public function getSystemMetaData($request)
    {

        return $this->getMetaDataService()->generateSystemMetaData($request);
    }

    /**
     * Handle getPaymentMetaData request
     *
     * @param array $request
     * @return string
     */

    public function getPaymentMetaData($request)
    {
        return $this->getMetaDataService()->generatePaymentMetaData($request);

    }
}
