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
			break;
		}
		
		return $name;
	}
	
	public function authTicket(SimpleXMLElement $or_XML, HTTPConnInfo &$oCI)
	{
		$obj_SOAP = new SOAPClient("https://". $oCI->getHost() . $oCI->getPath(), array("trace" => true, "exceptions" => true) );
		
		$shortCode = $this->getTxnInfo()->getClientConfig()->getAccountConfig()->getName();
		$currency = EUR;
		$exponent = 2;
        $orderContent = $this->getTxnInfo()->getDescription();
        $shopperIPAddress = $this->getTxnInfo()->getIP();
        $shopperEmailAddress = htmlspecialchars($this->getTxnInfo()->getEMail(), ENT_NOQUOTES);
        $authenticatedShopperID = $this->getTxnInfo()->getCustomerRef();
        $sOrderCode = $this->getTxnInfo()->getOrderID();
        if (empty($sOrderCode) === true) {
        	$sOrderCode = $this->getTxnInfo()->getID();
        }       
        $exponent = 2;
        $debitCardIndication = 'credit';     
        $oldOrder = $or_XML->ticket;
        $description = "mPoint ID: ". $this->getTxnInfo()->getID() ." for Order No.:". $this->getTxnInfo()->getOrderID();          
        $creditCardInfoAvailable = FALSE;       
        $billingAddressAvailable = FALSE;
        // NEEDS expansion of getStoredCards()
        $firstName = $or_XML->firstName;
        $lastName = $or_XML->lastName;
        $street = $or_XML->street;
        $city = $or_XML->city;
        $postalCode = $or_XML->postalCode;
        $countryCode = $or_XML->countryCode;
        $mobilNumber = $or_XML->mobilNumber;
        $nominalAuth = NO;
        
		$b = '<?xml version="1.0" encoding="UTF-8"?>';
        $b .= '<submit>';
        $b .= ' <shortCode>'. $shortCode .'</shortCode>'; // Short code of the Storefront application 
        $b .= ' <order orderCode="'. $orderCode .'">'; // mandatory, needs to be unique
        $b .= ' <oldOrder> '. $oldOrder .' </oldOrder>'; // Optional this is our storedcard
        if ($nominalAuth === YES)
        {
            $b .= '  <nominalAuth>Y</nominalAuth>'; // Optional, only storefronts that can accept nominalAuth need to send this element    
        }
        $b .= '  <description>'. htmlspecialchars($description, ENT_NOQUOTES) .'</description>'; // Mandatory, maxlenght=50, simple one line description of the order
        //TODO currency / exponent ??
        $b .= '  <amount value="'. $this->getTxnInfo()->getAmount() .'" curencyCode="'. $currency .'" exponent="'. $exponent .'" debitCardIndication="'. $debitCardIndication .'"/>'; // Mandatory, decimal based on exponent, code uppercase ISO4217, indicator should be always 'credit'
        $b .= '  <orderContent>';
        $b .= '   <![CDATA]['. htmlspecialchars($orderContent, ENT_NOQUOTES) .']]>'; // don't use <html><body> tags
        $b .= '  </orderContent>';
        //  START ************** MAYBE TO BE USED IN FUTURE
        if ($billingAddressAvailable === TRUE)
        {
            $b .= '  <paymentDetails>';
            if ( $creditCardInfoAvailable === TRUE)
            {
                // TODO: get the card info somehow...
                $b .= '   <'. $this->getCardName($cardId) .'>';
                $b .= '    <cardNumber> '. 123123123123123 .' </cardNumber>'; // mandatory, 0-20           
                $b .= '    <cvc> '. 123 .' </cvc>';       // optional
                $b .= '    <expiryDate>';
                $b .= '     <date month="'. 01 .'" year="'. 02 .'" />'; // mandatory
                $b .= '    </expiryDate>';
                $b .= '    <cardHolderName>'. Jona.Cpm .'</cardHolderName>'; // mandatory
            }
            $b .= '    <cardAddress>';
           	$b .= '   <address>';
        	$b .= '    <firstName>'. $firstName .'</firstName>'; // mandatory, 0-40 chars
        	$b .= '    <lastName>'. $lastName .'</lastName>'; // mandatory, 0-40 chars
        	$b .= '    <street>'. $street .'</street>'; // mandatory, 0-100 chars
        	$b .= '    <postalCode>'. $postalCode .'</postalCode>'; // optional, 0-20 chars
        	$b .= '    <city>'. $city .'</city>'; // mandatory, 0-50 chars
        	$b .= '    <countryCode>'. $countryCode .'</countryCode>'; // mandatory, 2-2 chars
	        $b .= '    <telephoneNumber>'. $mobilNumber .'</telephoneNumber>'; // optional
	        $b .= '   </address>';
            $b .= '    </cardAddress>';
            if ($creditCardInfoAvailable)
            {
                $b .= '   </.'. $this->getCardName($cardId) .'>';
            }
            $b .= '  </paymentDetails>';
        }
        //  END ************** MAYBE TO BE USED IN FUTURE
        $b .= '  <shopper>';
        $b .= '   <shopperIPAddress>'. $shopperIPAddress .'</shopperIPAddress>'; // mandatory
        $b .= '   <shopperEmailAddress>'. $shopperEmailAddress .'</shopperEmailAddress>'; // optional, 0-50 chars
        $b .= '   <authenticatedShopperID>'. $authenticatedShopperID .'</authenticatedShopperID>'; // optional, applicable to BIBIT, 0-20 chars
        $b .= '  </shopper>';
        // NEEDS expansion of getStoredCards()
        $b .= '  <shippingAddress>';
        $b .= '   <address>';
        $b .= '    <firstName>'. $firstName .'</firstName>'; // mandatory, 0-40 chars
        $b .= '    <lastName>'. $lastName .'</lastName>'; // mandatory, 0-40 chars
        $b .= '    <street>'. $street .'</street>'; // mandatory, 0-100 chars
        $b .= '    <postalCode>'. $postalCode .'</postalCode>'; // optional, 0-20 chars
        $b .= '    <city>'. $city .'</city>'; // mandatory, 0-50 chars
        $b .= '    <countryCode>'. $countryCode .'</countryCode>'; // mandatory, 2-2 chars
        $b .= '    <telephoneNumber>'. $mobilNumber .'</telephoneNumber>'; // optional
        $b .= '   </address>';
        $b .= '  </shippingAddress>';
        $b .= ' </order>';
        // TODO: check the url
        $b .= ' <returnURL>'. "http://". $_SERVER['HTTP_HOST'] ."/pay/accept.php?mpoint-id=". $this->getTxnInfo()->getID() .'</returnURL>';
//        $b.= ' <teaLeafGuid>452ef50f-68ef-4677-9b19-d7140c444d19</teaLeafGuid>'; // ???
        $b .= '</submit>';
        
        //TODO: send the request and follow the description in JIRA
        
        $obj_XML = simplexml_load_string($b);
        $obj_Std = $obj_SOAP->InitializePayment($obj_XML);
  
        $cpg_XML = simplexml_load_string($obj_Std->InitializePaymentResult);
        
        $redirect = $cpg_XML->xpath('//redirect');
        
        if(empty($redirect[0]) === false)
        {
        	$return_xml = '<status code="100">';
        	$return_xml = '<url> '. htmlspecialchars($redirect[0], ENT_NOQUOTES) .' </url>';
        	$return_xml = '<status>';
        }
        
		return $return_XML;
	}
	
}
?>