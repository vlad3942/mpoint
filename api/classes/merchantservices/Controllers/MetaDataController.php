<?php

namespace api\classes\merchantservices\Controllers;

use api\classes\merchantservices\Services\MetaDataService;
use api\classes\merchantservices\Helpers\Helpers;

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
    public function getSystemMetaData(array $request): string
    {
        $aSystemMetaData = [];
        $xml = '';

        $aSystemMetaData = $this->getMetaDataService()->generateSystemMetaData($request);

        $xml = '<system_metadata>';
        $xml .= Helpers::generateXML($aSystemMetaData);        
        $xml .= '</system_metadata>';

        return $xml;

    }

    /**
     * Handle getPaymentMetaData request
     *
     * @param array $request
     * @return string
     */

    public function getPaymentMetaData(array $request): string 
    {
        $aPaymentMetaData = [];
        $xml = '';

        $aPaymentMetaData = $this->getMetaDataService()->generatePaymentMetaData($request);

        $xml = '<payment_metadata>';
        $xml .= Helpers::generateXML($aPaymentMetaData);        
        $xml .= '</payment_metadata>';

        return $xml;

    }
}
