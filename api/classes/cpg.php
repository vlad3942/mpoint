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
	
	public function authTicket(SimpleXMLElement $obj_XML, HTTPConnInfo &$oCI)
	{
		$obj_SOAP = new SOAPClient("https://". $oCI->getHost() . $oCI->getPath(), array("trace" => true, "exceptions" => true) );
		 
		$b = '<?xml version="1.0" encoding="UTF-8"?>';
        $b .= '<submit>';
        $b .= ' <shortCode>'. htmlspecialchars( $this->getTxnInfo()->getClientConfig()->getAccountConfig()->getName(),ENT_NOQUOTES ) .'</shortCode>'; // Short code of the Storefront application 
        $b .= ' <order orderCode="'. htmlspecialchars( $this->getTxnInfo()->getOrderID(),ENT_NOQUOTES  ) .'">'; // mandatory, needs to be unique
        $b .= ' <oldOrder> '. htmlspecialchars( $obj_XML->ticket,ENT_NOQUOTES ) .' </oldOrder>'; // Optional this is our storedcard
        $b .= '  <description>'. htmlspecialchars("mPoint ID: ". $this->getTxnInfo()->getID() ." for Order No.:". $this->getTxnInfo()->getOrderID() , ENT_NOQUOTES) .'</description>';        
        $b .= '  <amount value="'. htmlspecialchars($this->getTxnInfo()->getAmount(),ENT_NOQUOTES ) .'" curencyCode="'. htmlspecialchars($this->getCurrency($this->getTxnInfo()->getClientConfig()->getCountryConfig()->getID(), Constants::iCPM_PSP),ENT_NOQUOTES ) .'" exponent="2" debitCardIndication="credit"/>'; 
        $b .= '  <orderContent>';
        $b .= '   <![CDATA]['. htmlspecialchars( $this->getTxnInfo()->getDescription(), ENT_NOQUOTES) .']]>'; // don't use <html><body> tags
        $b .= '  </orderContent>';
        // This needs to be added later as we dont have the billing address for now 
        $b .= '  <paymentDetails>';
        $b .= '    <cvc> '. intval($obj_XML->cvc) .' </cvc>';    
        $b .= '  </paymentDetails>';
        
        //  END ************** MAYBE TO BE USED IN FUTURE
        $b .= '  <shopper>';
        $b .= '   <shopperIPAddress>'. htmlspecialchars($this->getTxnInfo()->getIP(), ENT_NOQUOTES) .'</shopperIPAddress>'; // mandatory
        $b .= '   <shopperEmailAddress>'. htmlspecialchars($this->getTxnInfo()->getEMail(), ENT_NOQUOTES) .'</shopperEmailAddress>'; // optional, 0-50 chars
        $b .= '   <authenticatedShopperID>'. htmlspecialchars($this->getTxnInfo()->getCustomerRef(),ENT_NOQUOTES ) .'</authenticatedShopperID>'; // optional, applicable to BIBIT, 0-20 chars
        $b .= '  </shopper>';
        // NEEDS expansion of getStoredCards()
        $b .= '  <shippingAddress>';
        $b .= '   <address>';
        $b .= '    <firstName>'. htmlspecialchars($obj_XML->firstName, ENT_NOQUOTES) .'</firstName>'; // mandatory, 0-40 chars
        $b .= '    <lastName>'. htmlspecialchars($obj_XML->lastName, ENT_NOQUOTES) .'</lastName>'; // mandatory, 0-40 chars
        $b .= '    <street>'. htmlspecialchars($obj_XML->street, ENT_NOQUOTES) .'</street>'; // mandatory, 0-100 chars
        $b .= '    <postalCode>'. intval($obj_XML->postalCode) .'</postalCode>'; // optional, 0-20 chars
        $b .= '    <city>'. htmlspecialchars($obj_XML->city, ENT_NOQUOTES) .'</city>'; // mandatory, 0-50 chars
        $b .= '    <countryCode>'. htmlspecialchars($obj_XML->countryCode, ENT_NOQUOTES) .'</countryCode>'; // mandatory, 2-2 chars
        $b .= '    <telephoneNumber>'. intval($obj_XML->mobilNumber) .'</telephoneNumber>'; // optional
        $b .= '   </address>';
        $b .= '  </shippingAddress>';
        $b .= ' </order>';
        $b .= ' <returnURL>'. "http://". $_SERVER['HTTP_HOST'] ."/pay/accept.php?mpoint-id=". $this->getTxnInfo()->getID() .'</returnURL>';
        $b .= '</submit>';
        
        //TODO: send the request and follow the description in JIRA        
        $obj_XML = simplexml_load_string($b);
        $obj_Std = $obj_SOAP->InitializePayment($obj_XML);
        $cpg_XML = simplexml_load_string($obj_Std->InitializePaymentResult);
        
		return $cpg_XML;
	}
	
}
?>