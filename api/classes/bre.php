<?php

class BreException extends mPointException{}

class Bre 
{
	
	/**
	 * Handles the active database connection
	 *
	 * @var RDB
	 */
	private $_obj_DB;
	/**
	 * Handles the translation of text strings into a specific language
	 *
	 * @var TranslateText
	 */
	private $_obj_Txt;
	
	
    const sHTTP_METHOD = 'POST';
    const sHTTP_TIMEOUT = 120;
    const sHTTP_CONTENT_TYPE = 'text/xml';
    const sBRE_ROUTING_URL = '/bre/get-payment-routes';
    
    
    public function __construct(RDB $oDB, TranslateText $oTxt) {
    	$this->_obj_Txt = $oTxt;
    	$this->_obj_DB = $oDB;
    }


    public function getroute(TxnInfo $obj_TxnInfo,HTTPConnInfo &$oCI,$clientid , $aPayInfo  )
    {
    	
    	$objClientconfig = $obj_TxnInfo->getClientConfig ();
    	//echo( $aPayInfo->{'client-info'}->mobile["country-id"]);
    	$b = '<?xml version="1.0" encoding="UTF-8"?>';
    	$b .= '<root>';
    	$b .= '<get-routes-request client-id= "' . $clientid . '">';
    	$b .= '<transaction id="' . $aPayInfo->transaction ["id"] . '" product-type="'.$obj_TxnInfo->getProductType() .'">';
    	$b .= '<card type-id="' . $aPayInfo->transaction->card ["type-id"] . '">';
    	$b .= '<amount country-id="' . $aPayInfo->transaction->card->amount ["country-id"] . '" currency-id="' . $aPayInfo->transaction->card->amount ["currency-id"] . '">' . $aPayInfo->transaction->card->amount . '</amount>';
    	$b .= '</card>';
    	$b .= '</transaction>';
    	$b .=  $this->getGatewayConfigurations() ;
    	$b .= '<client-info platform="'. $aPayInfo->{'client-info'}['platform'].'" language="'. $aPayInfo->{'client-info'}['language'].'">';
    	$b .= '<mobile country-id="'.$aPayInfo->{'client-info'}->mobile["country-id"].'" operator-id="'.$aPayInfo->{'client-info'}->mobile["operator-id"].'">';
    	$b .=  $aPayInfo->{'client-info'}->mobile.'</mobile>';
    	$b .= '<email>'.$aPayInfo->{'client-info'}->email.'</email>';
    	$b .= '<device-id>'.$aPayInfo->{'client-info'}->{'device-id'}.'</device-id>';
    	$b .= '</client-info>';
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
    
    
    private function getGatewayConfigurations(){
    	
    	$b = "";
		$sql = "SELECT gst.gatewayid, gst.clientid, gst.statetypeid,gst.statvalue FROM Client." . sSCHEMA_POSTFIX . "gatewaytrigger_tbl gtr JOIN Client." . sSCHEMA_POSTFIX . "gatewaystat_tbl gst 
            ON (gtr.gatewayid = gst.gatewayid) AND (gtr.clientid = gst.clientid) AND gtr.enabled = 't' AND gst.enabled = 't'";
		
		$aRS = $this->_obj_DB->getAllNames($sql);
		
		if (is_array ( $aRS ) === true && count ( $aRS ) > 0) {
			$b .= '<gateway-statistics>' ;
			for($i = 0; $i < count ( $aRS ); $i ++) {
				$b .= '<gateway-statistic gateway-id="'.$aRS[$i]["GATEWAYID"].'" type-id="'.$aRS[$i]["STATETYPEID"].'">' ;
				$b .= $aRS[$i]["STATVALUE"] ;
				$b .= '</gateway-statistic>' ;
			}
			$b .= '</gateway-statistics>' ;
		}
		//echo $b ;
		return $b;
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