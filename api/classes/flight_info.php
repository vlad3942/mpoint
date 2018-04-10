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
class FlightInfoException extends mPointException {
}
/* ==================== Flight Information Exception Classes End ==================== */

/**
 * Data class for hold all data relevant of flight for a Transaction
 */
class FlightInfo {
	/**
	 * Unique ID for the Flight
	 *
	 * @var integer
	 */
	private $_iID;
	/**
	 * Value for the Service Class
	 */
	private $_ServiceClass;
	/**
	 * Value of the Departure Airport
	 */
	private $_DepartureAirport;
	/**
	 * Value of the Arrival Airport
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
	 * @var array
	 */
	private $_aAdditionalData;
	/**
	 * Flight number of this flight
	 *
	 * @var string
	 */
	private $_aFlightNumber;

    /**
     * Captures the itinerary sequence of this flight
     *
     * @var string
     */
    private $_aTag;

    /**
     * Captures the flight segment sequence
     *
     * @var string
     */
    private $_aTripCount;

    /**
     * Indicates the service level of this flight
     *
     * @var string
     */
    private $_aServiceLevel;


    /**
	 * Default Constructor
	 */
	public function __construct($id, $scid, $fnum, $daid, $aaid, $alid, $adid, $ddid, $tag, $tripCount, $serviceLevel, $Adata) {
		$this->_iID = ( integer ) $id;
		$this->_ServiceClass = $scid;
		$this->_DepartureAirport = $daid;
		$this->_ArrivalAirport = $aaid;
		$this->_AirlineCode = $alid;
		$this->_ArrivalDate = $adid;
		$this->_DepartureDate = $ddid;
		$this->_aAdditionalData = $Adata;
		$this->_aFlightNumber = $fnum;
		$this->_aTag = $tag;
		$this->_aTripCount = $tripCount;
		$this->_aServiceLevel = $serviceLevel;
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
	 * Returns the Service Class of a Passenger For that Transaction
	 *
	 * @return string
	 */
	public function getServiceClass() {
		return $this->_ServiceClass;
	}
	/**
	 * Returns the Departure Airport of that Passenger
	 *
	 * @return string
	 */
	public function getDepartureAirport() {
		return $this->_DepartureAirport;
	}
	/**
	 * Returns the Arrival Airport of that Passenger
	 *
	 * @return string
	 */
	public function getArrivalAirport() {
		return $this->_ArrivalAirport;
	}
	/**
	 * Returns the Code of that Airline from which Passenger Transacts
	 *
	 * @return string
	 */
	public function getAirline() {
		return $this->_AirlineCode;
	}
	/**
	 * Returns the date of Arrival of that Passenger
	 *
	 * @return timestamp
	 */
	public function getArrivalDate() {
		return $this->_ArrivalDate;
	}
	/**
	 * Returns the date of Departure of that Passenger
	 *
	 * @return timestamp
	 */
	public function getDepartureDate() {
		return $this->_DepartureDate;
	}
	/**
	 * Returns the Additional Data of this flight
	 *
	 * @return array
	 */
	public function getAdditionalData() {
		return $this->_aAdditionalData;
	}
	/**
	 * Returns the flight number of this flight
	 *
	 * @return array
	 */
	public function getFlightNumber() {
		return $this->_aFlightNumber;
	}

    /**
     * Returns itinerary sequence of this flight
     * @return string
     */
    public function getATag()
    {
        return $this->_aTag;
    }

    /**
     * Returns the itinerary segment sequence
     * @return string
     */
    public function getATripCount()
    {
        return $this->_aTripCount;
    }

    /**
     * Returns the service level of this flight booking
     * @return string
     */
    public function getAServiceLevel()
    {
        return $this->_aServiceLevel;
    }

	public static function produceConfig(RDB $oDB, $id) {
		$sql = "SELECT id, service_class, flight_number, departure_airport, arrival_airport, airline_code, order_id, arrival_date, departure_date, created, modified, tag, trip_count, service_level
					FROM log" . sSCHEMA_POSTFIX . ".flight_tbl WHERE id=" . $id;
		// echo $sql ."\n";
		$RS = $oDB->getName ( $sql );
		if (is_array ( $RS ) === true && count ( $RS ) > 0) {
			$sqlA = "SELECT name, value FROM log" . sSCHEMA_POSTFIX . ".additional_data_tbl WHERE externalid=" . $RS ["ID"];
			// echo $sqlA;
			$RSA = $oDB->getAllNames ( $sqlA );
			if (is_array ( $RSA ) === true && count ( $RSA ) > 0) {
				return new FlightInfo ( $RS ["ID"], $RS ["SERVICE_CLASS"], $RS ["FLIGHT_NUMBER"], $RS ["DEPARTURE_AIRPORT"], $RS ["ARRIVAL_AIRPORT"], $RS ["AIRLINE_CODE"], $RS ["ARRIVAL_DATE"], $RS ["DEPARTURE_DATE"], $RS ["TAG"],$RS ["TRIP_COUNT"],$RS ["SERVICE_LEVEL"], $RSA );
			} else {
				return new FlightInfo ( $RS ["ID"], $RS ["SERVICE_CLASS"], $RS ["FLIGHT_NUMBER"], $RS ["DEPARTURE_AIRPORT"], $RS ["ARRIVAL_AIRPORT"], $RS ["AIRLINE_CODE"], $RS ["ARRIVAL_DATE"], $RS ["DEPARTURE_DATE"],$RS ["TAG"],$RS ["TRIP_COUNT"],$RS ["SERVICE_LEVEL"] );
			}
		} else {
			return null;
		}
	}
	public static function produceConfigurations(RDB $oDB, $fid) {
		$sql = "SELECT id
				FROM Log" . sSCHEMA_POSTFIX . ".flight_tbl
				WHERE order_id = " . intval ( $fid ) . "";
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
		$xml .= '<flight-detail tag="'. $this->getATag() .'" trip-count="' . $this->getATripCount() . '" service-level="'. $this->getAServiceLevel() .'">';
		$xml .= '<service-class>' . $this->getServiceClass () . '</service-class>';
		$xml .= '<flight-number>' . $this->getFlightNumber () . '</flight-number>';
		$xml .= '<departure-airport>' . $this->getDepartureAirport () . '</departure-airport>';
		$xml .= '<arrival-airport>' . $this->getArrivalAirport () . '</arrival-airport>';
		$xml .= '<airline-code>' . $this->getAirline () . '</airline-code>';
		$xml .= '<departure-date>' . $this->getDepartureDate () . '</departure-date>';
		$xml .= '<arrival-date>' . $this->getArrivalDate () . '</arrival-date>';
		if ($this->getAdditionalData ()) {
			$xml .= '<additional-data>';
			foreach ($this->getAdditionalData () as $fAdditionalData) {
				$xml .= $this->getAdditionalDataArr ($fAdditionalData);
			}
			$xml .= '</additional-data>';
		} else {
		}
		$xml .= '</flight-detail>';
		return $xml;
	}
}
?>