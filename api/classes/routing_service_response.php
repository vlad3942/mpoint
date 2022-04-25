<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name:routing_service_response.php
 */

class RoutingServiceResponse
{
    /**
     * Hold dynamically selected routes
     *
     * @var array
     */
    private $_aObj_Route = array();

    /**
     * Hold list of payment methods
     *
     * @var array
     */
    private $_aObj_PaymentMethods = array();

    /**
     * @var array
     */
    private $_aCardsSchemes = array();

    /**
     * Default Constructor
     *
     * @param object  $aObjRoutingServiceResponse 	Hold array object of routing service response
     */
    public function __construct($aObjRoutingServiceResponse)
    {
        if (empty($aObjRoutingServiceResponse->payment_methods) === false)
        {
            $this->_aObj_PaymentMethods = $aObjRoutingServiceResponse;
        }
        if (empty($aObjRoutingServiceResponse->routes) === false)
        {
            $this->_aObj_Route = $aObjRoutingServiceResponse;
        }
        if(empty($aObjRoutingServiceResponse->card_schemes) === false)
        {
            $this->_aCardsSchemes = $aObjRoutingServiceResponse->card_schemes;
        }
    }

    /**
     * Produces a payment methods object.
     *
     * @param 	SimpleXMLElement $aObj_XML 	 List of payment methods
     * @return 	object                       Instance of routing service response
     */
    public static function produceGetRouteResponse(SimpleXMLElement $aObj_XML)
    {
        if (($aObj_XML instanceof SimpleXMLElement) === true)
        {
            $aObjRoute = new \stdClass();
            $aObjRoute->routes = new \stdClass();
            for ($i = 0; $i < count($aObj_XML->routes->route); $i++)
            {
                $aObjRoute->routes->route[$i] = new \stdClass();
                $aObjRoute->routes->route[$i]->id = (int)$aObj_XML->routes->route[$i]->id;
                $aObjRoute->routes->route[$i]->preference = (int)$aObj_XML->routes->route[$i]->preference;
            }
            $aObjRoute->kpi_used = General::xml2bool($aObj_XML->kpi_used);
            return new RoutingServiceResponse($aObjRoute);
        }
        return null;
    }

    /**
     * Produces a dynamically selected routes object.
     *
     * @param 	SimpleXMLElement $aObj_XML 	 List of dynamically selected routes
     * @return 	object                       Instance of routing service response
     */
    public static function produceGetPaymentMethodResponse(SimpleXMLElement $aObj_XML)
    {
        if (($aObj_XML instanceof SimpleXMLElement) === true)
        {
            $iPaymentMethodCount = count($aObj_XML->payment_methods->payment_method);
            if($iPaymentMethodCount > 0){
                $aObjPaymentMethod = new stdClass();
				$aObjPaymentMethod->payment_methods = new stdClass();
                for ($i = 0; $i < $iPaymentMethodCount; $i++)
                {
                    $aObjPaymentMethod->payment_methods->payment_method[$i] = new stdClass();
                    $aObjPaymentMethod->payment_methods->payment_method[$i]->id = (int)$aObj_XML->payment_methods->payment_method[$i]->id;
                    $aObjPaymentMethod->payment_methods->payment_method[$i]->psp_type = (int)$aObj_XML->payment_methods->payment_method[$i]->psp_type;
                    $aObjPaymentMethod->payment_methods->payment_method[$i]->preference = (int)$aObj_XML->payment_methods->payment_method[$i]->preference;
                    $aObjPaymentMethod->payment_methods->payment_method[$i]->state_id = (int)$aObj_XML->payment_methods->payment_method[$i]->state_id;

                    if(isset($aObj_XML->payment_methods->payment_method[$i]->card_schemes) && count($aObj_XML->payment_methods->payment_method[$i]->card_schemes) > 0) {
                        $iPMId = (int)$aObjPaymentMethod->payment_methods->payment_method[$i]->id ?? -1;
                        $iProviderId = WalletProcessor::$aWalletConstants[$iPMId] ?? -1;
                        $aObjPaymentMethod->card_schemes[$iProviderId] = self::generateCardSchemes((array) $aObj_XML->payment_methods->payment_method[$i]->card_schemes);
                    }

                }
                return new RoutingServiceResponse($aObjPaymentMethod);
            }
        }
        return null;
    }

    /**
     * @return object  array object of list of eligible payment methods
     */
    public function getPaymentMethods()
    {
        return $this->_aObj_PaymentMethods;
    }

    /**
     * @return object  array object of dynamically selected routes
     */
    public function getRoutes()
    {
        return $this->_aObj_Route;
    }

    /**
     * @param array $aCardSchemes
     * @return array
     */
    private function generateCardSchemes(array $aCardSchemes) : array
    {
        $aCardSchemeInfo = [];
        foreach ($aCardSchemes['card_scheme'] as $card) {
            $aCardSchemeInfo[] = (int) $card->id;
        }
        return $aCardSchemeInfo;
    }

    /**
     * @return array
     */
    public function getCardSchemes() : array
    {
        return $this->_aCardsSchemes;
    }


}