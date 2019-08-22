<?php

class api {
    
    private $endpoint;
    private $request_xml;
    
    function __construct($enpoint = null) {
        $this->request_xml = null;
        $this->endpoint = $enpoint;
    }
    
    /**
     * Prepare XML request which will used to save card
     * @param object $data Data object of card details
     */
    function createRequest($data = null) {
        $this->request_xml = '<?xml version="1.0" encoding="utf-8"?>';
        $this->request_xml .= '<root>';
        $this->request_xml .= '<save-account account="100110" client-id="10062" mvault="true">';
        $this->request_xml .= '<card type-id="8">';
        $this->request_xml .= '<name>'.$data->CardType.'</name>';
        $this->request_xml .= '<card-holder-name>'.$data->cardHolderName.'</card-holder-name>';
        $this->request_xml .= '<card-number>'.$data->CardNumber.'</card-number>';
        $this->request_xml .= '<expiry-month>'.$data->ExpiryMonth.'</expiry-month>';
        $this->request_xml .= '<expiry-year>'.$data->ExpiryYear.'</expiry-year>';
        $this->request_xml .= '</card>';
        $this->request_xml .= '<auth-token>profilesuccessvalidation</auth-token>';
        $this->request_xml .= '<client-info language="us" version="1.28" platform="iOS/10.3.1">';
        $this->request_xml .= '<customer-ref>'.$data->CustomerID.'</customer-ref>';
        $this->request_xml .= '<email>'.$data->Email.'</email>';
        $this->request_xml .= '</client-info>';
        $this->request_xml .= '</save-account>';
        $this->request_xml .= '</root>';
    }
    
    /**
     * API Call using curl
     */
    function call() {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $this->request_xml,
            CURLOPT_HTTPHEADER => array(
                "authorization: Basic ".base64_encode("miride:7!tBmpSD#"),
                "cache-control: no-cache",
                "content-type: text/xml"
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        
        return simplexml_load_string($response);
    }
    
}

?>