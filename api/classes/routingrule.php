<?php
/**
 * 
 *
 * @author Deblina Das
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Info
 * @subpackage RoutingRule
 * @version 2.01
 */

/**
 * Data class for hold all data relevant for an Rule Condition
 */
class RoutingRule {
	/**
	 * Unique ID for the Transaction
	 *
	 * @var integer
	 */
	private $_iID;
	
	/**
	 * Client ID for the Transaction
	 *
	 * @var integer
	 */
	private $_iClientId;
	
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
	private $_sRelation;
	/**
	 * The value of the condition
	 *
	 * @var string
	 */
	private $_iPriority;
	/**
	 * Configuration for the GatewayInfo send as part of the transaction.
	 *
	 * @var GatewayInfo
	 */
	private $_obj_GatewayConfigs = null;
	/**
	 * Configuration for the ConditionInfo send as part of the transaction.
	 *
	 * @var ConditionInfo
	 */
	private $_obj_ConditionConfigs = null;
	
	
	
	public function __construct($id,$clientId ,$name, $priority, $operator, $oConditions, $oGateways) {
		$this->_iID = ( integer ) $id;
		$this->_iClientId = ( integer ) $clientId;
		$this->_sName = trim ( $name );
		$this->_iPriority = ( integer ) $priority;
		$this->_sRelation = trim ( $operator );
		$this->_obj_ConditionConfigs = $oConditions;
		$this->_obj_GatewayConfigs = $oGateways;
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
	public static function produceConfig(RDB &$oDB, $clientid) {
		// Availability of Gateway to be retrieve later from Gateway performance monitoring system
		$sql = "SELECT RT.ID,RT.name,priority,OT.symbol as operator from client.Rule_tbl RT 
				JOIN system.operator_tbl OT ON RT.operatorid = OT.id  where clientid = " . intval ( $clientid ) . " and RT.enabled = 't'";
		
		$res = $oDB->query ( $sql );
		$aMessages = array ();
		while ( $RS = $oDB->fetchName ( $res ) ) {
			
			$_obj_ConditionConfigs = ConditionInfo::produceConfig ( $oDB, $RS ["ID"] );
			$_obj_GatewayConfigs = GatewayInfo::produceConfig ( $oDB, $RS ["ID"]);
			$aMessages [] = new RoutingRule ( $RS ["ID"],$clientid, $RS ["NAME"], $RS ["PRIORITY"], $RS ["OPERATOR"], $_obj_ConditionConfigs, $_obj_GatewayConfigs );
		}
		return $aMessages;
	}
	public static function toXML($oRoutingRules) {
		
		$xml = '<rules>';
		foreach ( $oRoutingRules as $oRule ) {
			$xml = $xml . '<rule  id="' . $oRule->_iID . '" client-id="' . $oRule->_iClientId .  '" name="' . $oRule->_sName . '" priority ="' . $oRule->_iPriority .  '" reltn ="' .$oRule->_sRelation .'">';
			$xml = $xml .ConditionInfo::toXML($oRule->_obj_ConditionConfigs);
			$xml = $xml .GatewayInfo::toXML($oRule->_obj_GatewayConfigs);
			$xml = $xml.'</rule>' ;
		}
		$xml = $xml . '</rules>';
		return $xml;
	}
}
?>