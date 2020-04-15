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
        if (empty($aObjRoutingServiceResponse->psps) === false)
        {
            $this->_aObj_Route = $aObjRoutingServiceResponse;
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
            $iRouteCount = count($aObj_XML->psps->psp);
            $aObjRoute = new stdClass();
            for ($i = 0; $i < $iRouteCount; $i++)
            {
                $aObjRoute->psps->psp[$i]->id = (int)$aObj_XML->psps->psp[$i]->id;
                $aObjRoute->psps->psp[$i]->preference = (int)$aObj_XML->psps->psp[$i]->preference;

            }
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
            $aObjPaymentMethod = new stdClass();
            for ($i = 0; $i < $iPaymentMethodCount; $i++)
            {
                $aObjPaymentMethod->payment_methods->payment_method[$i]->id = (int)$aObj_XML->payment_methods->payment_method[$i]->id;
                $aObjPaymentMethod->payment_methods->payment_method[$i]->psp_type = (int)$aObj_XML->payment_methods->payment_method[$i]->psp_type;
                $aObjPaymentMethod->payment_methods->payment_method[$i]->preference = (int)$aObj_XML->payment_methods->payment_method[$i]->preference;

            }
            return new RoutingServiceResponse($aObjPaymentMethod);
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


}