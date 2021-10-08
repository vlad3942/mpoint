<?php
namespace api\classes\merchantservices;

use api\classes\merchantservices\MetaData\Client;
use api\classes\merchantservices\MetaData\ClientPaymentMethodId;
use api\classes\merchantservices\MetaData\ClientUrl;
use api\classes\merchantservices\MetaData\StoreFront;
use api\classes\merchantservices\Repositories\MerchantConfigRepository;

class MerchantConfigInfo
{

    public function __construct()
    {

    }


    public function getAllAddonConfig(MerchantConfigRepository $configRepository) : array
    {
        return $configRepository->getAllAddonConfig();
    }

    public function saveAddonConfig(MerchantConfigRepository $configRepository,array $aAddonConfig)
    {
         $configRepository->saveAddonConfig($aAddonConfig);
    }

    public function updateAddonConfig(MerchantConfigRepository $configRepository,array $aAddonConfig)
    {
         $configRepository->updateAddonConfig($aAddonConfig);
    }

    public function getPropertyConfig(MerchantConfigRepository $configRepository, string $type, string $source,int $id=-1) : array
    {
       return $configRepository->getPropertyConfig($type,$source,$id);
    }

    public function getRoutePM(MerchantConfigRepository $configRepository, int $id=-1) : array
    {
        return $configRepository->getRoutePM($id);
    }

    public function saveRouteConfig(MerchantConfigRepository $configRepository,int $routeConfId, array $aPMIds, array $aPropertyInfo)
    {
         $configRepository->saveRouteConfig($routeConfId,$aPMIds,$aPropertyInfo);

    }

    /**
     * Get Client Configuration details
     * @param \api\classes\merchantservices\Repositories\MerchantConfigRepository $configRepository
     *
     * @return array
     */
    public function getClientConfigurations(MerchantConfigRepository $configRepository): array
    {
        return [
            'info'                  => $this->getClientDetail($configRepository->getClientDetailById()),
            'client_urls'           => $this->getClientURLs($configRepository->getClientURLByClientId()),
            'payment_method_ids'    => $this->getClientPaymentMethodId($configRepository->getPMIdsByClientId()),
            'storefronts'           => $this->getClientStoreFront($configRepository->getStoreFrontByClientId()),
            'storefrontssdsd'       =>  [],
        ];
    }

    /**
     * Get Array of Store Front
     *
     * @param array $storeFront
     *
     * @return array
     */
    private function getClientStoreFront(array $storeFront): array {
        $aStoreFront = [];
        if(!is_array($storeFront) && empty($storeFront) === true) {
            return $aStoreFront;
        }
        foreach ($storeFront as $rs){
            $storeFront = new StoreFront();
            $storeFront->setId($rs["ID"])->setName($rs["NAME"]);
            array_push($aStoreFront, $storeFront);
        }
        return $aStoreFront;
    }

    /**
     * Get Array of Payment Method ID
     * @param $paymentMethodId
     *
     * @return array
     */
    private function getClientPaymentMethodId($paymentMethodId): array{
        $aPMIds = [];
        if(!is_array($paymentMethodId) && empty($paymentMethodId) === true) {
            return $aPMIds;
        }
        foreach ($paymentMethodId as $rs){
            $objPMId = new ClientPaymentMethodId();
            $objPMId->setId($rs["PAYMENT_METHOD_ID"])->setName($rs["NAME"]);
            array_push($aPMIds, $objPMId);
        }
        return $aPMIds;
    }

    /**
     * Get Array of Client URLs
     *
     * @param $urls
     *
     * @return array
     */
    private function getClientURLs($urls): array{
        $aURLs = [];
        if(!is_array($urls) && empty($urls) === true)  return $aURLs;
        foreach ($urls as $rs){
            $objURL = new ClientUrl();
            $objURL->setTypeId($rs["TYPE_ID"])->setName($rs["NAME"])->setValue($rs["VALUE"]);
            array_push($aURLs, $objURL);
        }
        return $aURLs;
    }


    /**
     * Get Array of Client basic details
     *
     * @param $client
     *
     * @return Client Object of client
     */
    private function getClientDetail($client): Client {

        $objClient = new Client();
        $objClient->setId($client['ID'])
            ->setName($client['NAME'])
            ->setUsername($client['USERNAME'])
            ->setSalt($client['SALT'])
            ->setMaxAmount($client['MAXAMOUNT'])
            ->setCountryId($client['COUNTRYID'])
            ->setEmailNotification($client['EMAILRCPT'])
            ->setSmsNotification($client['SMSRCPT']);
        return $objClient;
    }

}