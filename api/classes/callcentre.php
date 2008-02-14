<?php
/**
 * The Call Centre package provides the business logic for mPoint's Call Centre API.
 * This API will start a new mPoint transaction and generate a WAP Link which is then sent to the recipient.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package CallCentre
 * @version 1.0
 */

/**
 * Model Class containing all the Business Logic for handling an mPoint Transaction initiated by a Call Centre Agent.
 *
 */
class CallCentre extends SMS_Purchase
{	
	/**
	 * Logs the custom variables provided by the Client for easy future retrieval.
	 * Custom variables are defined as an entry in the input arrays which key starts with var_
	 * 
	 * @see 	Constants::iCLIENT_VARS_STATE
	 * @see 	General::newMessage()
	 * 
	 * @param 	array $aInput 	Array of Input as received from the Client.
	 */
	public function logClientVars(array &$aInput)
	{
		$aClientVars = array();
		foreach ($aInput as $key => $val)
		{
			if (substr($key, 0, 4) == "var_") { $aClientVars[$key] = $val; }
		}
		if (count($aClientVars) > 0) { $this->newMessage($this->getTransactionID(), Constants::iCLIENT_VARS_STATE, serialize($aClientVars) ); }
	}
}
?>