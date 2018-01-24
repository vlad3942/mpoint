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
 *
 */
class ConditionInfo
{
	/**
	 * Unique ID for the Transaction
	 *
	 * @var integer
	 */
	private $_iID;
	
	/**
	 * Field name of the condition e.g "Amount","Currency","Card Scheme" etc
	 *
	 * @var string
	 */
	private $_sField;
	/**
	 * Operator that define the relation between condition field and value e.g "<" , ">=" etc
	 *
	 * @var string
	 */
	private $_sOperator;
	/**
	 * The value of the condition
	 *
	 * @var string
	 */
	private $_sValue;
	
	
	public function __construct($id, $field, $operator, $value)
	{
		$this->_iID =  (integer) $id;
		$this->_sField = trim($field);
		$this->_sOperator = trim($operator);
		$this->_sValue = trim($value);
	}


	/**
	 * Produces a new instance of a ConditionInfo Object.
	 *
	 * @param 	RDB $oDB 		Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param 	integer $id 	Unique ID for the Rule Condition the request is performed in
	 * @return 	ConditionInfo
	 */
	public static function produceConfig(RDB &$oDB, $ruleid)
	{
		$sql = "SELECT RCT.id ,CT.name AS field ,RCT.conditionvalue AS value,OT.symbol  AS operator
				FROM Client".sSCHEMA_POSTFIX.".RuleCondition_tbl RCT JOIN System".sSCHEMA_POSTFIX.".Condition_tbl CT ON (RCT.conditionid = CT.id )
                JOIN Client.Rule_tbl RT ON (RT.id = RCT.ruleid)
				JOIN System".sSCHEMA_POSTFIX.".Operator_tbl OT ON (RCT.operatorid = OT.id) WHERE RT.id =  ". intval($ruleid) ;
		
		// echo $sql;
		
		$res = $oDB->query($sql);
		$aMessages = array();
		while ($RS = $oDB->fetchName($res) )
		{
			$aMessages[] = new ConditionInfo($RS["ID"], $RS["FIELD"], $RS["OPERATOR"] ,$RS["VALUE"]); 
		}
		
		return $aMessages;
	}
	
	public static function toXML($oConditionInfo) {
		$xml = '<conditions>';
		foreach ( $oConditionInfo as $aConditionObj ) {
			$xml = $xml . '<condition  id="' . $aConditionObj->_iID . '" field ="' . $aConditionObj->_sField . '" operator ="' . $aConditionObj->_sOperator . '">' . $aConditionObj->_sValue . '</condition>';
		}
		$xml = $xml . '</conditions>';
		return $xml;
	}
	
}
?>