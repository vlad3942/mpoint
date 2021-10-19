<?php

require_once("../../inc/include.php");
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");

use api\classes\merchantservices\MerchantOnboardingException;

$xml = '';
$serviceName = '';
$requestType = '';
$arrParams = array();

$aXSDSource = array(
    'post' => sPROTOCOL_XSD_PATH ."mpoint-merchantservices.xsd",
    'put'  => sPROTOCOL_XSD_PATH ."mpoint-merchantservices-put.xsd"
);

if(isset($_REQUEST['service'])) 
{
    $serviceName = strtolower($_REQUEST['service']);
}

if(isset($_SERVER['REQUEST_METHOD']) && !empty($_SERVER['REQUEST_METHOD'])) 
{
    $requestType = strtolower($_SERVER['REQUEST_METHOD']);
}

$sSourceXSDFile = isset($aXSDSource[$requestType]) ?  $aXSDSource[$requestType] : sPROTOCOL_XSD_PATH . 'mpoint-merchantservices.xsd';

// Define Routes
$routes = [
    'clientconfig' => [
        'class'   => 'ConfigurationController',
        'get'     => 'getClientConfig',
        'post'    => 'postClientConfig',
        'put'     => 'putClientConfig',
        'delete'  => 'deleteClientConfig',
    ],
    'addonconfig' => [
        'class'   => 'ConfigurationController',
        'get'     => 'getAddonConfig',
        'post'    => 'saveAddonConfig',
        'put'     => 'updateAddonConfig',
        'delete'  => 'deleteAddonConfig'
    ],

    'pspconfig'   => [
        'class'   => 'ConfigurationController',
        'get'     => 'getPSPConfig',
        'post'    => 'savePSPConfig',
        'put'     => 'updatePSPConfig',
        'delete'  => 'deletePSPConfig'        
    ],

    'routeconfig' => [
        'class'   => 'ConfigurationController',
        'get'     => 'getRouteConfig',
        'post'    => 'saveRouteConfig',
        'put'     => 'updateRouteConfig',
        'delete'  => 'deleteRouteConfig'  
    ],

    'system_metadata' => [
        'class'   => 'MetaDataController',
        'get'     => 'getSystemMetaData'       
    ],

    'payment_metadata' => [
        'class'   => 'MetaDataController',
        'get'     => 'getPaymentMetaData' 
    ]
];

try
{
    if(isset($routes[$serviceName]) === false || isset($routes[$serviceName][$requestType]) === false)
    {
        throw new MerchantOnboardingException(MerchantOnboardingException::INVALID_REQUESTED_OPERATION,'In Valid Requested operation');
    }

    if(isset($_REQUEST['params']) && !empty($_REQUEST['params']) && ($requestType === 'get' || $requestType === 'delete'))
    {
        $strParams = $_REQUEST['params'];
        $arrParams = generateParams($strParams);
        if(!is_array($arrParams))
        {
            throw new MerchantOnboardingException(MerchantOnboardingException::INVALID_REQUEST_PARAM,'No Get Parameter Found');
        }

    }
    $clientid = -1;
    if($requestType !== 'get' && $requestType !== 'delete')
    {
        $obj_DOM = simpledom_load_string(file_get_contents('php://input'));

        if(($obj_DOM instanceof SimpleDOMElement) === false)
        {
            throw new MerchantOnboardingException(MerchantOnboardingException::UNSUPPORTED_MEDIA_TYPE, 'Invalid XML Document', );
        }

        if($obj_DOM->validate($sSourceXSDFile) === false)
        {
            $aObj_Errs = libxml_get_errors();

            $sErrorResponse = '';
            for ($i=0; $i<count($aObj_Errs); $i++)
            {
                $sErrorResponse .= '<error">'. htmlspecialchars($aObj_Errs[$i]->message, ENT_NOQUOTES) .'</error>';
            }
            throw new MerchantOnboardingException(MerchantOnboardingException::INVALID_XML, $sErrorResponse);
        }

        if(count($obj_DOM->client_id) > 0)
        {
            $clientid = (int)$obj_DOM->client_id;
            unset($obj_DOM->client_id);
        }
    }
    else if(isset($arrParams['client_id']) === true)
    {
        $clientid = (int)$arrParams['client_id'];
    }

    if($clientid > 0 && Validate::valClient($_OBJ_DB, $clientid) === 100)
    {
        $_OBJ_DB->query("START TRANSACTION");

        $contollerName = $routes[$serviceName]['class'];
        $methodName = $routes[$serviceName][$requestType];

        $contollerName = 'api\\classes\\merchantservices\\Controllers\\' . $contollerName;

        $objController = new $contollerName($_OBJ_DB,$clientid);
        if($requestType === 'get') $xml = $objController->$methodName($arrParams);
        else $xml = $objController->$methodName($obj_DOM, $arrParams);

        $_OBJ_DB->query("COMMIT");
    }
    else throw new MerchantOnboardingException(MerchantOnboardingException::INVALID_REQUEST_PARAM,'Client ID Param Not Found');
}

catch (MerchantOnboardingException $e)
{
    header($e->getHTTPHeader());
    $xml = $e->statusNode();
    trigger_error($e->getMessage(), E_USER_ERROR);
    $_OBJ_DB->query("ROLLBACK");

}
catch (Exception $e)
{
	header("HTTP/1.1 500 Internal Server Error");
	$xml = '<status code="500">'. $e->getMessage() .'</status>';
    $_OBJ_DB->query("ROLLBACK");
    trigger_error("Exception thrown in mApp/api/merchantservices/Onboarding: ". $e->getMessage() ."\n". $e->getTraceAsString(), E_USER_ERROR);
}

function generateParams($strParams) 
{
    $arrUrlComponents = [];
    $arrParams = [];
    $cntParams = 0;

    if(empty($strParams))
    {
        return [];
    }
    
    $arrUrlComponents = explode("/",$strParams);    
    $cntParams = count($arrUrlComponents);

    if($cntParams%2>0){
        return -1;
    }

    for( $i = 0; $i < $cntParams; $i = $i + 2) {
        if(empty($arrUrlComponents[$i]) || empty($arrUrlComponents[$i + 1]))
        {
            return -1;
        }
        $arrParams[$arrUrlComponents[$i]] = $arrUrlComponents[$i + 1];
    }

    return $arrParams;

}

header("Content-Type: text/xml; charset=\"UTF-8\"");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo $xml;
