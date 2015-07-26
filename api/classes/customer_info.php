<?php
/**
 * 
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package mConsole
 * @subpackage Search
 * @version 1.00
 */

/**
 * Data class for hold all data relevant for a Customer
 *
 */
class CustomerInfo
{
	/**
	 * Unique ID for the Customer
	 *
	 * @var integer
	 */
	private $_iID;
	/**
	 * GoMobile's ID for the Customer's Country
	 *
	 * @var integer
	 */
	private $_iCountryID;
	/**
	 * Customer's Mobile Number (MSISDN)
	 *
	 * @var long
	 */
	private $_lMobile;
	/**
	 * Customer's E-Mail Address
	 *
	 * @var string
	 */
	private $_sEMail;
	/**
	 * The Client's Reference for the Customer
	 *
	 * @var string
	 */
	private $_sCustomerRef;
	/**
	 * The customer's full name
	 *
	 * @var string
	 */
	private $_sFullName;
	/**
	 * The language that all payment pages should be rendered in by default for the Client
	 *
	 * @var string
	 */
	private $_sLanguage;

	/**
	 * Default constructor
	 * 
	 * @param integer $id		Unique ID for the Customer
	 * @param integer $cid		GoMobile's ID for the Customer's Country
	 * @param long $mob			Customer's Mobile Number (MSISDN)
	 * @param string $email		Customer's E-Mail Address
	 * @param string $cr		The Client's Reference for the Customer
	 * @param string $name		The customer's full name
	 * @param string $lang		The language that all payment pages should be rendered in by default for the Client
	 */
	public function __construct($id, $cid, $mob, $email, $cr, $name, $lang)
	{
		$this->_iID =  (integer) $id;
		$this->_iCountryID = (integer) $cid;
		$this->_lMobile = (float) $mob;
		$this->_sEMail = trim($email);
		$this->_sCustomerRef = trim($cr);
		$this->_sFullName = trim($name);
		$this->_sLanguage = trim($lang);
	}

	public function getID() { return $this->_iID; }
	public function getCountryID() { return $this->_iCountryID; }
	public function getMobile() { return $this->_lMobile; }
	public function getEMail() { return $this->_sEMail; }
	public function getCustomerRef() { return $this->_sCustomerRef; }
	public function getFullName() { return $this->_sFullName; }
	public function getLanguage() { return $this->_sLanguage; }

	public function toXML()
	{
		$xml  = '<customer';
		if ($this->_iID > 0) { $xml .= ' id="'. $this->_iID .'"'; }
		if (strlen($this->_sCustomerRef) > 0) { $xml .= ' customer-ref="'. htmlspecialchars($this->_sCustomerRef, ENT_NOQUOTES) .'"'; }
		$xml  .= '>';
		if (strlen($this->_sFullName) > 0) { $xml .= '<full-name>'. htmlspecialchars($this->_sFullName, ENT_NOQUOTES) .'</full-name>'; }
		if ($this->_lMobile > 0) { $xml .= '<mobile country-id="'. $this->_iCountryID .'">'. $this->_lMobile .'</mobile>'; }
		if (strlen($this->_sEMail) > 0) { $xml .= '<email>'. htmlspecialchars($this->_sEMail, ENT_NOQUOTES) .'</email>'; } 
		$xml  .= '</customer>';

		return $xml;
	}
}
?>