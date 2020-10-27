<?php
/**
 * This files contains the sos verification conditions for mPoint's initialize api, pay api, authorie api
 * The File ensure that all required conditions should be included in the api request 
 * If any condition is false , an error code will be return with message.
 *
 * @author Karishan Kumar
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 */

class Verification
{

  /**
   * verify request data.
   *
   *
   * @param  RDB $_OBJ_DB                     Reference to the Database Object that holds the active connection to the mPoint Database
   * @param  ClientConfig $obj_ClientConfig   Reference to the data object with the Client Configuration
   * @param  SimpleXMLElement $obj_DOM        Reference to the XML document that the Client Information should be constructed from
   * @param  $i                               Reference to the XML document for iterating element number
   * @param  TranslateText OBJ_TXT            Reference to the Text Translation Object
   * @return array
   */

  public static function verify($_OBJ_DB, $obj_ClientConfig, $obj_DOM, $i, $_OBJ_TXT)
  {

    $sosPreference =  $obj_ClientConfig->getAdditionalProperties(Constants::iInternalProperty, "SSO_PREFERENCE");
    if (strtoupper($sosPreference) == 'STRICT') 
    {    
        
        if(isset($obj_DOM->{'initialize-payment'}) ) 
        { 
          $new_obj_DOM = $obj_DOM->{'initialize-payment'} ; 
        }

        if(isset($obj_DOM->{'pay'}) ) 
        { 
          require_once(sCLASS_PATH ."/customer_info.php");
          $new_obj_DOM = $obj_DOM->{'pay'} ; 
        }

        if(isset($obj_DOM->{'authorize-payment'}) ) 
        { 
          require_once(sCLASS_PATH ."/mobile_web.php");
          $new_obj_DOM = $obj_DOM->{'authorize-payment'} ; 
        }

        $authToken = isset($new_obj_DOM[$i]->{'auth-token'}) ? trim($new_obj_DOM[$i]->{'auth-token'}) : false ;     
        $authenticationURL = $obj_ClientConfig->getAuthenticationURL();
        if (empty($authToken)=== false && empty($authenticationURL) === false) 
        { 
                    
            $client_id = $new_obj_DOM[$i]["client-id"] ; 
            $customer_ref = isset($new_obj_DOM[$i]->{'client-info'}->{'customer-ref'}) ? (string) $new_obj_DOM[$i]->{'client-info'}->{'customer-ref'} : '' ; 

            $customer_mob = isset($new_obj_DOM[$i]->{'client-info'}->mobile) ? intval($new_obj_DOM[$i]->{'client-info'}->mobile) : '' ; 
            $customer_country_id = isset($new_obj_DOM[$i]->{'client-info'}->mobile["country-id"]) ? intval($new_obj_DOM[$i]->{'client-info'}->mobile["country-id"]) : '' ; 
            $customer_email = isset($new_obj_DOM[$i]->{'client-info'}->email) ? (string)$new_obj_DOM[$i]->{'client-info'}->email : '' ;
            $customer_language = isset($new_obj_DOM[$i]->{'client-info'}["language"]) ? (string)$new_obj_DOM[$i]->{'client-info'}["language"] : '' ;
          $customer_profileid = isset($new_obj_DOM[$i]->{'client-info'}["profileid"]) ? $new_obj_DOM [$i]->{'client-info'}["profileid"] : '' ;
          
          if (strlen($customer_ref) > 0 ||  strlen($customer_mob) > 0   || strlen($customer_email) > 0 || empty(
              $customer_profileid) === false) 
          {  
            
            $obj_CustomerInformation = new CustomerInfo(0, $customer_country_id, $customer_mob, (string)$customer_email, $customer_ref, "",  
          $customer_language, $customer_profileid);
            $obj_Customer = simplexml_load_string($obj_CustomerInformation->toXML());
            $obj_CustomerInformation = CustomerInfo::produceInfo($obj_Customer);        
            $obj_mPoint = new MobileWeb($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);
            $code = $obj_mPoint->auth($obj_ClientConfig, $obj_CustomerInformation, $authToken, (integer)$client_id);
            if ($code != 10) 
            {
                return ['code' => 213, 'msg' => 'Profile authentication failed'];
            }

            return [];
          } 
          else 
          {
             return ['code' => 212, 'msg' => 'Mandatory fields are missing'];
          }                                    
        } 
        else 
        { 
            if (empty($authToken) === true)
            {
              return ['code' => 211, 'msg' => 'Auth token or SSO token not received'] ;
            }
            
            return ['code' => 209, 'msg' => 'Auth url not configured'] ;
        }
        
    }

    return [];
  }

}
