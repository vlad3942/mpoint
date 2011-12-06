<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol.
 * The WorldPay subpackage is a specific implementation capable of imitating WorldPay's own protocol.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage Authorize.Net
 * @version 1.00
 */

/**
 * Model Class containing all the Business Logic for handling interaction with Authorize.Net
 *
 */
class AuthorizeNet extends Callback
{
	/**
     * Generates a fingerprint needed for a hosted order form or DPM.
     *
     * @param 	string $id		API Login ID provided by Authorize.Net
     * @param 	string $key		API key provided by Authorize.Net
     * @param 	string $amount	Amount of transaction.
     * @param 	string $txnid	mPoint's unique ID for the Transaction
     * @param 	string $ts		Current timestamp, made using the PHP time() function
     *
     * @return	string
     */
    public function genChecksum($id, $key, $amount, $txnid, $ts)
    {
    	if (function_exists("hash_hmac") === true) { return hash_hmac("md5", $id . "^" . $txnid . "^" . $ts . "^" . $amount . "^", $key); }
    	else { return $this->_hmac("md5", $id . "^" . $txnid . "^" . $ts . "^" . $amount . "^", $key); }
    }
    
	private function _hmac($algo, $data, $key, $raw_output=false)
	{
	    $algo = strtolower($algo);
	    $pack = 'H'.strlen($algo('test'));
	    $size = 64;
	    $opad = str_repeat(chr(0x5C), $size);
	    $ipad = str_repeat(chr(0x36), $size);
	
	    if (strlen($key) > $size) {
	        $key = str_pad(pack($pack, $algo($key)), $size, chr(0x00));
	    } else {
	        $key = str_pad($key, $size, chr(0x00));
	    }
	
	    for ($i = 0; $i < strlen($key) - 1; $i++) {
	        $opad[$i] = $opad[$i] ^ $key[$i];
	        $ipad[$i] = $ipad[$i] ^ $key[$i];
	    }
	
	    $output = $algo($opad.pack($pack, $algo($ipad.$data)));
	
	    return ($raw_output) ? pack($pack, $output) : $output;
	}
}
?>