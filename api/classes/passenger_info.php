<?php
/**
 * The Info package contains various data classes holding information such as:
 * 	- Passenger specific details as received by the cart that is send when a transation is initialized.
 *
 * @author Manish S Dewani
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Info
 * @subpackage PassengerInfo
 * @version 1.10
 */

/* ==================== Passenger Information Exception Classes Start ==================== */
/**
 * Exception class for all Passenger Information exceptions
 */
class PassengerInfoException extends mPointException {
}
/* ==================== Passenger Information Exception Classes End ==================== */

/**
 * Data class for hold all data relevant of Passenger for a Transaction
 */
class PassengerInfo {
	/**
	 * Unique ID for the Passenger
	 *
	 * @var integer
	 */
	private $_iID;
	/**
	 * Value for First Name
	 */
	private $_First_Name;
	/**
	 * Value for Last Name
	 */
	private $_Last_Name;
	/**
	 * Value of Type
	 */
	private $_Type;
	/**
	 * Data for Additional info related to Passenger
	 *
	 * @var integer
	 */
	private $_AdditionalData;
	
	/**
	 * Default Constructor
	 */
	public function __construct($id, $fnm, $lnm, $type, $Adata) {
		$this->_iID = ( integer ) $id;
		$this->_First_Name = $fnm;
		$this->_Last_Name = $lnm;
		$this->_Type = $type;
		$this->_AdditionalData = $Adata;
	}
	
	/**
	 * Returns the Unique ID for the Passenger
	 *
	 * @return integer
	 */
	public function getID() {
		return $this->_iID;
	}
	/**
	 * Returns the First Name of a Passenger For that Transaction
	 *
	 * @return string
	 */
	public function getFirstName() {
		return $this->_First_Name;
	}
	/**
	 * Returns the Last Name of a Passenger For that Transaction
	 *
	 * @return string
	 */
	public function getLastName() {
		return $this->_Last_Name;
	}
	/**
	 * Returns the type of the Passenger
	 *
	 * @return string
	 */
	public function getType() {
		return $this->_Type;
	}
	/**
	 * Returns the Additional Data of the passenger
	 *
	 * @return array
	 */
	public function getAdditionalData() {
		return $this->_AdditionalData;
	}
	
	
	public static function produceConfig(RDB $oDB, $id) {
		$sql = "SELECT id, first_name, last_name, type, order_id, created, modified
					FROM log" . sSCHEMA_POSTFIX . ".passenger_tbl WHERE id=" . $id;
		// echo $sql ."\n";
		$RS = $oDB->getName ( $sql );
		if (is_array ( $RS ) === true && count ( $RS ) > 0) {
			$sqlA = "SELECT name, value FROM log" . sSCHEMA_POSTFIX . ".additional_data_tbl WHERE externalid=" . $RS ["ID"];
			// echo $sqlA;
			$RSA = $oDB->getAllNames ( $sqlA );
			
			if (is_array ( $RSA ) === true && count ( $RSA ) > 0) {
				return new PassengerInfo ( $RS ["ID"], $RS ["FIRST_NAME"], $RS ["LAST_NAME"], $RS ["TYPE"], $RSA );
			} else {
				return new PassengerInfo ( $RS ["ID"], $RS ["FIRST_NAME"], $RS ["LAST_NAME"], $RS ["TYPE"] );
			}
		} else {
			return null;
		}
	}
	
	public static function produceConfigurations(RDB $oDB, $pid) {
		$sql = "SELECT id
				FROM Log" . sSCHEMA_POSTFIX . ".passenger_tbl
				WHERE order_id = " . intval ( $pid ) . "";
		// echo $sql ."\n";
		$aConfigurations = array ();
		$res = $oDB->query ( $sql );
		while ( $RS = $oDB->fetchName ( $res ) ) {
			$aConfigurations [] = self::produceConfig ( $oDB, $RS ["ID"] );
		}
		return $aConfigurations;
	}
	
	public function getAdditionalDataArr($aDataArr) {
		$Axml = '<param name="' . $aDataArr ["NAME"] . '">' . $aDataArr ["VALUE"] . '</param>';
		return $Axml;
	}
	
	public function toXML() {
		$xml = '';
		$xml .= '<passenger-detail>';
		$xml .= '<first-name>' . $this->getFirstName () . '</first-name>';
		$xml .= '<last-name>' . $this->getLastName () . '</last-name>';
		$xml .= '<type>' . $this->getType () . '</type>';
		if ($this->getAdditionalData ()) {
			$xml .= '<additional-data>';
			foreach ( $this->getAdditionalData () as $pAdditionalData ) {
				$xml .= $this->getAdditionalDataArr ( $pAdditionalData );
			}
			$xml .= '</additional-data>';
		} else {
		}
		$xml .= '</passenger-detail>';
		return $xml;
	}
}
?>