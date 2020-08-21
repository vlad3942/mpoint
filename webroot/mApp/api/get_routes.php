<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Digital
 * Link: https://cellpointdigital.com/
 * Project: server
 * Package:
 * File Name:get_routes.php
 */

// Require Global Include File
require_once("../../inc/include.php");
// Require data class for Client routes config
require_once(sCLASS_PATH ."/client_routes_config.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH . "/validate.php");


if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
    $clientId = (integer)$_REQUEST['client_id'];

    $code = Validate::valClient($_OBJ_DB, $clientId);
    if ($code === 100)
    {
        $obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, $clientId);
        if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW']))
        {
            $obj_RoutesConfig = ClientRoutesConfig::produceConfig($_OBJ_DB, $clientId);
            $routesCount = count($obj_RoutesConfig);
            $xml = '';
            $xml .= '<routes>';
            for($i=0; $routesCount > $i; $i++)
            {
                if(($obj_RoutesConfig[$i] instanceof ClientRoutesConfig) === true )
                {
                    $xml .= $obj_RoutesConfig[$i]->toXML();
                }
            }
            $xml .= '</routes>';
        }
        else
        {
            header("HTTP/1.1 401 Unauthorized");

            $xml = '<status code="401">Username / Password doesn\'t match</status>';
        }

    }
    elseif ($code === 2)
    {
        header("HTTP/1.1 400 Bad Request");

        $xml = '<status code="' . $code . '">Invalid Client ID</status>';
    }
    elseif ($code === 3)
    {
        header("HTTP/1.1 400 Bad Request");

        $xml = '<status code="' . $code . '">Unknown Client ID</status>';
    }
    elseif ($code === 4)
    {
        header("HTTP/1.1 400 Bad Request");

        $xml = '<status code="' . $code . '">Client Disabled</status>';
    }
    else
    {
        header("HTTP/1.1 400 Bad Request");

        $xml = '<status code="' . $code . '">Undefined Client ID</status>';
    }
}
else
{
    header("HTTP/1.1 401 Unauthorized");

    $xml = '<status code="401">Authorization required</status>';
}
header("Content-Type: text/xml; charset=\"UTF-8\"");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<route_response>';
echo $xml;
echo '</route_response>';

exit;


