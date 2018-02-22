<?php 

class BreException extends mPointException{}

class Bre extends General
{
    const sHTTP_METHOD = 'POST';
    const sHTTP_TIMEOUT = 120;
    const sHTTP_CONTENT_TYPE = 'text/xml';
    const sBRE_ROUTING_URL = '/bre/get-payment-routes';


    public function getroute(ClientConfig $objClientconfig,HTTPConnInfo &$oCI,$clientid , $aPayInfo , $obj_RoutingRuleInfos )
    {
    	//echo( $aPayInfo->{'client-info'}->mobile["country-id"]);
    	$b = '<?xml version="1.0" encoding="UTF-8"?>';
    	$b .= '<root>';
    	$b .= '<get-routes-request client-id= "' . $clientid . '">';
    	$b .= '<transaction id="' . $aPayInfo->transaction ["id"] . '">';
    	$b .= '<card type-id="' . $aPayInfo->transaction->card ["type-id"] . '">';
    	$b .= '<amount country-id="' . $aPayInfo->transaction->card->amount ["country-id"] . '" currency-id="' . $aPayInfo->transaction->card->amount ["currency-id"] . '">' . $aPayInfo->transaction->card->amount . '</amount>';
    	$b .= '</card>';
    	$b .= '</transaction>';
    	$b .= '<client-info platform="'. $aPayInfo->{'client-info'}['platform'].'" language="'. $aPayInfo->{'client-info'}['language'].'">';
    	$b .= '<mobile country-id="'.$aPayInfo->{'client-info'}->mobile["country-id"].'" operator-id="'.$aPayInfo->{'client-info'}->mobile["operator-id"].'">';
    	$b .=  $aPayInfo->{'client-info'}->mobile.'</mobile>';
    	$b .= '<email>'.$aPayInfo->{'client-info'}->email.'</email>';
    	$b .= '<device-id>'.$aPayInfo->{'client-info'}->{'device-id'}.'</device-id>';
    	$b .= '</client-info>';
    	$b .=  RoutingRule::toXML ( $obj_RoutingRuleInfos );
    	$b .= '</get-routes-request>';
    	$b .= '</root>';
    
    	$aURLInfo = parse_url($objClientconfig->getMESBURL() );
    	$obj_ConnInfo =  new HTTPConnInfo( $aURLInfo["scheme"], $aURLInfo["host"], '10080',  self::sHTTP_TIMEOUT, self::sBRE_ROUTING_URL,self::sHTTP_METHOD,"text/xml", $objClientconfig->getUsername(), $objClientconfig->getPassword() );
    
    	$obj_HTTP = new HTTPClient ( new Template (), $obj_ConnInfo );
    	$obj_HTTP->connect ();
    	$code = $obj_HTTP->send ( $this->constHeader (), $b );
    	$obj_HTTP->disconnect ();
    	$obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
    	return $obj_XML;
    }
    
    protected function constHeader()
    {
    	/* ----- Construct HTTP Header Start ----- */
    	$h = "{METHOD} {PATH} HTTP/1.0" .HTTPClient::CRLF;
    	$h .= "host: {HOST}" .HTTPClient::CRLF;
    	$h .= "referer: {REFERER}" .HTTPClient::CRLF;
    	$h .= "content-length: {CONTENTLENGTH}" .HTTPClient::CRLF;
    	$h .= "content-type: {CONTENTTYPE}; charset=\"UTF-8\"" .HTTPClient::CRLF;
    	$h .= "user-agent: bre" .HTTPClient::CRLF;
    	/* ----- Construct HTTP Header End ----- */
    
    	return $h;
    }
    


}