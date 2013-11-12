<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol.
 * The PayEx subpackage is a specific implementation capable of imitating CPG's own protocol.
 *
 * @author Jonatan Evald Buus, Tomas Kraina
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage CPG
 * @version 1.00
 */

/**
 * Model Class containing all the Business Logic for handling interaction with CPG
 *
 */
class CPG extends Callback
{
	
	public function getCardName($id)
	{
		switch ($id)
		{
		case (1):	// American Express
			$name = "AMEX-SSL"; 
			break;
		case (2):	// Dankort
			$name = "DANKORT-SSL";
			break;
		case (3):	// Diners Club
			$name = "DINERS-SSL";
			break;
		case (4):	// EuroCard
			$name = "ECMC-SSL";
			break;
		case (5):	// JCB
			$name = "JCB-SSL";
			break;
		case (6):	// Maestro
			$name = "MAESTRO-SSL";
			break;
		case (7):	// MasterCard
			$name = "ECMC-SSL";
			break;
		case (8):	// VISA
			$name = "VISA-SSL";
			break;
		case (9):	// VISA Electron
			$name = "VISA_ELECTRON-SSL";
			break;
		default:	// Unknown
			$name = $id;
			break;
		}
		
		return $name;
	}
	
	public function authTicket(SimpleXMLElement $obj_XML, HTTPConnInfo &$oCI)
	{
		$aClientVars = $this->getMessageData($this->getTxnInfo()->getID(), Constants::iCLIENT_VARS_STATE);
		
		$b = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<submit>';
		$b .= '<shortCode>'. htmlspecialchars($this->getTxnInfo()->getClientConfig()->getAccountConfig()->getName(), ENT_NOQUOTES) .'</shortCode>'; // Short code of the Storefront application 
		$b .= '<order orderCode="'. htmlspecialchars($this->getTxnInfo()->getOrderID(), ENT_NOQUOTES) .'">'; // mandatory, needs to be unique
		list(, $pnr, , ) = explode("/", $this->getTxnInfo()->getOrderID() );
		$b .= '<description>Emirates Airline Ticket Purchase '. $pnr .'</description>';		
		$b .= '<amount value="'. $this->getTxnInfo()->getAmount() .'" currencyCode="'. htmlspecialchars($this->getCurrency($this->getTxnInfo()->getCountryConfig()->getID(), Constants::iCPG_PSP), ENT_NOQUOTES) .'" exponent="2" debitCreditIndicator="credit" />'; 
		if  (array_key_exists("var_tax", $aClientVars) === true)
		{
			$b .= '<tax value="'. $aClientVars["var_tax"] .'" currencyCode="'. htmlspecialchars($this->getCurrency($this->getTxnInfo()->getCountryConfig()->getID(), Constants::iCPG_PSP), ENT_NOQUOTES) .'" exponent="2" />';
		}					
		$b .= '<orderContent>'. htmlspecialchars($this->getTxnInfo()->getDescription(), ENT_NOQUOTES) .'</orderContent>'; 
		$b .= '<paymentDetails>';
		$b .= '<'. $this->getCardName($obj_XML["type-id"]) .'>';
		$b .= '<CCRKey>'.  htmlspecialchars($obj_XML->ticket, ENT_NOQUOTES)  .'</CCRKey>'; // mandatory, 0-20
		$b .= '<cvc>'. intval($obj_XML->cvc) .'</cvc>';    
//		$b .= '<expiryDate>';
//		$b .= '<date month="'. substr($obj_XML->expiry, 0, 2) .'" year="20'. substr($obj_XML->expiry, -2) .'" />'; // mandatory
//		$b .= '</expiryDate>';
//		$b .= '<cardHolderName>'. htmlspecialchars($obj_XML->{'card-holder-name'}, ENT_NOQUOTES) .'</cardHolderName>'; // mandatory
//		$b .= '<paymentCountryCode>'. $this->_getCountryCode(intval($obj_XML->address['country-id']) ) .'</paymentCountryCode>';
		if (array_key_exists("var_fiscal-number", $aClientVars) === true)
		{
			$b .= '<fiscalNumber>'. $aClientVars["var_fiscal-number"] .'</fiscalNumber>';
		}
		if (array_key_exists("var_payment-country-code", $aClientVars) === true)
		{
			$b .= '<paymentCountryCode>'. $aClientVars["var_payment-country-code"] .'</paymentCountryCode>';
		}
		if (array_key_exists("var_number-of-instalments", $aClientVars) === true)
		{
			$b .= '<numberofinstalments>'. $aClientVars["var_number-of-instalments"] .'</numberofinstalments>';
		}			    
		$b .= '<cardAddress>';
		$b .= '<address>';
		$b .= '<firstName>'. htmlspecialchars($obj_XML->address->{'first-name'}, ENT_NOQUOTES) .'</firstName>'; // mandatory, 0-40 chars
		$b .= '<lastName>'. htmlspecialchars($obj_XML->address->{'last-name'}, ENT_NOQUOTES) .'</lastName>'; // mandatory, 0-40 chars
		$b .= '<street>'. htmlspecialchars($obj_XML->address->street, ENT_NOQUOTES) .'</street>'; // mandatory, 0-100 chars
		$b .= '<postalCode>'. intval($obj_XML->address->{'postal-code'}) .'</postalCode>'; // optional, 0-20 chars
		$b .= '<city>'. htmlspecialchars($obj_XML->address->city, ENT_NOQUOTES) .'</city>'; // mandatory, 0-50 chars
		$b .= '<countryCode>'. $this->_getCountryCode(intval($obj_XML->address['country-id']) ) .'</countryCode>'; // mandatory, 2-2 chars
//		$b .= '<telephoneNumber>'. floatval($this->getTxnInfo()->getMobile() ) .'</telephoneNumber>'; // optional
		$b .= '</address>';
		$b .= '</cardAddress>';
		$b .= '</'. $this->getCardName($obj_XML["type-id"]) .'>';
		$b .= '</paymentDetails>';
		$b .= '<shopper>';
		$b .= '<shopperIPAddress>'. htmlspecialchars($this->getTxnInfo()->getIP(), ENT_NOQUOTES) .'</shopperIPAddress>'; // mandatory
		$b .= '<shopperEmailAddress>'. htmlspecialchars($this->getTxnInfo()->getEMail(), ENT_NOQUOTES) .'</shopperEmailAddress>'; // optional, 0-50 chars
		$b .= '<authenticatedShopperID>'. htmlspecialchars($this->getTxnInfo()->getCustomerRef(), ENT_NOQUOTES) .'</authenticatedShopperID>'; // optional, applicable to BIBIT, 0-20 chars
//		$b .= '<authenticatedShopperID />';
		$b .= '</shopper>';
		$b .= '<shippingAddress>';
		$b .= '<address>';
		$b .= '<firstName>'. htmlspecialchars($obj_XML->address->{'first-name'}, ENT_NOQUOTES) .'</firstName>'; // mandatory, 0-40 chars
		$b .= '<lastName>'. htmlspecialchars($obj_XML->address->{'last-name'}, ENT_NOQUOTES) .'</lastName>'; // mandatory, 0-40 chars
		$b .= '<street>'. htmlspecialchars($obj_XML->address->street, ENT_NOQUOTES) .'</street>'; // mandatory, 0-100 chars
		$b .= '<postalCode>'. intval($obj_XML->address->{'postal-code'}) .'</postalCode>'; // optional, 0-20 chars
		$b .= '<city>'. htmlspecialchars($obj_XML->address->city, ENT_NOQUOTES) .'</city>'; // mandatory, 0-50 chars
		$b .= '<countryCode>'. $this->_getCountryCode(intval($obj_XML->address['country-id']) ) .'</countryCode>'; // mandatory, 2-2 chars
//		$b .= '<telephoneNumber>'. floatval($this->getTxnInfo()->getMobile() ) .'</telephoneNumber>'; // optional
		$b .= '</address>';
		$b .= '</shippingAddress>';
		if (array_key_exists("var_enhanced-data", $aClientVars) === true)
		{
			$b .= trim($aClientVars["var_enhanced-data"]);
		}
		$b .= '</order>';
		$b .= '<returnURL>'. htmlspecialchars($this->getTxnInfo()->getAcceptURL(), ENT_NOQUOTES) .'</returnURL>';
		$b .= '</submit>';
		$obj_HTTP = new HTTPClient(new Template(), $oCI);
		$obj_HTTP->connect();
		$code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
		$obj_HTTP->disConnect();
		if ($code == 200)
		{
			$obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
			if (count($obj_XML->orderStatus->redirect) == 1)
			{
				$xml = '<status code="100">';
				$xml .= $obj_XML->orderStatus->pgsp->asXML();
				$xml .= '<url>'. htmlspecialchars($obj_XML->orderStatus->redirect, ENT_NOQUOTES) .'</url>';
				$xml .= '</status>';
			}
			elseif (count($obj_XML->orderStatus->error) == 1)
			{
				$xml = '<status code="92">'. htmlspecialchars($obj_XML->orderStatus->error, ENT_NOQUOTES) .' ('. $obj_XML->orderStatus->error["code"] .')</status>'; ;
			}
			else { $xml = $obj_XML->asXML(); }
		}
		else { $xml = '<status code="92">Rejected with HTTP Code: '. $code .'</status>'; }
		
		return $xml;
	}

	private function _getCountryCode($id)
	{
		switch (intval($id) )
		{
		case (100):	// Denmark
			return "DK";
			break;
		case (101):	// Sweden
			return "SE";
			break;
		case (102):	// Norway
			return "NO";
			break;
		case (104):	// Finland
			return "FI";
			break;
		case (105):	// Greece
			return "GR";
			break;
		case (106):	// Israel
			return "IL";
			break;
		case (107):	// Italy
			return "IT";
			break;
		case (108):	// France
			return "FR";
			break;
		case (109):	// Switzerland
			return "CH";
			break;
		case (110):	// Netherlands
			return "NL";
			break;
		case (111):	// Belgium
			return "BE";
			break;
		case (112):	// Poland
			return "PL";
			break;
		case (113):	// Spain
			return "ES";
			break;
		case (114):	// Austria
			return "AT";
			break;
		case (115):	// Germany
			return "DE";
			break;
		case (116):	// Afghanistan
			return "AF";
			break;
		case (117):	// Albania
			return "AL";
			break;
		case (118):	// Andorra
			return "AD";
			break;
		case (119):	// Armenia
			return "AM";
			break;
		case (120):	// Belarus
			return "BY";
			break;
		case (122):	// Bulgaria
			return "BG";
			break;
		case (123):	// Croatia
			return "HR";
			break;
		case (124):	// Cyprus
			return "CY";
			break;
		case (125):	// Czech Republic
			return "CZ";
			break;
		case (126):	// Estonia
			return "EE";
			break;
		case (127):	// Faroe Islands
			return "FO";
			break;
		case (128):	// Georgia
			return "GE";
			break;
		case (129):	// Gibraltar
			return "GI";
			break;
		case (130):	// Greenland
			return "GL";
			break;
		case (131):	// Hungary
			return "HU";
			break;
		case (132):	// Iceland
			return "IS";
			break;
		case (133):	// Ireland
			return "IE";
			break;
		case (134):	// Isle of Man
			return "IM";
			break;
		case (135):	// Latvia
			return "LV";
			break;
		case (136):	// Liechtenstein
			return "LI";
			break;
		case (137):	// Lithuania
			return "LT";
			break;
		case (138):	// Luxembourg
			return "LU";
			break;
		case (139):	// Malta
			return "MT";
			break;
		case (140):	// Moldova
			return "MD";
			break;
		case (141):	// Monaco
			return "MC";
			break;
		case (142):	// Montenegro
			return "ME";
			break;
		case (143):	// Republic of Macedonia
			return "MK";
			break;
		case (144):	// Monserrat
			return "MS";
			break;
		case (145):	// St. Pierre and Miquelon
			return "PM";
			break;
		case (146):	// Palestinian Territory
			return "PS";
			break;
		case (147):	// Portugal
			return "PT";
			break;
		case (148):	// Kosovo
			return "RK";
			break;
		case (149):	// Romania
			return "RO";
			break;
		case (150):	// Republic of Serbia
			return "RS";
			break;
		case (151):	// San Marino
			return "SM";
			break;
		case (152):	// Slovakia
			return "SK";
			break;
		case (153):	// Slovenia
			return "SI";
			break;
		case (154):	// Turkey
			return "TR";
			break;
		case (155):	// Ukraine
			return "UA";
			break;
		case (156):	// Vatican City State
			return "VA";
			break;
		case (157):	// Yugoslavia
			return "YU";
			break;
		case (201):	// Mexico
			return "MX";
			break;
		case (202):	// Canada
			return "CA";
			break;
		case (203):	// Anguilla
			return "AI";
			break;
		case (204):	// Antigua and Barbuda
			return "AG";
			break;
		case (205):	// Barbados
			return "BB";
			break;
		case (206):	// British Virgin Islands
			return "VG";
			break;
		case (207):	// Cayman Islands
			return "KY";
			break;
		case (208):	// Cuba
			return "CU";
			break;
		case (209):	// Dominican Republic
			return "DO";
			break;
		case (210):	// Guadeloupe
			return "GP";
			break;
		case (211):	// Haiti
			return "HT";
			break;
		case (212):	// Jamaica
			return "JM";
			break;
		case (213):	// American Samoa
			return "AS";
			break;
		case (214):	// Bermuda
			return "BM";
			break;
		case (215):	// Bahamas
			return "BS";
			break;
		case (216):	// Johnston Island
			return "JL";
			break;
		case (217):	// Midway Island
			return "MI";
			break;
		case (218):	// United States Minor Outlying Islands
			return "UM";
			break;
		case (300):	// Algeria
			return "DZ";
			break;
		case (301):	// Angola
			return "AO";
			break;
		case (302):	// Bangladesh
			return "BD";
			break;
		case (303):	// Benin
			return "BJ";
			break;
		case (304):	// Bolivia
			return "BO";
			break;
		case (305):	// Botswana
			return "BW";
			break;
		case (306):	// Burkina Faso
			return "BF";
			break;
		case (307):	// Burundi
			return "BI";
			break;
		case (308):	// Cameroon
			return "CM";
			break;
		case (309):	// Cape Verde
			return "CV";
			break;
		case (310):	// Central African Republic
			return "CF";
			break;
		case (311):	// Chad
			return "TD";
			break;
		case (312):	// Comoros
			return "KM";
			break;
		case (313):	// Congo
			return "CG";
			break;
		case (315):	// Democratic Republic of the Congo
			return "CD";
			break;
		case (316):	// Djibouti
			return "DJ";
			break;
		case (317):	// Egypt
			return "EG";
			break;
		case (318):	// Equatorial Guinea
			return "GQ";
			break;
		case (319):	// Ethiopia
			return "ET";
			break;
		case (320):	// Gabon
			return "GA";
			break;
		case (321):	// Gambia
			return "GM";
			break;
		case (322):	// Ghana
			return "GH";
			break;
		case (323):	// Guinea
			return "GN";
			break;
		case (324):	// Guinea-Bissau
			return "GW";
			break;
		case (325):	// Kenya
			return "KE";
			break;
		case (326):	// Lesotho
			return "LS";
			break;
		case (327):	// Liberia
			return "LR";
			break;
		case (328):	// Madagascar
			return "MG";
			break;
		case (329):	// Malawi
			return "MW";
			break;
		case (330):	// Mali
			return "ML";
			break;
		case (331):	// Mauritania
			return "MR";
			break;
		case (332):	// Mauritius
			return "MU";
			break;
		case (333):	// Morocco
			return "MA";
			break;
		case (334):	// Mozambique
			return "MZ";
			break;
		case (335):	// Tristan Da Cunha
			return "CT";
			break;
		case (336):	// Western Sahara
			return "EH";
			break;
		case (337):	// Eritrea
			return "ER";
			break;
		case (338):	// Libyan Arab Jamahiriya
			return "LY";
			break;
		case (339):	// Mayotte
			return "YT";
			break;
		case (340):	// Namibia
			return "NA";
			break;
		case (341):	// Niger
			return "NE";
			break;
		case (342):	// Nigeria
			return "NG";
			break;
		case (343):	// Reunion
			return "RE";
			break;
		case (344):	// Rwanda
			return "RW";
			break;
		case (345):	// Seychelles
			return "SC";
			break;
		case (346):	// Sudan
			return "SD";
			break;
		case (347):	// St. Helena
			return "SH";
			break;
		case (348):	// Sierra Leone
			return "SL";
			break;
		case (349):	// Sao Tome and Principe
			return "ST";
			break;
		case (350):	// Swaziland
			return "SZ";
			break;
		case (351):	// Togo
			return "TG";
			break;
		case (352):	// Tunisia
			return "TN";
			break;
		case (353):	// Tanzania
			return "TZ";
			break;
		case (354):	// Uganda
			return "UG";
			break;
		case (355):	// South Africa
			return "ZA";
			break;
		case (356):	// Zambia
			return "ZM";
			break;
		case (357):	// Zaire
			return "ZR";
			break;
		case (358):	// Zimbabwe
			return "ZW";
			break;
		case (400):	// Argentina
			return "AR";
			break;
		case (401):	// Aruba
			return "AW";
			break;
		case (402):	// Belize
			return "BZ";
			break;
		case (403):	// Brazil
			return "BR";
			break;
		case (404):	// Chile
			return "CL";
			break;
		case (405):	// Colombia
			return "CO";
			break;
		case (406):	// Costa Rica
			return "CR";
			break;
		case (407):	// Ecuador
			return "EC";
			break;
		case (408):	// El Salvador
			return "SV";
			break;
		case (410):	// Guatemala
			return "GT";
			break;
		case (411):	// Guyana
			return "GY";
			break;
		case (412):	// Honduras
			return "HN";
			break;
		case (413):	// Antarctica
			return "AQ";
			break;
		case (414):	// Carriacou
			return "OU";
			break;
		case (415):	// Netherlands Antilles
			return "AN";
			break;
		case (416):	// Bouvet Island
			return "BV";
			break;
		case (417):	// Scott Base
			return "CB";
			break;
		case (418):	// Dominica
			return "DM";
			break;
		case (419):	// Falkland Islands
			return "FK";
			break;
		case (420):	// Grenada
			return "GD";
			break;
		case (421):	// French Guiana
			return "GF";
			break;
		case (422):	// South Georgia And IS
			return "GS";
			break;
		case (424):	// St. Christopher (St. Kitts) Nevis
			return "KN";
			break;
		case (425):	// St. Lucia
			return "LC";
			break;
		case (426):	// Martinique
			return "MQ";
			break;
		case (427):	// Nicaragua
			return "NI";
			break;
		case (428):	// Panama
			return "PA";
			break;
		case (429):	// Peru
			return "PE";
			break;
		case (430):	// Pitcairn Island
			return "PN";
			break;
		case (431):	// Puerto Rico
			return "PR";
			break;
		case (432):	// Paraguay
			return "PY";
			break;
		case (433):	// Senegal
			return "SN";
			break;
		case (434):	// Somalia
			return "SO";
			break;
		case (435):	// Suriname
			return "SR";
			break;
		case (436):	// St. Maarten
			return "SX";
			break;
		case (437):	// Turks and Caicos Islands
			return "TC";
			break;
		case (438):	// Trinidad and Tobago
			return "TT";
			break;
		case (439):	// Uruguay
			return "UY";
			break;
		case (440):	// St. Vincent and The Grenadines
			return "VC";
			break;
		case (441):	// Venezuela
			return "VE";
			break;
		case (442):	// Virgin Islands, United States
			return "VI";
			break;
		case (500):	// Australia
			return "AU";
			break;
		case (501):	// Brunei Darussalam
			return "BN";
			break;
		case (502):	// Cook Islands
			return "CK";
			break;
		case (503):	// Fiji
			return "FJ";
			break;
		case (504):	// French Polynesia
			return "PF";
			break;
		case (505):	// Indonesia
			return "ID";
			break;
		case (506):	// Micronesia
			return "FM";
			break;
		case (507):	// Cocos (Keeling) Islands
			return "CC";
			break;
		case (508):	// Christmas Island
			return "CX";
			break;
		case (509):	// New Caledonia
			return "NC";
			break;
		case (510):	// Norfolk Island
			return "NF";
			break;
		case (511):	// Nauru
			return "NR";
			break;
		case (512):	// Niue
			return "NU";
			break;
		case (513):	// New Zealand
			return "NZ";
			break;
		case (514):	// Solomon Islands
			return "SB";
			break;
		case (515):	// Tonga
			return "TO";
			break;
		case (516):	// Tuvalu
			return "TV";
			break;
		case (517):	// Vanuatu
			return "VU";
			break;
		case (518):	// Wallis and Futuna Islands
			return "WF";
			break;
		case (519):	// Samoa
			return "WS";
			break;
		case (601):	// Bahrain
			return "BH";
			break;
		case (603):	// India
			return "IN";
			break;
		case (604):	// Kuwait
			return "KW";
			break;
		case (605):	// Oman
			return "OM";
			break;
		case (607):	// Russia
			return "RU";
			break;
		case (609):	// China
			return "CN";
			break;
		case (610):	// Pakistan
			return "PK";
			break;
		case (611):	// Azerbaijan
			return "AZ";
			break;
		case (612):	// Bhutan
			return "BT";
			break;
		case (613):	// Cambodia
			return "KH";
			break;
		case (614):	// Hong Kong
			return "HK";
			break;
		case (615):	// Iran
			return "IR";
			break;
		case (616):	// Japan
			return "JP";
			break;
		case (617):	// Jordan
			return "JO";
			break;
		case (619):	// Kyrgyzstan
			return "KG";
			break;
		case (621):	// Lebanon
			return "LB";
			break;
		case (623):	// Maldives
			return "MV";
			break;
		case (624):	// Mongolia
			return "MN";
			break;
		case (626):	// Diego Garcia
			return "DG";
			break;
		case (627):	// Guam
			return "GU";
			break;
		case (628):	// Iraq
			return "IQ";
			break;
		case (629):	// Kerguelen Archipelago
			return "KA";
			break;
		case (630):	// Kiribati
			return "KI";
			break;
		case (633):	// Kazakstan
			return "KZ";
			break;
		case (634):	// Sri Lanka
			return "LK";
			break;
		case (635):	// Marshall Islands
			return "MH";
			break;
		case (636):	// Macau
			return "MO";
			break;
		case (637):	// Northern Mariana Islands
			return "MP";
			break;
		case (638):	// Malaysia
			return "MY";
			break;
		case (639):	// Nepal
			return "NP";
			break;
		case (640):	// Philippines
			return "PH";
			break;
		case (641):	// Palau
			return "PW";
			break;
		case (642):	// Singapore
			return "SG";
			break;
		case (643):	// Syrian Arab Republic
			return "SY";
			break;
		case (644):	// Thailand
			return "TH";
			break;
		case (645):	// Turkmenistan
			return "TM";
			break;
		case (646):	// Taiwan
			return "TW";
			break;
		case (647):	// United Arab Emirates
			return "AE";
			break;
		case (648):	// Uzbekistan
			return "UZ";
			break;
		case (649):	// Vietnam
			return "VN";
			break;
		case (650):	// Yemen
			return "YE";
			break;
		default:	// Error: Unknown Country
			trigger_error("Unknown Country: ". $id, E_USER_WARNING);
			break;
		}
	}
}
?>