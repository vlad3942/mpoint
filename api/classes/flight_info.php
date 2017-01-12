<?php
/**
 * The Info package contains various data classes holding information such as:
* 	- Flight specific details as received by the cart that is send when a transation is initialized.
*
* @author Manish S Dewani
* @copyright Cellpoint Mobile
* @link http://www.cellpointmobile.com
* @package Info
* @subpackage FlightInfo
* @version 1.10
*/

/* ==================== Flight Information Exception Classes Start ==================== */
/**
 * Exception class for all Flight Information exceptions
*/
class FlightInfoException extends mPointException { }
/* ==================== Flight Information Exception Classes End ==================== */

/**
 * Data class for hold all data relevant of flight for a Transaction
 *
 */
class FlightInfo
{
	/**
	 * Unique ID for the Flight
	 *
	 * @var integer
	 */
	private $_iID;
	/**
	 * Value for the Service Class
	 *
	 */
	private $_ServiceClass;
	/**
	 * Value of the Departure Airport 
	 *
	 */
	private $_DepartureAirport;
	/**
	 * Value of the Arrival Airport 
	 *
	 */
	private $_ArrivalAirport;
	/**
	 * Unique Value of Airline
	 *
	 * @var integer
	 */
	private $_AirlineCode;
	/**
	 * Arrival Date of Passenger
	 *
	 * @var timestamp
	 */
	private $_ArrivalDate;
	/**
	 * Departure Date of Passenger
	 *
	 * @var timestamp
	 */
	private $_DepartureDate;
	/**
	 * Additional Data related to flight
	 *
	 * @var string
	 */
	private  $_aAdditionalName;
	/**
	 * Additional Data related to flight
	 *
	 * @var string
	 */
	private  $_aAdditionalValue;


	/**
	 * Default Constructor
	 *

	 *
	 */
	public function __construct($id, $scid, $daid, $aaid, $alid, $adid, $ddid, $ain, $aiv)
	{
		$this->_iID =  (integer) $id;
		$this->_ServiceClass = $scid;
		$this->_DepartureAirport = $daid;
		$this->_ArrivalAirport = $aaid;
		$this->_AirlineCode = $alid;
		$this->_ArrivalDate = $adid;
		$this->_DepartureDate = $ddid;
		$this->_aAdditionalName = $ain;
		$this->_aAdditionalValue = $aiv;
	}

	/**
	 * Returns the Unique ID for the Flight
	 *
	 * @return 	integer
	 */
	public function getID() { return $this->_iID; }
	/**
	 * Returns the Service Class of a Passenger For that Transaction
	 *
	 * @return 	string
	 */
	public function getServiceClass() { return $this->_ServiceClass; }
	/**
	 * Returns the Departure Airport of that Passenger
	 *
	 * @return 	string
	 */
	public function getDepartureAirport() { return $this->_DepartureAirport; }
	/**
	 * Returns the Arrival Airport of that Passenger
	 *
	 * @return 	string
	 */
	public function getArrivalAirport() { return $this->_ArrivalAirport; }
	/**
	 * Returns the Code of that Airline from which Passenger Transacts
	 *
	 * @return 	string
	 */
	public function getAirline() { return $this->_AirlineCode; }
	/**
	 * Returns the date of Arrival of that Passenger
	 *
	 * @return 	timestamp
	 */
	public function getArrivalDate() { return $this->_ArrivalDate; }
	/**
	 * Returns the date of Departure of that Passenger
	 *
	 * @return 	timestamp
	 */
	public function getDepartureDate() { return $this->_DepartureDate; }
	/**
	 * Returns the Additional Data of this flight
	 *
	 * @return 	array
	 */
	
	public function getAdditionalName() { return $this->_aAdditionalName; }
	/**
	 * Returns the Additional Data of this flight
	 *
	 * @return 	array
	 */
	public function getAdditionalValue() { return $this->_aAdditionalValue; }

	
	
	public static function produceConfig(RDB $oDB, $id)
	{
		$sql = "SELECT id, service_class, departure_airport, arrival_airport, airline_code, order_id, arrival_date, departure_date, created, modified, additional_data_ref
					FROM log".sSCHEMA_POSTFIX.".flight_tbl where id=".$id;
			//echo $sql ."\n";
		$RS = $oDB->getName($sql);
		if (is_array($RS) === true && count($RS) > 0)
		{
			$sqlA = "SELECT name, value FROM log".sSCHEMA_POSTFIX.".additional_data_tbl where id=".$RS["ADDITIONAL_DATA_REF"];
			//echo $sqlA;
			$RSA = $oDB->getName($sqlA);
		
			     if(is_array($RSA) === true && count($RSA) > 0)
			     {
			     	return new FlightInfo($RS["ID"], $RS["SERVICE_CLASS"], $RS["DEPARTURE_AIRPORT"], $RS["ARRIVAL_AIRPORT"], $RS["AIRLINE_CODE"],
			     		 $RS["ARRIVAL_DATE"], $RS["DEPARTURE_DATE"],  $RSA["NAME"], $RSA["VALUE"]);
			     }
			     else 
			     {
			     	return new FlightInfo($RS["ID"], $RS["SERVICE_CLASS"], $RS["DEPARTURE_AIRPORT"], $RS["ARRIVAL_AIRPORT"], $RS["AIRLINE_CODE"],
			     			 $RS["ARRIVAL_DATE"], $RS["DEPARTURE_DATE"]);
			     }
			    
			     
			
		}
		else { return null; }
	}

	public static function produceConfigurations(RDB $oDB, $fid)
	{
		$sql = "SELECT id
				FROM Log". sSCHEMA_POSTFIX .".flight_tbl
				WHERE order_id = ". intval($fid) ."";
		//echo $sql ."\n";
		$aConfigurations = array();
		$res = $oDB->query($sql);
		while ($RS = $oDB->fetchName($res) )
		{
			$aConfigurations[] = self::produceConfig($oDB, $RS["ID"]);
		}
		
		return $aConfigurations;
	}

	public function toXML()
	{
		$xml = '';
		$xml .= '<flight-detail>';
		$xml .= '<service-class>'. $this->getServiceClass() .'</service-class>';
		$xml .= '<departure-airport>'. $this->getDepartureAirport() .'</departure-airport>';
		$xml .= '<arrival-airport>'. $this->getArrivalAirport() .'</arrival-airport>';
		$xml .= '<airline-code>'. $this->getAirline() .'</airline-code>';
		$xml .= '<departure-date>'. $this->getDepartureDate() .'</departure-date>';
		$xml .= '<arrival-date>'. $this->getArrivalDate() .'</arrival-date>';
		if($this->getAdditionalName())
		{
			$xml .= '<additional-data>';
			$xml .= '<data name="'.$this->getAdditionalName().'">'. $this->getAdditionalValue() .'</data>';
			$xml .= '</additional-data>';
		}
		else
		{
		}
		$xml .= '</flight-detail>';
		return $xml;
	}
}
?>