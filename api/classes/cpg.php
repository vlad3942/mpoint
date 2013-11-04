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
		$b .= '<amount value="'. $this->getTxnInfo()->getAmount() .'" currencyCode="'. htmlspecialchars($this->getCurrency($this->getTxnInfo()->getClientConfig()->getCountryConfig()->getID(), Constants::iCPG_PSP), ENT_NOQUOTES) .'" exponent="2" debitCreditIndicator="credit" />'; 
		if  (array_key_exists("var_tax", $aClientVars) === true)
		{
			$b .= '<tax value="'. $aClientVars["var_tax"] .'" currencyCode="'. htmlspecialchars($this->getCurrency($this->getTxnInfo()->getClientConfig()->getCountryConfig()->getID(), Constants::iCPG_PSP), ENT_NOQUOTES) .'" exponent="2" />';
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
			$b .= '<fiscalNumber>"'. $aClientVars["var_fiscal-number"] .'"</fiscalNumber>';
		}
		if (array_key_exists("var_payment-country-code", $aClientVars) === true)
		{
			$b .= '<paymentCountryCode>"'. $aClientVars["var_payment-country-code"] .'"</paymentCountryCode>';
		}
		if (array_key_exists("var_number-of-instalments", $aClientVars) === true)
		{
			$b .= '<numberofinstalments>"'. $aClientVars["var_number-of-instalments"] .'"</numberofinstalments>';
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
		case (101): // Sweden
			return "SE";
			break;
	    case (102):	// Norway
	    	return "NO";
	    	break;
		case (103): // UK
			return "GB";
			break;
	    case (105):	// Afghanistan
	    	return "AF";
	    	break;
	    case (106):	// Albania
	    	return "AL";
	    	break;
	    case (107):	// Andorra
	    	return "AD";
	    	break;
	    case (108):	// Austria
	    	return "AT";
	    	break;
	    case (109):	// Belarus
	    	return "BY";
	    	break;
	    case (110):	// Belgium
	    	return "BE";
	    	break;
	    case (111):	// Bosnia  Herzegovina
	    	return "BA";
	    	break;
	    case (112):	// Bulgaria
	    	return "BG";
	    	break;
	    case (113): // Croatia
	    	return "HR";
	    	break;
	    case (114): // Cyprus
	    	return "CY";
	    	break;
	    case (115): // Czech Republic
	    	return "CZ";
	    	break;
	    case (116):	// Estonia
	    	return "EE";
	    	break;
	     // Faroe Islands
	    case (117): return "FO"; break;
	     // France
	    case (118): return "FR"; break;
	     // Georgia
	    case (119): return "GE"; break;
	     // Germany
	    case (120): return "DE"; break;
	     // Gibraltar
	    case (121): return "GI"; break;
	     // Greece
	    case (122): return "GR"; break;
	     // Greenland
	    case (123): return "GL"; break;
	     // Hungary
	    case (124): return "HU"; break;
	     // Iceland
	    case (125): return "IS"; break;
	     // Ireland
	    case (126): return "IE"; break;
	     // Italy
	    case (127): return "IT"; break;
	     // Latvia
	    case (128): return "LV"; break;
	     // Liechtenstein
	    case (129): return "LI"; break;
	     // Lithuania
	    case (130): return "LT"; break;
	     // Luxembourg
	    case (131): return "LU"; break;
	     // Malta
	     case (132): return "MT"; break;
	     // Moldova
	    case (133): return "MD"; break;
	     // Mexico
	    case (201): return "MX"; break;
	     // Canada
	    case (202): return "CA"; break;
	     // Anguilla
	    case (203): return "AI"; break;
	     // Antigua and Barbuda
	    case (204): return "AG"; break;
	     // Barbados
	    case (205): return "BB"; break;
	     // British Virgin Islands
	    case (206): return "VG"; break;
	     // Cayman Islands
	    case (207): return "KY"; break;
	     // Cuba
	     case (208): return "CU"; break;
	     // Dominican Republic
	    case (209): return "DO"; break;
	     // Guadeloupe
	    case (210): return "GP"; break;
	     // Haiti
	     case (211): return "HT"; break;
	     // Jamaica
	    case (212): return "JM"; break;
	     // Algeria
	    case (300): return "DZ"; break;
	     // Angola
	    case (301): return "AO"; break;
	     // Bahrain
	    case (302): return "BH"; break;
	     // Bangladesh
	    case (303): return "BD"; break;
	     // Benin
	     case (304): return "BJ"; break;
	     // Bolivia
	    case (305): return "BO"; break;
	     // Botswana
	    case (306): return "BW"; break;
	     // Burkina Faso
	    case (307): return "BF"; break;
	     // Burundi
	    case (308): return "BI"; break;
	     // Cameroon
	    case (309): return "CM"; break;
	     // Cape Verde
	    case (310): return "CV"; break;
	     // Central African Republic
	    case (311): return "CF"; break;
	     // Chad
	     case (312): return "TD"; break;
	     // Comoros
	    case (313): return "KM"; break;
	     // Congo
	     case (314): return "CG"; break;
	     // Côte d'Ivoire
	     case (315): return "CI"; break;
	    // Democratic Republic of the Congo
	     case (316): return "CD"; break;
	    // Djibouti
		case (317): return "DJ"; break;
	    // Egypt
		case (318): return "EG"; break;
	    // Equatorial Guinea
	     case (319): return "GQ"; break;
	    // Ethiopia
		case (320): return "ET"; break;
	    // Gabon
		case (321): return "GA"; break;
	    // Gambia
	     case (322): return "GM"; break;
	    // Ghana
		case (323): return "GH"; break;
	    // Guinea
	     case (324): return "GN"; break;
	    // Guinea-Bissau
	     case (325): return "GW"; break;
	    // Kenya
		case (326): return "KE"; break;
	    // Lesotho
	     case (327): return "LS"; break;
	    // Liberia
	     case (328): return "LR"; break;
	    // Madagascar
	     case (329): return "MG"; break;
	    // Malawi
	     case (330): return "MW"; break;
	    // Mali
		case (331): return "ML"; break;
	    // Mauritania
	     case (332): return "MR"; break;
	    // Mauritius
	     case (333): return "MU"; break;
	    // Morocco
	     case (334): return "MA"; break;
	    // Mozambique
	     case (335): return "MZ"; break;
	    // Argentina
	     case (400): return "AR"; break;
	    // Aruba
		case (401): return "AW"; break;
	    // Belize
	     case (402): return "BZ"; break;
	    // Brazil
	     case (403): return "BR"; break;
	    // Chile
		case (404): return "CL"; break;
	    // Colombia
		case (405): return "CO"; break;
	    // Costa Rica
	     case (406): return "CR"; break;
	    // Ecuador
	     case (407): return "EC"; break;
	    // El Salvador
	     case (408): return "SV"; break;
	    // Guatemala
	     case (410): return "GT"; break;
	    // Guyana
	     case (411): return "GY"; break;
	    // Honduras
		case (412): return "HN"; break;
	    // Australia
	     case (500): return "AU"; break;
	    // Brunei Darussalam
	     case (501): return "BN"; break;
	    // Cook Islands
	     case (502): return "CK"; break;
	    // Fiji
		case (503): return "FJ"; break;
	    // French Polynesia
	     case (504): return "PF"; break;
	    // Indonesia
	     case (505): return "ID"; break;
	    // Micronesia
	     case (506): return "FM"; break;
	    // Azerbaijan
	     case (600): return "AZ"; break;
	    // Bhutan
	     case (601): return "BT"; break;
	    // Cambodia
		case (602): return "KH"; break;
	    // China
		case (603): return "CN"; break;
	    // Hong Kong
	     case (604): return "HK"; break;
	    // Iran
		case (605): return "IR"; break;
	    // India
		case (606): return "IN"; break;
	    // Israel
	     case (607): return "IL"; break;
	    // Japan
		case (608): return "JP"; break;
	    // Jordan
	     case (609): return "JO"; break;
	    // Kazakhstan
	     case (610): return "KZ"; break;
	    // Kuwait
	     case (611): return "KW"; break;
	    // Kyrgyzstan
	     case (612): return "KG"; break;
	    // Maldives
		case (613): return "MV"; break;
	    // Mongolia
		case (614): return "MN"; break;
	    // United Arab Emirates
	     case (701): return "AE"; break;
	    // Armenia
	     case (702): return "AM"; break;
	    // Netherlands Antilles
	     case (703): return "AN"; break;
	    // Antarctica
	     case (704): return "AQ"; break;
	    // American Samoa
	     case (705): return "AS"; break;
	    // Bermuda
	     case (706): return "BM"; break;
	    // Bahamas
	     case (707): return "BS"; break;
	    // Bouvet Island
	     case (708): return "BV"; break;
	    // Scott Base
	     case (709): return "CB"; break;
	    // Cocos (Keeling) Islands
	     case (710): return "CC"; break;
	    // Tristan Da Cunha
	     case (711): return "CT"; break;
	    // Christmas Island
	     case (712): return "CX"; break;
	    // Diego Garcia
	     case (713): return "DG"; break;
	    // Dominica
		case (714): return "DM"; break;
	    // Western Sahara
	     case (715): return "EH"; break;
	    // Eritrea
	     case (716): return "ER"; break;
	    // Falkland Islands
	     case (717): return "FK"; break;
	    // Grenada
	     case (718): return "GD"; break;
	    // French Guiana
	     case (719): return "GF"; break;
	    // South Georgia And IS
	     case (720): return "GS"; break;
	    // Guam
		case (721): return "GU"; break;
	    // Isle of Man
	     case (722): return "IM"; break;
	    // British Int Ocean Tertry
	     case (723): return "IO"; break;
	    // Iraq
		case (724): return "IQ"; break;
	    // Johnston Island
	     case (725): return "JL"; break;
	    // Kerguelen Archipelago
	     case (726): return "KA"; break;
	    // Kiribati
		case (727): return "KI"; break;
	    // Kaliningrad
	     case (728): return "KL"; break;
	    // St. Christopher (St. Kitts) Nevis
	     case (729): return "KN"; break;
	    // Korea, Democratic Peoples Republic of
	     case (730): return "KP"; break;
	    // Republic of Korea
	     case (731): return "KR"; break;
	    // Lao Peoples Democratic Republic
	     case (732): return "LA"; break;
	    // St. Lucia
	     case (733): return "LC"; break;
	    // Sri Lanka
	     case (734): return "LK"; break;
	    // Libyan Arab Jamahiriya
	     case (735): return "LY"; break;
	    // Monaco
	     case (736): return "MC"; break;
	    // Montenegro
	     case (737): return "ME"; break;
	    // Marshall Islands
	     case (738): return "MH"; break;
	    // Midway Island
	     case (739): return "MI"; break;
	    // Republic of Macedonia
	     case (740): return "MK"; break;
	    // Myanmar, Union of
	     case (741): return "MM"; break;
	    // Macau
		case (742): return "MO"; break;
	    // Northern Mariana Islands
	     case (743): return "MP"; break;
	    // Martinique
	     case (744): return "MQ"; break;
	    // Monserrat
	     case (745): return "MS"; break;
	    // Malaysia
		case (746): return "MY"; break;
	    // Namibia
	     case (747): return "NA"; break;
	    // New Caledonia
	     case (748): return "NC"; break;
	    // Not Defined
	     case (749): return "ND"; break;
	    // Niger
		case (750): return "NE"; break;
	    // Norfolk Island
	     case (751): return "NF"; break;
	    // Nigeria
	     case (752): return "NG"; break;
	    // Nicaragua
	     case (753): return "NI"; break;
	    // Nepal
		case (754): return "NP"; break;
	    // Nauru
		case (755): return "NR"; break;
	    // Niue
		case (756): return "NU"; break;
	    // New Zealand
	     case (757): return "NZ"; break;
	    // Carriacou
	     case (758): return "OU"; break;
	    // Panama
	     case (759): return "PA"; break;
	    // Peru
		case (760): return "PE"; break;
	    // Philippines
	     case (761): return "PH"; break;
	    // St. Pierre and Miquelon
	     case (762): return "PM"; break;
	    // Pitcairn Island
	     case (763): return "PN"; break;
	    // Puerto Rico
	     case (764): return "PR"; break;
	    // Palestinian Territory
	     case (765): return "PS"; break;
	    // Portugal
		case (766): return "PT"; break;
	    // Palau
		case (767): return "PW"; break;
	    // Paraguay
		case (768): return "PY"; break;
	    // Qatar
		case (769): return "QA"; break;
	    // Reunion
	     case (770): return "RE"; break;
	    // Kosovo
	     case (771): return "RK"; break;
	    // Romania
	     case (772): return "RO"; break;
	    // Republic of Serbia
	     case (773): return "RS"; break;
	    // Rwanda
	     case (774): return "RW"; break;
	    // Solomon Islands
	     case (775): return "SB"; break;
	    // Seychelles
	     case (776): return "SC"; break;
	    // Sudan
		case (777): return "SD"; break;
	    // Singapore
	     case (778): return "SG"; break;
	    // St. Helena
	     case (779): return "SH"; break;
	    // Slovenia
		case (780): return "SI"; break;
	    // Slovakia
		case (781): return "SK"; break;
	    // Sierra Leone
	     case (782): return "SL"; break;
	    // San Marino
	     case (783): return "SM"; break;
	    // Senegal
	     case (784): return "SN"; break;
	    // Somalia
	     case (785): return "SO"; break;
	    // Suriname
		case (786): return "SR"; break;
	    // Sao Tome and Principe
	     case (787): return "ST"; break;
	    // St. Maarten
	     case (788): return "SX"; break;
	    // Syrian Arab Republic
	     case (789): return "SY"; break;
	    // Swaziland
	     case (790): return "SZ"; break;
	    // Turks and Caicos Islands
	     case (791): return "TC"; break;
	    // Togo
		case (792): return "TG"; break;
	    // Thailand
		case (793): return "TH"; break;
	    // Turkmenistan
	     case (794): return "TM"; break;
	    // Tunisia
	     case (795): return "TN"; break;
	    // Tonga
		case (796): return "TO"; break;
	    // Turkey
	     case (797): return "TR"; break;
	    // Trinidad and Tobago
	     case (798): return "TT"; break;
	    // Tuvalu
	     case (799): return "TV"; break;
	    // Taiwan
	     case (800): return "TW"; break;
	    // Tanzania
		case (801): return "TZ"; break;
	    // Ukraine
	     case (802): return "UA"; break;
	    // Uganda
	     case (803): return "UG"; break;
	    // United States Minor Outlying Islands
	     case (804): return "UM"; break;
	    // United States
	     case (805): return "US"; break;
	    // Unserviced Destn
	     case (806): return "UX"; break;
	    // Uruguay
	     case (807): return "UY"; break;
	    // Uzbekistan
	     case (808): return "UZ"; break;
	    // Vatican City State
	     case (809): return "VA"; break;
	    // St. Vincent and The Grenadines
	     case (810): return "VC"; break;
	    // Venezuela
	     case (811): return "VE"; break;
	    // Virgin Islands, United States
	     case (812): return "VI"; break;
	    // Viet Nam
		case (813): return "VN"; break;
	    // Vanuatu
	     case (814): return "VU"; break;
	    // Wallis and Futuna Islands
	     case (815): return "WF"; break;
	    // Samoa
		case (816): return "WS"; break;
	    // Yemen
		case (817): return "YE"; break;
	    // Mayotte
	     case (818): return "YT"; break;
	    // Yugoslavia
	     case (819): return "YU"; break;
	    // South Africa
	     case (820): return "ZA"; break;
	    // Zambia
	     case (821): return "ZM"; break;
	    // Zaire
		case (822): return "ZR"; break;
	    // Zimbabwe
		case (823): return "ZW"; break;
	    // Switzerland
	     case (824): return "CH"; break;
	    // Spain
		case (825): return "ES"; break;
	    // Finland
	     case (826): return "FI"; break;
	    // Netherlands
	     case (827): return "NL"; break;
	    // Oman
		case (828): return "OM"; break;
	    // Papua New Guinea
	     case (829): return "PG"; break;
	    // Pakistan
		case (830): return "PK"; break;
	    // Poland
	     case (831): return "PL"; break;
	    // Russia
	     case (832): return "RU"; break;
	    // Saudi Arabia
	     case (833): return "SA"; break;
	    // Lebanon
	     case (834): return "LB"; break;
		}
	}
}
?>