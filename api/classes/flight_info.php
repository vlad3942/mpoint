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
	 * Unique Value of Operating Airline
	 *
	 * @var integer
	 */
	private $_OpAirlineCode;
    /**
     * Unique Value of Marketing Airline
     *
     * @var integer
     */
    private $_MktAirlineCode;
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
	 * Marketing Flight number of this flight
	 *
	 * @var string
	 */
	private $_MktFlightNumber;

    /**
     * Operating Flight number of this flight
     *
     * @var string
     */
    private $_OpFlightNumber;

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
     * Indicates the departure country of the flight
     *
     * @var string
     */
    private $_iDepartureCountryId;

    /**
     * Indicates the arrival country of the flight
     *
     * @var string
     */
    private $_iArrivalCountryId;

    /**
     * Indicates the departure date time zone
     *
     * @var string
     */
    private $_DeptTimeZone;
    /**
     * Indicates the arrival date time zone
     *
     * @var string
     */
    private $_ArrivalTimeZone;

    /**
     * Indicates the arrival terminal
     *
     * @var string
     */
    private $_ArrivalTerminal;

    /**
     * Indicates the departure terminal
     *
     * @var string
     */
    private $_DeptTerminal;




    /**
	 * Default Constructor
	 */
	public function __construct($id, $scid, $fnum, $daid, $aaid, $alid, $adid, $ddid, $tag, $tripCount, $serviceLevel, $departureCountryId, $arrivalCountryId, $Adata, $timeZone, $opFlightNumber, $aTimeZone, $mAirlineCode, $deptCity, $arrivalCity, $airCraftType, $aTerminal, $dTerminal ) {
		$this->_iID = ( integer ) $id;
		$this->_ServiceClass = $scid;
		$this->_DepartureAirport = $daid;
		$this->_ArrivalAirport = $aaid;
		$this->_OpAirlineCode = $alid;
		$this->_ArrivalDate = $adid;
		$this->_DepartureDate = $ddid;
		$this->_aAdditionalData = $Adata;
		$this->_MktFlightNumber = $fnum;
		$this->_aTag = $tag;
		$this->_aTripCount = $tripCount;
		$this->_aServiceLevel = $serviceLevel;
        $this->_iDepartureCountryId = $departureCountryId;
        $this->_iArrivalCountryId = $arrivalCountryId;
        $this->_DeptTimeZone = $timeZone;
        $this->_OpFlightNumber = $opFlightNumber;
        $this->_ArrivalTimeZone = $aTimeZone;
        $this->_MktAirlineCode = $mAirlineCode;
        $this->_DepartureCity = $deptCity;
        $this->_ArrivalCity = $arrivalCity;
        $this->_AirCraftType = $airCraftType;
        $this->_ArrivalTerminal = $aTerminal;
        $this->_DeptTerminal = $dTerminal;
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
	 * Returns the Operating Code of that Airline from which Passenger Transacts
	 *
	 * @return string
	 */
	public function getOperatingAirline() {
		return $this->_OpAirlineCode;
	}

    /**
     * Returns the Marketing Code of that Airline from which Passenger Transacts
     *
     * @return string
     */
    public function getMarketingAirline() {
        return $this->_MktAirlineCode;
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
	 * Returns the marketing flight number of this flight
	 *
	 * @return array
	 */
	public function getMktFlightNumber() {
		return $this->_MktFlightNumber;
	}

    /**
     * Returns the operating flight number of this flight
     *
     * @return array
     */
    public function getOpFlightNumber() {
        return $this->_OpFlightNumber;
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

    /**
     * Returns the departure country of the flight
     * @return integer
     */
    public function getDepartureCountry()
    {
        return $this->_iDepartureCountryId;
    }

    /**
     * Returns the arrival country of the flight
     * @return integer
     */
    public function getArrivalCountry()
    {
        return $this->_iArrivalCountryId;
    }

    /**
     * Returns the departure date time zone
     * @return string
     */
    public function getDeparturetTimeZone()
    {
        return $this->_DeptTimeZone;
    }

    /**
     * Returns the arrival date time zone
     * @return string
     */
    public function getArrivalTimeZone()
    {
        return $this->_ArrivalTimeZone;
    }

    /**
     * Returns departure city of this flight
     * @return string
     */
    public function getDepartureCity()
    {
        return $this->_DepartureCity;
    }

    /**
     * Returns arrival city of this flight
     * @return string
     */
    public function getArrivalCity()
    {
        return $this->_ArrivalCity;
    }

    /**
     * Returns aircraft type of this flight
     * @return string
     */
    public function getAircraftType()
    {
        return $this->_AirCraftType;
    }

    /**
     * Returns arrival terminal of this flight
     * @return string
     */
    public function getArrivalTerminal()
    {
        return $this->_ArrivalTerminal;
    }

    /**
     * Returns departure terminal of this flight
     * @return string
     */
    public function getDepartureTerminal()
    {
        return $this->_DeptTerminal;
    }

    /**
     * Returns the id of service level
     * @return string
     */
    private function _getServiceLevelName($serviceLevelId)
    {
        return Constants::aServiceLevelAndIdMapp[$serviceLevelId];
    }

    /**
     * Returns date time with timezone
     * @return string
     */
    private function _getDateTimeWithZone($dateTime)
    {
        $dt = new DateTime($dateTime);
        return $dt->format('Y-m-d\TH:i:s\Z');
    }

	public static function produceConfig(RDB $oDB, $id) : ?FlightInfo {
		$sql = "SELECT id, service_class, mkt_flight_number, departure_airport, arrival_airport, op_airline_code, order_id, arrival_date, departure_date, created, modified, tag, trip_count, service_level, departure_countryid, arrival_countryid, departure_timezone, op_flight_number, arrival_timezone, mkt_airline_code,
                departure_city, arrival_city, aircraft_type, arrival_terminal, departure_terminal
					FROM log" . sSCHEMA_POSTFIX . ".flight_tbl WHERE id=" . $id;
		// echo $sql ."\n";
		$RS = $oDB->getName ( $sql );
		if (is_array ( $RS ) === true && count ( $RS ) > 0) {
			$sqlA = "SELECT name, value FROM log" . sSCHEMA_POSTFIX . ".additional_data_tbl WHERE type='Flight' and created >= '" . $RS["CREATED"]  . "'::timestamp  - interval '60 seconds' and externalid=" . $RS ["ID"];
			// echo $sqlA;
			$RSA = $oDB->getAllNames ( $sqlA );
			if (is_array ( $RSA ) === true && count ( $RSA ) > 0) {
				return new FlightInfo ( $RS ["ID"], $RS ["SERVICE_CLASS"], $RS ["MKT_FLIGHT_NUMBER"], $RS ["DEPARTURE_AIRPORT"], $RS ["ARRIVAL_AIRPORT"], $RS ["OP_AIRLINE_CODE"], $RS ["ARRIVAL_DATE"], $RS ["DEPARTURE_DATE"], $RS ["TAG"],$RS ["TRIP_COUNT"],$RS ["SERVICE_LEVEL"], $RS ["DEPARTURE_COUNTRYID"], $RS ["ARRIVAL_COUNTRYID"], $RSA, $RS ["DEPARTURE_TIMEZONE"], $RS["OP_FLIGHT_NUMBER"], $RS["ARRIVAL_TIMEZONE"], $RS["MKT_AIRLINE_CODE"], $RS["DEPARTURE_CITY"], $RS["ARRIVAL_CITY"], $RS["AIRCRAFT_TYPE"], $RS["ARRIVAL_TERMINAL"], $RS["DEPARTURE_TERMINAL"] );
			} else {
				return new FlightInfo ( $RS ["ID"], $RS ["SERVICE_CLASS"], $RS ["MKT_FLIGHT_NUMBER"], $RS ["DEPARTURE_AIRPORT"], $RS ["ARRIVAL_AIRPORT"], $RS ["OP_AIRLINE_CODE"], $RS ["ARRIVAL_DATE"], $RS ["DEPARTURE_DATE"],$RS ["TAG"],$RS ["TRIP_COUNT"],$RS ["SERVICE_LEVEL"], $RS ["DEPARTURE_COUNTRYID"], $RS ["ARRIVAL_COUNTRYID"],null, $RS ["DEPARTURE_TIMEZONE"], $RS["OP_FLIGHT_NUMBER"], $RS["ARRIVAL_TIMEZONE"], $RS["MKT_AIRLINE_CODE"], $RS["DEPARTURE_CITY"], $RS["ARRIVAL_CITY"], $RS["AIRCRAFT_TYPE"], $RS["ARRIVAL_TERMINAL"], $RS["DEPARTURE_TERMINAL"] );
			}
		} else {
		    trigger_error('Unable to create Flight Info object', E_USER_NOTICE);
			return null;
		}
	}
	public static function produceConfigurations(RDB $oDB, $fid) {
		$sql = "SELECT id
				FROM Log" . sSCHEMA_POSTFIX . ".flight_tbl
				WHERE order_id = " . intval ( $fid );
		// echo $sql ."\n";
		$aConfigurations = array ();
		$res = $oDB->query ( $sql );
		while ( $RS = $oDB->fetchName ( $res ) ) {
			$aConfigurations [] = self::produceConfig ( $oDB, $RS ["ID"] );
		}
		return $aConfigurations;
	}
	public function getAdditionalDataArr($aDataArr) {
		$Axml = '<param name="' . $aDataArr ["NAME"] . '">' . htmlspecialchars($aDataArr ["VALUE"]) . '</param>';
		return $Axml;
	}
    public function getAdditionalDataAttributeLess($aDataArr)
    {
        $Axml = '<param>';
        $Axml .=  '<name>'. $aDataArr ["NAME"] . '</name>';
        $Axml .=  '<value>'. htmlspecialchars($aDataArr ["VALUE"]) . '</value>';
        $Axml .= '</param>';
        return $Axml;
    }
    public function toXML() : string
    {
        $xml = '';

        $xml .= '<trip tag="'. $this->getATag() .'" seq="' . $this->getATripCount() . '">';
        $xml .= '<origin external-id="'. $this->getDepartureAirport() .'" country-id="' . $this->getDepartureCountry() . '" time-zone="' . $this->getDeparturetTimeZone() . '" terminal="' . $this->getDepartureTerminal() . '">'. $this->getDepartureCity() .'</origin>';
        $xml .= '<destination external-id="'. $this->getArrivalAirport() .'" country-id="' . $this->getArrivalCountry() . '" time-zone="' . $this->getArrivalTimeZone() . '" terminal="' . $this->getArrivalTerminal() . '">'. $this->getArrivalCity() .'</destination>';
        $xml .= '<departure-time>' . $this->_getDateTimeWithZone($this->getDepartureDate ()) . '</departure-time>';
        $xml .= '<arrival-time>' . $this->_getDateTimeWithZone($this->getArrivalDate()) . '</arrival-time>';
        $xml .= '<departure-time-without-timezone>' . $this->getDepartureDate() . '</departure-time-without-timezone>';
        $xml .= '<arrival-time-without-timezone>' . $this->getArrivalDate() . '</arrival-time-without-timezone>';
        $xml .= '<booking-class>' . $this->getServiceClass () . '</booking-class>';
        $xml .= '<service-level id="' .$this->getAServiceLevel(). '">' . $this->_getServiceLevelName($this->getAServiceLevel()) . '</service-level>';
        $xml .= '<transportation code="'. $this->getMarketingAirline() .'" number="' . $this->getOpFlightNumber() . '">';
        $xml .= '<carriers>';
        $xml .= '<carrier code="'. $this->getOperatingAirline() .'" type-id="'. $this->getAircraftType() .'">';
        $xml .= '<number>'. $this->getMktFlightNumber() .'</number>';
        $xml .= '</carrier>';
        $xml .= '</carriers>';
        $xml .= '</transportation>';
        if ($this->getAdditionalData ()) {
            $xml .= '<additional-data>';
            foreach ($this->getAdditionalData () as $fAdditionalData) {
                $xml .= $this->getAdditionalDataArr ($fAdditionalData);
            }
            $xml .= '</additional-data>';
        } else {
        }
        $xml .= '</trip>';

        return $xml;
    }

    private function _toOldXML()
    {
        $xml = '';
        $xml .= '<flight-detail tag="'. $this->getATag() .'" trip-count="' . $this->getATripCount() . '" service-level="'. $this->getAServiceLevel() .'">';
        $xml .= '<service-class>' . $this->getServiceClass () . '</service-class>';
        $xml .= '<flight-number>' . $this->getMktFlightNumber() . '</flight-number>';
        $xml .= '<departure-airport>' . $this->getDepartureAirport () . '</departure-airport>';
        $xml .= '<arrival-airport>' . $this->getArrivalAirport () . '</arrival-airport>';
        $xml .= '<airline-code>' . $this->getOperatingAirline() . '</airline-code>';
        $xml .= '<departure-date>' . $this->getDepartureDate () . '</departure-date>';
        $xml .= '<arrival-date>' . $this->getArrivalDate () . '</arrival-date>';
        $xml .= '<departure-country>' . $this->getDepartureCountry () . '</departure-country>';
        $xml .= '<arrival-country>' . $this->getArrivalCountry () . '</arrival-country>';
        $xml .= '<time-zone>' . $this->getDeparturetTimeZone() . '</time-zone>';
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

    public function toAttributeLessXML()
    {
        $xml = '';
        $xml .= '<flightDetail>';
        $xml .= '<tag>'.$this->getATag().'</tag>';
        $xml .= '<tripCount>'.$this->getATripCount().'</tripCount>';
        $xml .= '<serviceLevel>'.$this->getAServiceLevel().'</serviceLevel>';

        $xml .= '<serviceClass>' . $this->getServiceClass () . '</serviceClass>';
        $xml .= '<flightNumber>' . $this->getMktFlightNumber() . '</flightNumber>';
        $xml .= '<departureAirport>' . $this->getDepartureAirport () . '</departureAirport>';
        $xml .= '<arrivalAirport>' . $this->getArrivalAirport () . '</arrivalAirport>';
        $xml .= '<airlineCode>' . $this->getOperatingAirline() . '</airlineCode>';
        $xml .= '<departureDate>' . $this->getDepartureDate () . '</departureDate>';
        $xml .= '<arrivalDate>' . $this->getArrivalDate () . '</arrivalDate>';
        if ($this->getAdditionalData ())
        {
            $xml .= '<additionalData>';
            foreach ($this->getAdditionalData () as $fAdditionalData)
            {
                $xml .= $this->getAdditionalDataAttributeLess ( $fAdditionalData);
            }
            $xml .= '</additionalData>';
        }
        else { }
        $xml .= '</flightDetail>';
        return $xml;
    }
}
?>