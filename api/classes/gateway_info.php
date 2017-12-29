<?php
/**
 * 
 *
 * @author Deblina Das
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Info
 * @subpackage TxnInfo
 * @version 2.01
 */

/**
 * Data class for hold all data relevant for an Rule Condition
 */
class GatewayInfo {
	/**
	 * Unique ID for the Transaction
	 *
	 * @var integer
	 */
	private $_iID;
	
	/**
	 * Field name of the condition e.g "Wirecard","Worldpay" etc
	 *
	 * @var string
	 */
	private $_sName;
	/**
	 * Operator that define the relation between condition field and value e.g "<" , ">=" etc
	 *
	 * @var string
	 */
	private $_bAvailable;
	/**
	 * The value of the condition
	 *
	 * @var string
	 */
	private $_iPreference;
	public function __construct($id, $name, $available, $preference) {
		$this->_iID = ( integer ) $id;
		$this->_sName = trim ( $name );
		$this->_bAvailable = ( bool ) $available;
		$this->_iPreference = ( integer ) $preference;
	}
	
	/**
	 * Produces a new instance of a Gateway Info Object.
	 *
	 * @param RDB $oDB
	 *        	Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param integer $id
	 *        	Unique ID for the Gateway the request is performed in
	 * @return GatewayInfo
	 */
	public static function produceConfig(RDB &$oDB, $ruleId) {
		
		// Availability of Gateway to be retrieve later from Gateway performance monitoring system
		$sql = "SELECT gatewayid AS id ,PT.name AS name, preference AS pref,'true' AS available from client.routing_tbl RT 
               JOIN system.Psp_tbl PT ON RT.gatewayid = PT.id WHERE ruleid =" . intval ( $ruleId );
		
		$res = $oDB->query ( $sql );
		$aMessages = array ();
		while ( $RS = $oDB->fetchName ( $res ) ) {
			$aMessages [] = new GatewayInfo ( $RS ["ID"], $RS ["NAME"], $RS ["AVAILABLE"], $RS ["PREF"] );
		}
		
		return $aMessages;
	}
	public static function toXML($oGatewayInfo) {
		$xml = '<gateways>';
		foreach ( $oGatewayInfo as $aGatewayObj ) {
			$xml = $xml . '<gateway  id="' . $aGatewayObj->_iID . '" name ="' . $aGatewayObj->_sName . '" preference ="' . $aGatewayObj->_iPreference . '" available ="' . General::bool2xml ( $aGatewayObj->_bAvailable ) . '" />"';
		}
		$xml = $xml. '</gateways>';
		return $xml;
	}
}
?>