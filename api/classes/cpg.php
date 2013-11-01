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
		$clientVars = $this->getMessageData($this->getTxnInfo()->getID(), Constants::iCLIENT_VARS_STATE);
		
		$b = '<?xml version="1.0" encoding="UTF-8"?>';
        $b .= '<submit>';
        $b .= ' <shortCode>'. htmlspecialchars( $this->getTxnInfo()->getClientConfig()->getAccountConfig()->getName(),ENT_NOQUOTES ) .'</shortCode>'; // Short code of the Storefront application 
        $b .= ' <order orderCode="'. htmlspecialchars( $this->getTxnInfo()->getOrderID(),ENT_NOQUOTES  ) .'">'; // mandatory, needs to be unique
        $b .= '  <description>'. htmlspecialchars("mPoint ID: ". $this->getTxnInfo()->getID() ." for Order No.:". $this->getTxnInfo()->getOrderID() , ENT_NOQUOTES) .'</description>';        
        $b .= '  <amount value="'. htmlspecialchars($this->getTxnInfo()->getAmount(),ENT_NOQUOTES ) .'" curencyCode="'. htmlspecialchars($this->getCurrency($this->getTxnInfo()->getClientConfig()->getCountryConfig()->getID(), Constants::iCPM_PSP),ENT_NOQUOTES ) .'" exponent="2" debitCardIndication="credit"/>'; 
		if  (array_key_exists("var_tax", $aClientVars) === true)
		{
			$b .= '	   <tax "'. $clientVars["var_tax"] .'"/>';
		}					
        $b .= '  <orderContent>';
        $b .= '   <![CDATA]['. htmlspecialchars( $this->getTxnInfo()->getDescription(), ENT_NOQUOTES) .']]>'; // don't use <html><body> tags
        $b .= '  </orderContent>';
        // This needs to be added later as we dont have the billing address for now 
        $b .= '  <paymentDetails>';
        $b .= '   <'. $this->getCardName($obj_XML->card->{'type-id'}) .'>';
       	$b .= '    <CCRKey>'.  htmlspecialchars( $obj_XML->ticket,ENT_NOQUOTES )  .'</CCRKey>'; // mandatory, 0-20
        //TODO should be card number not masked card number
        $b .= '    <cardNumber> '. $obj_XML->{'card-number-mask'} .' </cardNumber>'; // mandatory, 0-20
        $b .= '    <cvc> '. intval($obj_XML->cvc) .' </cvc>';    
        $b .= '    <expiryDate>';
        $b .= '     <date month="'. substr($obj_XML->expiry,0,2) .'" year="'. substr($obj_XML->expiry, -2) .'" />'; // mandatory
        $b .= '    </expiryDate>';
        $b .= '    <cardHolderName>'. htmlspecialchars($obj_XML->address->{'card-holder-name'}, ENT_NOQUOTES) .'</cardHolderName>'; // mandatory
		if (array_key_exists("var_fiscal-number", $aClientVars) === true)
		{
			$b .= '	   <fiscalNumber>"'. $clientVars["var_fiscal-number"] .'"</fiscalNumber>';
		}
		if (array_key_exists("var_payment-country-code", $aClientVars) === true)
		{
			$b .= '	   <paymentCountryCode>"'. $clientVars["var_payment-country-code"] .'"</paymentCountryCode>';
		}
		if (array_key_exists("var_number-of-instalments", $aClientVars) === true)
		{
			$b .= '	   <pnumberofinstalments>"'. $clientVars["var_number-of-instalments"] .'"</numberofinstalments>';
		}			    
        $b .= '    <cardAddress>';
        $b .= '     <address>';
        $b .= '      <firstName>'. htmlspecialchars($obj_XML->address->{'first-name'}, ENT_NOQUOTES) .'</firstName>'; // mandatory, 0-40 chars
        $b .= '      <lastName>'. htmlspecialchars($obj_XML->address->{'last-name'}, ENT_NOQUOTES) .'</lastName>'; // mandatory, 0-40 chars
        $b .= '      <street>'. htmlspecialchars($obj_XML->address->street, ENT_NOQUOTES) .'</street>'; // mandatory, 0-100 chars
        $b .= '      <postalCode>'. intval($obj_XML->address->{'postal-code'}) .'</postalCode>'; // optional, 0-20 chars
        $b .= '      <city>'. htmlspecialchars($obj_XML->address->city, ENT_NOQUOTES) .'</city>'; // mandatory, 0-50 chars
        $b .= '      <countryCode>'. intval($obj_XML->address['country-id'], ENT_NOQUOTES) .'</countryCode>'; // mandatory, 2-2 chars
        $b .= '      <telephoneNumber>'. floatval($this->getTxnInfo()->getMobile() ) .'</telephoneNumber>'; // optional
        $b .= '     </address>';
        $b .= '    </cardAddress>';
        $b .= '   </' .$this->getCardName($obj_XML->card->{'type-id'}) .'>';
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
        $b .= '      <firstName>'. htmlspecialchars($obj_XML->address->{'first-name'}, ENT_NOQUOTES) .'</firstName>'; // mandatory, 0-40 chars
        $b .= '      <lastName>'. htmlspecialchars($obj_XML->address->{'last-name'}, ENT_NOQUOTES) .'</lastName>'; // mandatory, 0-40 chars
        $b .= '      <street>'. htmlspecialchars($obj_XML->address->street, ENT_NOQUOTES) .'</street>'; // mandatory, 0-100 chars
        $b .= '      <postalCode>'. intval($obj_XML->address->{'postal-code'}) .'</postalCode>'; // optional, 0-20 chars
        $b .= '      <city>'. htmlspecialchars($obj_XML->address->city, ENT_NOQUOTES) .'</city>'; // mandatory, 0-50 chars
        $b .= '      <countryCode>'. intval($obj_XML->address['country-id'], ENT_NOQUOTES) .'</countryCode>'; // mandatory, 2-2 chars
        $b .= '      <telephoneNumber>'. floatval($this->getTxnInfo()->getMobile() ) .'</telephoneNumber>'; // optional
        $b .= '   </address>';
        $b .= '  </shippingAddress>';
        if (array_key_exists("var_enhanced-data", $aClientVars) === true)
        {
        	$b .= '	   <enchancedData>"'. $clientVars["var_enhanced-data"] .'"</enchancedData>';
        }
        $b .= ' </order>';
        $b .= ' <returnURL>'. "http://". $_SERVER['HTTP_HOST'] ."/pay/accept.php?mpoint-id=". $this->getTxnInfo()->getID() .'</returnURL>';
        $b .= '</submit>';
        
        //TODO: send the request and follow the description in JIRA        
        $obj_XML = simplexml_load_string($b);
        $obj_Std = $obj_SOAP->InitializePayment($obj_XML);
        $cpg_XML = simplexml_load_string($obj_Std->InitializePaymentResult);
        
        if (empty($cpg_XML->redirect)=== false)
        {
        	$xml .= '<status code="100">';
        	$xml .= '<url>'. $cpg_XML->redirect .'</url>';
        	$xml .= '</status>';
        }
        elseif (empty($cpg_XML->error)===false)
        {
        	$xml .= $cpg_XML->error;
        }
        else 
        {
        	$xml .= '<error>Unknown Error</error>';
        }
        
		return $xml;
	}		
}
?>