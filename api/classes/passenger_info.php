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
	private $id;
	/**
	 * Value for First Name
	 */
	private $first_name;
	/**
	 * Value for Last Name
	 */
	private $last_name;
	/**
	 * Value of Type
	 */
	private $type;
	/**
	 * Data for Additional info related to Passenger
	 *
	 * @var integer
	 */
	private $additional_data;

    /**
     * Value of title
     */
    private $title;
    /**
     * Value of Email
     */
    private $email;
    /**
     * Value of Mobile
     */
    private $mobile;
    /**
     * Value of Country id
     */
    private $country_id;
    /**
     * Amount Paid by Passenger
     */
    private $amount;
    /**
     * The sequence number of a passenger
     */
    private $seq;

    /**
     * Default Constructor
     * @param $id
     * @param $fnm
     * @param $lnm
     * @param $type
     * @param $title
     * @param $email
     * @param $mobile
     * @param $countryId
     * @param $amount
     * @param $seq
     * @param $Adata
     */
	public function __construct($id, $fnm, $lnm, $type, $title, $email, $mobile, $countryId, $amount, $seq, $Adata = null) {
		$this->id = ( integer ) $id;
		$this->first_name = $fnm;
		$this->last_name = $lnm;
		$this->type = $type;
		$this->additional_data = $Adata;
		$this->title = $title;
		$this->email = $email;
		$this->mobile = $mobile;
		$this->country_id = $countryId;
		$this->amount = $amount;
        $this->seq = $seq;
	}
	
	/**
	 * Returns the Unique ID for the Passenger
	 *
	 * @return integer
	 */
	public function getID() {
		return $this->id;
	}
	/**
	 * Returns the First Name of a Passenger For that Transaction
	 *
	 * @return string
	 */
	public function getFirstName() {
		return $this->first_name;
	}
	/**
	 * Returns the Last Name of a Passenger For that Transaction
	 *
	 * @return string
	 */
	public function getLastName() {
		return $this->last_name;
	}
	/**
	 * Returns the type of the Passenger
	 *
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}
	/**
	 * Returns the Additional Data of the passenger
	 *
	 * @return array
	 */
	public function getAdditionalData() {
		return $this->additional_data;
	}

    /**
     * Returns the title of the Passenger
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns the email id of the Passenger
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Returns the mobile number of the Passenger
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Returns the country of the Passenger
     * @return string
     */
    public function getCountryId()
    {
        return $this->country_id;
    }

    /**
     * Returns the country of the Passenger
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Returns the sequence number of a passenger
     * @return integer
     */
    public function getSeqNumber()
    {
        return $this->seq;
    }



    public static function produceConfig(RDB $oDB, $id) : ?PassengerInfo {
		$sql = "SELECT id, first_name, last_name, type, order_id, created, modified, title, email, mobile, country_id,amount, seq
					FROM log" . sSCHEMA_POSTFIX . ".passenger_tbl WHERE id=" . $id;
		// echo $sql ."\n";
		$RS = $oDB->getName ( $sql );
		if (is_array ( $RS ) === true && count ( $RS ) > 0) {
			$sqlA = "SELECT name, value FROM log" . sSCHEMA_POSTFIX . ".additional_data_tbl WHERE type='Passenger' and created >= '" . $RS["CREATED"]  . "'::timestamp  - interval '60 seconds' and externalid=" . $RS ["ID"];
			// echo $sqlA;
			$RSA = $oDB->getAllNames ( $sqlA );
			
			if (is_array ( $RSA ) === true && count ( $RSA ) > 0) {
				return new PassengerInfo ( $RS ["ID"], $RS ["FIRST_NAME"], $RS ["LAST_NAME"], $RS ["TYPE"], $RS ["TITLE"],$RS ["EMAIL"],$RS ["MOBILE"],$RS ["COUNTRY_ID"],$RS ["AMOUNT"], $RS["SEQ"], $RSA );
			} else {
				return new PassengerInfo ( $RS ["ID"], $RS ["FIRST_NAME"], $RS ["LAST_NAME"], $RS ["TYPE"], $RS ["TITLE"],$RS ["EMAIL"],$RS ["MOBILE"],$RS ["COUNTRY_ID"],$RS ["AMOUNT"], $RS["SEQ"]);
			}
		} else {
            trigger_error('Unable to create Passenger Info object', E_USER_NOTICE);
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
		$Axml = '<param name="' . $aDataArr ["NAME"] . '">' . htmlspecialchars($aDataArr ["VALUE"]) . '</param>';
		return $Axml;
	}

    public function getAdditionalDataAttributeLess($aDataArr) {
        $Axml = '<param>';
        $Axml .=  '<name>'. $aDataArr ["NAME"] . '</name>';
        $Axml .=  '<value>'. htmlspecialchars($aDataArr ["VALUE"]) . '</value>';
        $Axml .= '</param>';
        return $Axml;
    }
	
	public function toXML()
    {
		$xml = '';

        $xml .= '<profile>';
        $xml .= '<seq>' . $this->getSeqNumber() . '</seq>';
        $xml .= '<title>' . $this->getTitle() . '</title>';
        $xml .= '<first-name>' . $this->getFirstName () . '</first-name>';
        $xml .= '<last-name>' . $this->getLastName () . '</last-name>';
        $xml .= '<type>' . $this->getType () . '</type>';
        if ($this->getAmount() > 0) { $xml .= '<amount>' . $this->getAmount() . '</amount>'; }
        if ($this->getEmail() || $this->getMobile())
        {
            $xml .= '<contact-info>';
            $xml .= '<email>' . $this->getEmail() .'</email>';
            $xml .= '<mobile country-id="' . $this->getCountryId() .'">' . $this->getMobile() .'</mobile>';
            $xml .= '</contact-info>';
        }
        if ($this->getAdditionalData ()) {
            $xml .= '<additional-data>';
            foreach ( $this->getAdditionalData () as $pAdditionalData ) {
                $xml .= $this->getAdditionalDataArr ( $pAdditionalData );
            }
            $xml .= '</additional-data>';
        } else {
        }
        $xml .= '</profile>';

		return $xml;
	}

	private function _toOldXML()
    {
        $xml = '';
        $xml .= '<passenger-detail>';
        $xml .= '<title>' . $this->getTitle() . '</title>';
        $xml .= '<first-name>' . $this->getFirstName () . '</first-name>';
        $xml .= '<last-name>' . $this->getLastName () . '</last-name>';
        $xml .= '<type>' . $this->getType () . '</type>';
        if ($this->getAmount() > 0) { $xml .= '<amount>' . $this->getAmount() . '</amount>'; }
        if ($this->getEmail() || $this->getMobile())
        {
            $xml .= '<contact-info>';
            $xml .= '<email>' . $this->getEmail() .'</email>';
            $xml .= '<mobile country-id="' . $this->getCountryId() .'">' . $this->getMobile() .'</mobile>';
            $xml .= '</contact-info>';
        }
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

    public function toAttributeLessXML()
    {
        $xml = '';
        $xml .= '<passengerDetail>';
        $xml .= '<title>' . $this->getTitle() . '</title>';
        $xml .= '<firstName>' . $this->getFirstName () . '</firstName>';
        $xml .= '<lastName>' . $this->getLastName () . '</lastName>';
        $xml .= '<type>' . $this->getType () . '</type>';
        if ($this->getAmount() > 0) { $xml .= '<amount>' . $this->getAmount() . '</amount>'; }
        if ($this->getEmail() || $this->getMobile())
        {
            $xml .= '<contactInfo>';
            $xml .= '<email>' . $this->getEmail() .'</email>';
            $xml .= '<mobile country-id="' . $this->getCountryId() .'">';
            $xml .=  '<countryId>'.$this->getCountryId().'</countryId>';
            $xml .=  '<value>'.$this->getMobile().'</value>';
            $xml .= '</mobile>';
            $xml .= '</contactInfo>';
        }
        if ($this->getAdditionalData ()) {
            $xml .= '<additionalData>';
            foreach ( $this->getAdditionalData () as $pAdditionalData )
            {
                $xml .= $this->getAdditionalDataAttributeLess ( $pAdditionalData );
            }
            $xml .= '</additionalData>';
        } else {
        }
        $xml .= '</passengerDetail>';
        return $xml;
    }
}
?>