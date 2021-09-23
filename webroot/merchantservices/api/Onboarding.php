<?php

require_once("../../inc/include.php");
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");

require_once(sCLASS_PATH . "merchantservices/MerchantConfigRepositry.php");

$isRequestValid = true;
$xml = '';
$serviceName = '';
$requestType = '';
$strParam = '';
$arrParams = [];



if(isset($_REQUEST['service'])) 
{
    $serviceName = strtolower($_REQUEST['service']);
}

if(isset($_SERVER['REQUEST_METHOD']) && !empty($_SERVER['REQUEST_METHOD'])) 
{
    $requestType = strtolower($_SERVER['REQUEST_METHOD']);
}

if(isset($_REQUEST['params']) && !empty($_REQUEST['params']))
{
    $strParams = $_REQUEST['params'];
    $arrParams = generateParams($strParams);

    if(!is_array($arrParams)) {
        header("HTTP/1.1 400 Bad Request");
        $xml = '<status code="400">Bad Request</status>';  
        $isRequestValid = false;
    }
}

$obj_DOM = simpledom_load_string(file_get_contents('php://input'));

// Define Routes
$routes = [
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

    'system_metadeta' => [
        'class'   => 'MetaDataController',
        'get'     => 'getSystemMetaData'       
    ],

    'payment_metadeta' => [
        'class'   => 'MetaDataController',
        'get'     => 'getPaymentMetaData' 
    ]
];

if(empty($serviceName) || !isset($routes[$serviceName]['class']) || empty($requestType) || !isset($routes[$serviceName][$requestType])){

    header("HTTP/1.1 400 Bad Request");
    $xml = '<status code="400">Bad Request</status>';  
    $isRequestValid = false;
}

try {

    if($isRequestValid) {

        // Authentication ... to be added

        $contollerName = $routes[$serviceName]['class'];
        $methodName = $routes[$serviceName][$requestType];

        $merchantConfigRepositry = new MerchantConfigRepositry($_OBJ_DB);

        if(file_exists(sCLASS_PATH . "merchantservices/{$contollerName}.php")) {
            include_once(sCLASS_PATH . "merchantservices/{$contollerName}.php");
        } else {
            throw new Exception("Internal error");
        }

        $objController = new $contollerName($merchantConfigRepositry);
        $result = $objController->$methodName($obj_DOM, $arrParams);

        print_r($result);
        // Format Response
        // $xml .= formatResponse($result);
    }

} catch (Exception $e)
{
	header("HTTP/1.1 500 Internal Server Error");
	$xml = '<status code="500">'. $e->getMessage() .'</status>';
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
echo '<root>';
echo $xml;
echo '</root>';