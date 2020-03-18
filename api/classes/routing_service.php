<?php

class RoutingServiceException extends mPointException{}

class RoutingService extends EndUserAccount
{
    /**
     * Data object with the Transaction Information
     *
     * @var TxnInfo
     */
    private $_obj_TxnInfo;

    const sHTTP_METHOD = 'POST';
    const sHTTP_TIMEOUT = 120;
    const sHTTP_CONTENT_TYPE = 'text/xml';
    const sROUTING_SERVICE_URL = '/routing/get-payment-methods';

    /**
     * Default Constructor
     *
     * @param	RDB $oDB			Reference to the Database Object that holds the active connection to the mPoint Database
     * @param	TranslateText $oDB 	Reference to the Text Translation Object for translating any text into a specific language
     * @param	TxnInfo $oTI 		Reference to the Data object with the Transaction Information
     */
    public function __construct(RDB &$oDB, TranslateText &$oTxt, TxnInfo &$oTI)
    {
        parent::__construct($oDB, $oTxt, $oTI->getClientConfig() );

        $this->_obj_TxnInfo = $oTI;
    }

    /**
     * Returns the XML payload of list of eligible payment methods to process transaction
     *
     * @param 	object $aInitInfo 	 initialize payment transaction object
     * @return 	XML payload of eligible payment methods
     */
    public function getCards($aInitInfo)
    {
        $aObj_XML = $this->getPaymentMethods($aInitInfo);
        $xml = null;
        if(empty($aObj_XML)===false) {

            $paymentMethods = $aObj_XML->payment_methods->payment_method;
            $data = array();
            for ($i = 0; $i < count($paymentMethods); $i++) {
                $data[(integer)$paymentMethods[$i]->id] = (integer)$paymentMethods[$i]->psp_type;
            }

            $sql = "SELECT DISTINCT C.position, C.id, C.name, C.minlength, C.maxlength, C.cvclength, C.paymenttype, PP.currencyid AS currency
				FROM System" . sSCHEMA_POSTFIX . ".Card_Tbl C
				INNER JOIN System" . sSCHEMA_POSTFIX . ".CardPricing_Tbl CP ON C.id = CP.cardid
				INNER JOIN System" . sSCHEMA_POSTFIX . ".PricePoint_Tbl PP ON CP.pricepointid = PP.id AND PP.currencyid = " . $this->_obj_TxnInfo->getCurrencyConfig()->getID() . " AND PP.amount = -1 AND PP.enabled = '1'
				WHERE C.id IN (" . (implode(',', array_keys($data))) . ")
				AND C.enabled = '1'
				ORDER BY C.name ASC";

            $res = $this->getDBConn()->query($sql);

            $xml = '<cards accountid="' . $this->_obj_TxnInfo->getAccountID() . '">';
            while ($RS = $this->getDBConn()->fetchName($res)) {
                $aRS = array();
                // Transaction instantiated via SMS or "Card" is NOT Premium SMS
                if ($this->_obj_TxnInfo->getGoMobileID() > -1 || $RS["ID"] != 10) {
                    // My Account
                    if ($RS["ID"] == 11) {
                        // Only use Stored Cards (e-money based prepaid account will be unavailable)
                        if (($this->_obj_TxnInfo->getClientConfig()->getStoreCard() & 1) == 1) {
                            $sName = $this->getText()->_("Stored Cards");
                        } else {
                            $sName = str_replace("{CLIENT}", $this->_obj_TxnInfo->getClientConfig()->getName(), $this->getText()->_("My Account"));
                        }
                    } else {
                        $sName = $RS["NAME"];

                        $sql = "SELECT min, \"max\"
							FROM System" . sSCHEMA_POSTFIX . ".CardPrefix_Tbl
							WHERE cardid = " . $RS["ID"];

                        $aRS = $this->getDBConn()->getAllNames($sql);
                    }

                    if (isset($data[$RS['ID']])) {
                        $RS['PSP_TYPE'] = $data[$RS['ID']];
                    }

                    //Default logo dimensions
                    $iWidth = 180;
                    $iHeight = 115;

                    $enabled = true;
                    // Construct XML Document with card data
                    $xml .= '<item id="' . $RS["ID"] . '" type-id="' . $RS["ID"] . '" min-length="' . $RS["MINLENGTH"] . '" max-length="' . $RS["MAXLENGTH"] . '" cvc-length="' . $RS["CVCLENGTH"] . '" payment-type="' . $RS['PAYMENTTYPE'] . '"' . ' preferred="' . General::bool2xml($RS['PREFERRED']) . '"' . ' enabled = "' . General::bool2xml($enabled) . '"' . ' processor-type = "' . $RS['PSP_TYPE'] . '" >';
                    $xml .= '<name>' . htmlspecialchars($sName, ENT_NOQUOTES) . '</name>';
                    $xml .= '<logo-width>' . $iWidth . '</logo-width>';
                    $xml .= '<logo-height>' . $iHeight . '</logo-height>';
                    $xml .= '<currency>' . $RS["CURRENCY"] . '</currency>';
                    if (is_array($aRS) === true && count($aRS) > 0) {
                        $xml .= '<prefixes>';
                        for ($i = 0; $i < count($aRS); $i++) {
                            $xml .= '<prefix>';
                            $xml .= '<min>' . $aRS[$i]["MIN"] . '</min>';
                            $xml .= '<max>' . $aRS[$i]["MAX"] . '</max>';
                            $xml .= '</prefix>';
                        }
                        $xml .= '</prefixes>';
                    } else {
                        $xml .= '<prefixes />';
                    }
                    $xml .= '</item>';
                }
            }
            $xml .= '</cards>';
        }
        return $xml;
    }


    /**
     * Returns the XML payload of list of eligible payment methods used by the client.
     *
     * @param 	object $aInitInfo 	 initialize payment transaction object
     * @return 	array $obj_XML       List of eligible payment methods
     */
    private function getPaymentMethods($aInitInfo)
    {
        $objClientconfig = $this->_obj_TxnInfo->getClientConfig ();

        $b = '<?xml version="1.0" encoding="UTF-8"?>';
        $b .= '<payment_method_search_criteria>';
        $b .= '<transaction>';
        $b .= '<amount>';
        $b .= '<value>'.$aInitInfo->transaction->amount.'</value>';
        $b .= '<country_id>'.$aInitInfo->transaction->amount["country-id"].'</country_id>';
        $b .= '<currency_id>'.$aInitInfo->transaction->amount["currency-id"].'</currency_id>';
        $b .= '</amount>';
        $b .= '<type_id>'.$aInitInfo->transaction["type-id"].'</type_id>';
        $b .= '<order_no>'.$aInitInfo->transaction["order-no"].'</order_no>';
        $b .= '</transaction>';
        $b .= '<client_info>';
        $b .= '<platform>'.$aInitInfo->{'client-info'}["platform"].'</platform>';
        $b .= '<language>'.$aInitInfo->{'client-info'}["language"].'</language>';
        $b .= '<version>'.$aInitInfo->{'client-info'}["version"].'</version>';
        $b .= '<mobile>';
        $b .= '<value>'.$aInitInfo->{'client-info'}->mobile.'</value>';
        $b .= '<operator-id>'.$aInitInfo->{'client-info'}->mobile["operator-id"].'</operator-id>';
        $b .= '<country_id>'.$aInitInfo->{'client-info'}->mobile["country-id"].'</country_id>';
        $b .= '</mobile>';
        $b .= '<email>'.$aInitInfo->{'client-info'}->email.'</email>';
        $b .= '<customer_reference>'.$aInitInfo->{'client-info'}->{'customer-ref'}.'</customer_reference>';
        $b .= '<client_id>'.$aInitInfo["client-id"].'</client_id>';
        $b .= '<device_id>'.$aInitInfo->{'client-info'}->{'device-id'}.'</device_id>';
        $b .= '</client_info>';
        $b .= '</payment_method_search_criteria>';

        $obj_XML = null;
        $aURLInfo = parse_url($objClientconfig->getMESBURL() );
        $obj_ConnInfo =  new HTTPConnInfo( 'http', $aURLInfo["host"], '10080',  self::sHTTP_TIMEOUT, self::sROUTING_SERVICE_URL,self::sHTTP_METHOD,"text/xml", $objClientconfig->getUsername(), $objClientconfig->getPassword() );

        $obj_HTTP = new HTTPClient ( new Template (), $obj_ConnInfo );
        $obj_HTTP->connect ();
        $code = $obj_HTTP->send ( $this->constHeader (), $b );
        $obj_HTTP->disconnect ();
        if ($code == 200)
        {
            $obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
        }
        return $obj_XML;
    }

    /**
     * Construct standard HTTP Headers for the HTTP Request
     *
     * @return string
     */
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