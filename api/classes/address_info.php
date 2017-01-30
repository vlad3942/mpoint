<?php
/**
 * The Info package contains various data classes holding information such as:
 * 	- Address specific details for a Order as received by the cart that is send when a transation is initialized.
 *
 * @author Manish S Dewani
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Info
 * @subpackage AddressInfo
 * @version 1.10
 */

/* ==================== Address Information Exception Classes Start ==================== */
/**
 * Exception class for all Address Information exceptions
 */
class AddressInfoException extends mPointException {
}
/* ==================== Flight Information Exception Classes End ==================== */

/**
 * Data class for hold all data relevant of Address for a Transaction
 */
class AddressInfo {
	/**
	 * Unique ID for the Address
	 *
	 * @var integer
	 */
	private $_iID;
	/**
	 * Value for the Name of Address
	 */
	private $_Name;
	/**
	 * Value of the Street of that Address
	 */
	private $_Street;
	/**
	 * Value of the Street of that Address
	 */
	private $_Street2;
	/**
	 * Value of the City of that Address
	 */
	private $_CityCode;
	/**
	 * Value of the State of that Address
	 */
	private $_StateCode;
	/**
	 * Value of the Zip of that Address
	 */
	private $_ZipCode;
	/**
	 * Value of the Country of that Address
	 */
	private $_CountryCode;
	/**
	 * External Reference Data related to Address
	 */
	private $_aExternalValue;
	
	/**
	 * Default Constructor
	 */
	public function __construct($id, $nm, $street, $streets, $city, $state, $zip, $country, $extra) {
		$this->_iID = ( integer ) $id;
		$this->_Name = $nm;
		$this->_Street = $street;
		$this->_Street2 = $streets;
		$this->_CityCode = $city;
		$this->_StateCode = $state;
		$this->_ZipCode = $zip;
		$this->_CountryCode = $country;
		$this->_aExternalValue = $extra;
	}
	
	/**
	 * Returns the Unique ID for the Flight
	 *
	 * @return integer
	 */
	public function getID() {
		return $this->_iID;
	}
	/**
	 * Returns the Name of a Address For that Order
	 *
	 * @return string
	 */
	public function getName() {
		return $this->_Name;
	}
	/**
	 * Returns the Street of a Address For that Order
	 *
	 * @return string
	 */
	public function getStreet() {
		return $this->_Street;
	}
	/**
	 * Returns the Second Line Of Street of a Address For that Order
	 *
	 * @return string
	 */
	public function getStreet2() {
		return $this->_Street2;
	}
	/**
	 * Returns the City of a Address For that Order
	 *
	 * @return string
	 */
	public function getCity() {
		return $this->_CityCode;
	}
	/**
	 * Returns the State of a Address For that Order
	 *
	 * @return string
	 */
	public function getState() {
		return $this->_StateCode;
	}
	/**
	 * Returns the Zip of a Address For that Order
	 *
	 * @return string
	 */
	public function getZip() {
		return $this->_ZipCode;
	}
	/**
	 * Returns the Country of a Address For that Order
	 *
	 * @return string
	 */
	public function getCountry() {
		return $this->_CountryCode;
	}
	/**
	 * Returns the External Reference Value for which the Address is Inserted.
	 *
	 * @return integer
	 */
	public function getExternal() {
		return $this->_aExternalValue;
	}
	public static function produceConfig(RDB $oDB, $id) {
		$sql = "SELECT id, name, street, street2, city, state, country, zip, external_ref
					FROM log" . sSCHEMA_POSTFIX . ".address_tbl WHERE id=" . $id;
		// echo $sql ."\n";
		$RS = $oDB->getName ( $sql );
		if (is_array ( $RS ) === true && count ( $RS ) > 0) {
			$RSA = $oDB->getName ( $sqlA );
			return new AddressInfo ( $RS ["ID"], $RS ["NAME"], $RS ["STREET"], $RS ["STREET2"], $RS ["CITY"], $RS ["STATE"], $RS ["COUNTRY"], $RS ["ZIP"], $RS ["EXTERNAL_REF"] );
		} else {
			return null;
		}
	}
	public static function produceConfigurations(RDB $oDB, $fid) {
		$sql = "SELECT id
				FROM Log" . sSCHEMA_POSTFIX . ".address_tbl
				WHERE external_ref = " . intval ( $fid ) . "";
		// echo $sql ."\n";
		$aConfigurations = array ();
		$res = $oDB->query ( $sql );
		while ( $RS = $oDB->fetchName ( $res ) ) {
			$aConfigurations [] = self::produceConfig ( $oDB, $RS ["ID"] );
		}
		
		return $aConfigurations;
	}
	public function toXML() {
		$xml = '';
		$xml .= '<shipping-address>';
		$xml .= '<name>' . $this->getName () . '</name>';
		$xml .= '<street>' . $this->getStreet () . '</street>';
		$xml .= '<street2>' . $this->getStreet2 () . '</street2>';
		$xml .= '<city>' . $this->getCity () . '</city>';
		$xml .= '<state>' . $this->getState () . '</state>';
		$xml .= '<zip>' . $this->getZip () . '</zip>';
		$xml .= '<country>' . $this->getCountry () . '</country>';
		$xml .= '<shipping-address>';
		return $xml;
	}
}
?>