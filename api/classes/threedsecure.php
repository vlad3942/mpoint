<?php
/**
 * The enclosed class provides model logic for activating 3D Secure for an initialized transaction
 * The model layer assumes that an external 3D secure challenge parser URL has been configured for the active client
 *
 * @author Johan Thomsen
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package API
 * @subpackage ThreeDSecure
 * @version 1.99
 */
class ThreeDSecure extends General
{
	/**
	 * @var ClientConfig
	 */
	private $_obj_ClientConfig;

	/**
	 * Default Constructor
	 *
	 * @param RDB $oDB						Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param TranslateText $oTxt			Text Translation Object for translating any text into a specific language
	 * @param ClientConfig $oClientConfig	Client Configuration object for the sessions active client
	 */
	public function __construct(RDB $oDB, api\classes\core\TranslateText $oTxt, ClientConfig $oClientConfig)
	{
		parent::__construct($oDB, $oTxt);
		$this->_obj_ClientConfig = $oClientConfig;
	}


	/**
	 * Invokes external service for parsing 3D Secure challenge provided.
	 * This will log State: 1100 - 3D Secure Activated for the transaction.
	 * 
	 * @see Constants::i3D_SECURE_ACTIVATED_STATE
	 *
	 * @param TxnInfo $obj_TxnInfo Transaction to activate 3D Secure for
	 * @param SimpleXMLElement $obj_Challenge Challenge to parse for the client
	 * @return HTTPClient For the caller to pull out response body and code
	 * @throws SQLQueryException
	 * @throws mPointException
	 */
	public function parse3DSecureChallenge(TxnInfo $obj_TxnInfo, SimpleXMLElement $obj_Challenge)
	{
		$code = -1;
		try
		{
			$obj_ConnInfo = $this->_obj_ClientConfig->getParse3DSecureChallengeURLConfig()->constConnInfo();

			$b = '<?xml version="1.0" encoding="UTF-8"?>';
			$b .= '<root>';
			$b .= '<parse-3dsecure-challenge>';
			$b .= $obj_TxnInfo->toXML();
			$b .= $obj_Challenge->asXML();
			$b .= '</parse-3dsecure-challenge>';
			$b .= '</root>';

			$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
			$obj_HTTP->connect();
			$code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
			$obj_HTTP->disConnect();

			$this->newMessage($obj_TxnInfo->getID(), Constants::i3D_SECURE_ACTIVATED_STATE, var_export(array("URL" => $this->_obj_ClientConfig->getParse3DSecureChallengeURLConfig()->getURL(), "Response-Code" => $code, "Response-Body "=> $obj_HTTP->getReplyBody() ), true) );
			// Try-parse response
			if ( (simpledom_load_string($obj_HTTP->getReplyBody() ) instanceof SimpleDOMElement) === false)
			{
				throw new mPointException("Could not parse response from 3D Secure Challenge Parser for Transaction ID: ". $obj_TxnInfo->getID(). " using URL: ". $this->_obj_ClientConfig->getParse3DSecureChallengeURLConfig()->getURL(), 94);
			}

			return $obj_HTTP;
		}
		catch (HTTPException $e)
		{
			$this->newMessage($obj_TxnInfo->getID(), Constants::i3D_SECURE_ACTIVATED_STATE, var_export(array("URL" => $this->_obj_ClientConfig->getParse3DSecureChallengeURLConfig()->getURL(), "Exception" => $e), true) );
			throw new mPointException("Communication with 3D Secure Challenge parser failed for Transaction ID: ". $obj_TxnInfo->getID() ." using URL: ". $this->_obj_ClientConfig->getParse3DSecureChallengeURLConfig()->getURL(), 95, $e);
		}
	}
}
