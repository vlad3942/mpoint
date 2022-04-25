<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol.
 * The MobilePay subpackage is a specific implementation capable of imitating MobilePay's own protocol.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage MobilePay
 * @version 1.01
 */

/**
 * Model Class containing all the Business Logic for handling Callback requests from MobilePay.
 *
 */
class MobilePay extends CPMPSP
{
    /**
     * (non-PHPdoc)
     * @param PSPConfig $obj_PSP
     * @param int $euaid
     * @param bool $sc
     * @param int $card_type_id
     * @param string $card_token
     * @param null $obj_BillingAddress
     * @param ClientInfo|null $obj_ClientInfo
     * @param null $authToken
     * @return SimpleXMLElement
     * @see        CPMPSP::initialize()
     *
     * Returns the XML document in the following format:
     * {code}
     *    <?xml version="1.0" encoding="UTF-8"?>
     *    <root>
     *        <url method="app" />
     *        <callback-url>[STRING]</callback-url>
     *    </root>
     * {code}
     *
     * @see        ClientConfig#getMESBURL();
     */
	public function initialize(PSPConfig $obj_PSP,$euaid=-1, $sc=false, $card_type_id=-1, $card_token='', $obj_BillingAddress = NULL, ClientInfo $obj_ClientInfo = NULL, $authToken = NULL, $cardName='',$aWalletCardSchemes = array())
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<root>';
		$xml .= '<url method="app" />';
		$xml .= '<callback-url>'. htmlspecialchars($this->getTxnInfo()->getClientConfig()->getMESBURL() ."/mpoint/danskebank/callback", ENT_NOQUOTES) .'</callback-url>';
		$xml .= '</root>';
		
		return simplexml_load_string($xml);
	}

	public function auth($ticket=null, $apiKey=null, $cardID=null, $storecard=null)  { /* no operation */ }

	public function initCallback(PSPConfig $obj_PSPConfig, TxnInfo $cardid, $txnid, $cardno, $expiry) { /* no operation */ }

	public function getPSPID() { return Constants::iMOBILEPAY_PSP; }
}
