<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol.
 * The DIBS' subpackage is a specific implementation capable of imitating DIBS' own protocol.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage DIBS
 * @version 1.0
 */

/**
 * Model Class containing all the Business Logic for handling Callback requests from DIBS.
 *
 */
class DIBS extends Callback 
{	
	/**
	 * Notifies the Client of the Payment Status by performing a callback via HTTP.
	 * The method will re-construct the data received from DIBS after having removed the following mPoint specific fields:
	 * 	- width
	 * 	- height
	 * 	- format
	 * 	- PHPSESSID (found using PHP's session_name() function)
	 * 	- language
	 * 	- cardid
	 * Additionally the method will add mPoint's Unique ID for the Transaction.
	 * 
	 * @see 	Callback::notifyClient()
	 * @see 	Callback::send()
	 *
	 * @param 	integer $sid 	Unique ID of the State that the Transaction terminated in
	 * @param 	array $_post 	Array of data received from DIBS via HTTP POST
	 */
	public function notifyClient($sid, array $_post)
	{
		// Client is configured to use mPoint's protocol
		if ($this->getTxnInfo()->getClientConfig()->getMethod() == "mPoint")
		{
			parent::notifyClient($sid);
		}
		// Client is configured to use DIBS' protocol
		else
		{
			// Remove mPoint specific data fields from Callback request
			unset($_post["width"]);
			unset($_post["height"]);
			unset($_post["format"]);
			unset($_post[session_name()]);
			unset($_post["language"]);
			unset($_post["cardid"]);
			// Replace data fields previously overwritten by mPoint
			$_post["orderid"] = $this->getTxnInfo()->getOrderID();
			$_post["callbackurl"] = $this->getTxnInfo()->getCallbackURL();
			$_post["accepturl"] = $this->getTxnInfo()->getAcceptURL();
			// Get custom Client Variables
			$_post = array_merge($_post, $this->getMessageData($txnid, Constants::iCLIENT_VARS_STATE) );
			
			// Re-Construct DIBS request
			$sBody = "mpoint-id=". $this->getTxnInfo()->getID();
			foreach ($_post as $key => $val)
			{
				$sBody .= "&". $key ."=". urlencode($val);
			}
			
			$this->send($sBody);
		}
	}
}
?>