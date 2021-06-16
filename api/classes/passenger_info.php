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
     * Value of title
     */
    private $_Title;
    /**
     * Value of Email
     */
    private $_Email;
    /**
     * Value of Mobile
     */
    private $_Mobile;
    /**
     * Value of Country id
     */
    private $_CountryId;
    /**
     * Amount Paid by Passenger
     */
    private $_Amount;
    /**
     * The sequence number of a passenger
     */
    private $_Seq;

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
		$this->_iID = ( integer ) $id;
		$this->_First_Name = $fnm;
		$this->_Last_Name = $lnm;
		$this->_Type = $type;
		$this->_AdditionalData = $Adata;
		$this->_Title = $title;
		$this->_Email = $email;
		$this->_Mobile = $mobile;
		$this->_CountryId = $countryId;
		$this->_Amount = $amount;
        $this->_Seq = $seq;
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

    /**
     * Returns the title of the Passenger
     * @return string
     */
    public function getTitle()
    {
        return $this->_Title;
    }

    /**
     * Returns the email id of the Passenger
     * @return string
     */
    public function getEmail()
    {
        return $this->_Email;
    }

    /**
     * Returns the mobile number of the Passenger
     * @return string
     */
    public function getMobile()
    {
        return $this->_Mobile;
    }

    /**
     * Returns the country of the Passenger
     * @return string
     */
    public function getCountryId()
    {
        return $this->_CountryId;
    }

    /**
     * Returns the country of the Passenger
     * @return integer
     */
    public function getAmount()
    {
        return $this->_Amount;
    }

    /**
     * Returns the sequence number of a passenger
     * @return integer
     */
    public function getSeqNumber()
    {
        return $this->_Seq;
    }



    public static function produceConfig(RDB $oDB, $id) : ?PassengerInfo {
		$sql = "SELECT id, first_name, last_name, type, order_id, created, modified, title, email, mobile, country_id,amount, seq
					FROM log" . sSCHEMA_POSTFIX . ".passenger_tbl WHERE id=" . $id;
		// echo $sql ."\n";
		$RS = $oDB->getName ( $sql );
		if (is_array ( $RS ) === true && count ( $RS ) > 0) {
			$sqlA = "SELECT name, value FROM log" . sSCHEMA_POSTFIX . ".additional_data_tbl WHERE type='Passenger' and externalid=" . $RS ["ID"];
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
		if ($GLOBALS['oldOrderXml'] === true) {
		    // return old AID format
            $xml = $this->_toOldXML();
        } else {
		    //return new AID format
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
        }

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