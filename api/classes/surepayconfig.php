<?php
class SurePayConfig
{
	private $_iMax = 5;
	private $_iDelay = 5;

	public function __construct($max, $delay)
	{
		$this->_iMax = $max;
		$this->_iDelay = $delay;
	}
	public function getMax() { return $this->_iMax; }
	public function getDelay() { return $this->_iDelay; }
	/**
	 * Produces a new instance of a SurePay Configuration Object.
	 *
	 * @param 	RDB $oDB 		Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param 	integer $id 	Unique ID for the Client whose SurePay Configuration should be retrieved
	 * @return 	SurePayConfig
	 */
	public static function produceConfig(RDB &$oDB, $id)
	{
		$sql = "SELECT max, delay
				FROM Client.SurePay_Tbl
				WHERE clientid = ". intval($id);
//		echo $sql ."\n";
		$RS = $oDB->getName($sql);

		return is_array($RS) === true ? new SurePayConfig($RS["MAX"], $RS["DELAY"]) : null;
	}
}
?>