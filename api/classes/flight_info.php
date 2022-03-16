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
	private $id;
	/**
	 * Value for the Service Class
	 */
	private $service_class;
	/**
	 * Value of the Departure Airport
	 */
	private $departure_airport;
	/**
	 * Value of the Arrival Airport
	 */
	private $arrival_airport;
	/**
	 * Unique Value of Operating Airline
	 *
	 * @var integer
	 */
	private $op_airline_code;
    /**
     * Unique Value of Marketing Airline
     *
     * @var integer
     */
    private $mkt_airline_code;
	/**
	 * Arrival Date of Passenger
	 *
	 * @var timestamp
	 */
	private $arrival_date;
	/**
	 * Departure Date of Passenger
	 *
	 * @var timestamp
	 */
	private $departure_date;
	/**
	 * Additional Data related to flight
	 *
	 * @var array
	 */
	private $additional_data;
	/**
	 * Marketing Flight number of this flight
	 *
	 * @var string
	 */
	private $mkt_flight_number;

    /**
     * Operating Flight number of this flight
     *
     * @var string
     */
    private $op_flight_number;

    /**
     * Captures the itinerary sequence of this flight
     *
     * @var string
     */
    private $tag;

    /**
     * Captures the flight segment sequence
     *
     * @var string
     */
    private $trip_count;

    /**
     * Indicates the service level of this flight
     *
     * @var string
     */
    private $service_level;

    /**
     * Indicates the departure country of the flight
     *
     * @var string
     */
    private $departure_country_id;

    /**
     * Indicates the arrival country of the flight
     *
     * @var string
     */
    private $arrival_country_id;

    /**
     * Indicates the departure date time zone
     *
     * @var string
     */
    private $dept_time_zone;
    /**
     * Indicates the arrival date time zone
     *
     * @var string
     */
    private $arrival_time_zone;

    /**
     * Indicates the arrival terminal
     *
     * @var string
     */
    private $arrival_terminal;

    /**
     * Indicates the departure terminal
     *
     * @var string
     */
    private $dept_terminal;

    /**
     * Indicates the departure city
     *
     * @var string
     */
    private $dept_city;

    /**
     * Indicates the arrival city
     *
     * @var string
     */
    private $arrival_city;

    /**
     * Indicates the aircraft type
     *
     * @var string
     */
    private $aircraft_type;




    /**
	 * Default Constructor
	 */
	public function __construct($id, $scid, $fnum, $daid, $aaid, $alid, $adid, $ddid, $tag, $tripCount, $serviceLevel, $departureCountryId, $arrivalCountryId, $Adata, $timeZone, $opFlightNumber, $aTimeZone, $mAirlineCode, $deptCity, $arrivalCity, $airCraftType, $aTerminal, $dTerminal ) {
		$this->id = ( integer ) $id;
		$this->service_class = $scid;
		$this->departure_airport = $daid;
		$this->arrival_airport = $aaid;
		$this->op_airline_code = $alid;
		$this->arrival_date = $adid;
		$this->departure_date = $ddid;
		$this->additional_data = $Adata;
		$this->mkt_flight_number = $fnum;
		$this->tag = $tag;
		$this->trip_count = $tripCount;
		$this->service_level = $serviceLevel;
        $this->departure_country_id = $departureCountryId;
        $this->arrival_country_id = $arrivalCountryId;
        $this->dept_time_zone = $timeZone;
        $this->op_flight_number = $opFlightNumber;
        $this->arrival_time_zone = $aTimeZone;
        $this->mkt_airline_code = $mAirlineCode;
        $this->dept_city = $deptCity;
        $this->arrival_city = $arrivalCity;
        $this->aircraft_type = $airCraftType;
        $this->arrival_terminal = $aTerminal;
        $this->dept_terminal = $dTerminal;
	}
	
	/**
	 * Returns the Unique ID for the Flight
	 *
	 * @return integer
	 */
	public function getID() {
		return $this->id;
	}
	/**
	 * Returns the Service Class of a Passenger For that Transaction
	 *
	 * @return string
	 */
	public function getServiceClass() {
		return $this->service_class;
	}
	/**
	 * Returns the Departure Airport of that Passenger
	 *
	 * @return string
	 */
	public function getDepartureAirport() {
		return $this->departure_airport;
	}
	/**
	 * Returns the Arrival Airport of that Passenger
	 *
	 * @return string
	 */
	public function getArrivalAirport() {
		return $this->arrival_airport;
	}
	/**
	 * Returns the Operating Code of that Airline from which Passenger Transacts
	 *
	 * @return string
	 */
	public function getOperatingAirline() {
		return $this->op_airline_code;
	}

    /**
     * Returns the Marketing Code of that Airline from which Passenger Transacts
     *
     * @return string
     */
    public function getMarketingAirline() {
        return $this->mkt_airline_code;
    }
	/**
	 * Returns the date of Arrival of that Passenger
	 *
	 * @return timestamp
	 */
	public function getArrivalDate() {
		return $this->arrival_date;
	}
	/**
	 * Returns the date of Departure of that Passenger
	 *
	 * @return timestamp
	 */
	public function getDepartureDate() {
		return $this->departure_date;
	}
	/**
	 * Returns the Additional Data of this flight
	 *
	 * @return array
	 */
	public function getAdditionalData() {
		return $this->additional_data;
	}
	/**
	 * Returns the marketing flight number of this flight
	 *
	 * @return array
	 */
	public function getMktFlightNumber() {
		return $this->mkt_flight_number;
	}

    /**
     * Returns the operating flight number of this flight
     *
     * @return array
     */
    public function getOpFlightNumber() {
        return $this->op_flight_number;
    }

    /**
     * Returns itinerary sequence of this flight
     * @return string
     */
    public function getATag()
    {
        return $this->tag;
    }

    /**
     * Returns the itinerary segment sequence
     * @return string
     */
    public function getATripCount()
    {
        return $this->trip_count;
    }

    /**
     * Returns the service level of this flight booking
     * @return string
     */
    public function getAServiceLevel()
    {
        return $this->service_level;
    }

    /**
     * Returns the departure country of the flight
     * @return integer
     */
    public function getDepartureCountry()
    {
        return $this->departure_country_id;
    }

    /**
     * Returns the arrival country of the flight
     * @return integer
     */
    public function getArrivalCountry()
    {
        return $this->arrival_country_id;
    }

    /**
     * Returns the departure date time zone
     * @return string
     */
    public function getDeparturetTimeZone()
    {
        return $this->dept_time_zone;
    }

    /**
     * Returns the arrival date time zone
     * @return string
     */
    public function getArrivalTimeZone()
    {
        return $this->arrival_time_zone;
    }

    /**
     * Returns departure city of this flight
     * @return string
     */
    public function getDepartureCity()
    {
        return $this->dept_city;
    }

    /**
     * Returns arrival city of this flight
     * @return string
     */
    public function getArrivalCity()
    {
        return $this->arrival_city;
    }

    /**
     * Returns aircraft type of this flight
     * @return string
     */
    public function getAircraftType()
    {
        return $this->aircraft_type;
    }

    /**
     * Returns arrival terminal of this flight
     * @return string
     */
    public function getArrivalTerminal()
    {
        return $this->arrival_terminal;
    }

    /**
     * Returns departure terminal of this flight
     * @return string
     */
    public function getDepartureTerminal()
    {
        return $this->dept_terminal;
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