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

	public function initialize(HTTPConnInfo &$oCI, $currency, $shortCode, $description, $shippingInfo)
	{
        
        $orderContent = NULL;
        $shopperIPAddress = NULL;
        $authenticatedShopperID = NULL;
        $orderCode = NULL;
        $exponent = 2;
        $debitCardIndication = 'credit';
        $cardId = 8;
        $oldOrder = NULL;
        $description = "mPoint ID: ". $this->getTxnInfo()->getID() ." for Order No.:". $this->getTxnInfo()->getOrderID();
        $creditCardInfoAvailable = FALSE;
        $billingAddressAvailable = FALSE;
        $nominalAuth = NO;
        
		$b = '<?xml version="1.0" encoding="UTF-8"?>';
        $b.= '<submit>';
        $b.= ' <shortCode>'. $shortCode .'</shortCode>'; // Short code of the Storefront application 
        $b.= ' <order orderCode="'. $orderCode .'">'; // mandatory, needs to be unique
        if (strlen($oldOrder) > 0)
        {
            $b.= '  <oldOrder></oldOrder>'; // Optional
        }
        if ($nominalAuth === YES)
        {
            $b.= '  <nominalAuth>Y</nominalAuth>'; // Optional, only storefronts that can accept nominalAuth need to send this element    
        }
        $b.= '  <description>'. htmlspecialchars($description, ENT_NOQUOTES) .'</description>'; // Mandatory, maxlenght=50, simple one line description of the order
        $b.= '  <amount value="'. $this->getTxnInfo()->getAmount() .'" curencyCode="'. $currency .'" exponent="'. $exponent .'" debitCardIndication="'. $debitCardIndication .'"/>'; // Mandatory, decimal based on exponent, code uppercase ISO4217, indicator should be always 'credit'
        $b.= '  <orderContent>';
        $b.= '   <![CDATA]['. htmlspecialchars($orderContent, ENT_NOQUOTES) .']]>'; // don't use <html><body> tags
        $b.= '  </orderContent>';
        if ($billingAddressAvailable === TRUE)
        {
            $b.= '  <paymentDetails>';
            if ($creditCardInfoAvailable)
            {
                // TODO: get the card info somehow...
                $b.= '   <'. $this->getCardName($cardId) .'>';
                $b.= '    <cardNumber>4444333322221111</cardNumber>'; // mandatory, 0-20
                $b.= '    <expiryDate>';
                $b.= '     <date month="09" year="2003" />'; // mandatory
                $b.= '    </expiryDate>';
                $b.= '    <cardHolderName>J. Doe</cardHolderName>'; // mandatory
            }
            $b.= '    <cardAddress>';
            $b.= '     <address>';
            // TODO: split the name
            $b.= '      <firstName>'. htmlspecialchars($this->getTxnInfo()->getClientConfig()->getName(), ENT_NOQUOTES) .'</firstName>'; // mandatory, 0-40 chars
            $b.= '      <lastName>'. htmlspecialchars($this->getTxnInfo()->getClientConfig()->getName(), ENT_NOQUOTES) .'</lastName>'; // mandatory, 0-40 chars
            // TODO: fill out the address
            $b.= '      <street>11 Hereortherestreet</street>'; // mandatory, 0-100 chars
            $b.= '      <postalCode>1234KL</postalCode>'; // optional, 0-20 chars
            $b.= '      <city>Somewhereorother</city>'; // mandatory, 0-50 chars
            $b.= '      <countryCode>TP</countryCode>'; // mandatory, 2-2 chars
            $b.= '      <telephoneNumber>0123456789</telephoneNumber>'; // optional
            $b.= '     </address>';
            $b.= '    </cardAddress>';
            if ($creditCardInfoAvailable)
            {
                $b.= '   </.'. $this->getCardName($cardId) .'>';
            }
            $b.= '  </paymentDetails>';
        }
        $b.= '  <shopper>';
        $b.= '   <shopperIPAddress>'.  .'</shopperIPAddress>'; // mandatory
        $b.= '   <shopperEmailAddress>'. htmlspecialchars($this->getTxnInfo()->getEMail(), ENT_NOQUOTES) .'</shopperEmailAddress>'; // optional, 0-50 chars
        // TODO: shopper id
        $b.= '   <authenticatedShopperID>1234567890</authenticatedShopperID>'; // optional, applicable to BIBIT, 0-20 chars
        $b.= '  </shopper>';
        // TODO: fill out the address
        $b.= '  <shippingAddress>';
        $b.= '   <address>';
        $b.= '    <firstName>Joh</firstName>'; // mandatory, 0-40 chars
        $b.= '    <lastName>Doe</lastName>'; // mandatory, 0-40 chars
        $b.= '    <street>11 Hereortherestreet</street>'; // mandatory, 0-100 chars
        $b.= '    <postalCode>1234KL</postalCode>'; // optional, 0-20 chars
        $b.= '    <city>Somewhereorother</city>'; // mandatory, 0-50 chars
        $b.= '    <countryCode>TP</countryCode>'; // mandatory, 2-2 chars
        $b.= '    <telephoneNumber>0123456789</telephoneNumber>'; // optional
        $b.= '   </address>';
        $b.= '  </shippingAddress>';
        $b.= ' </order>';
        // TODO: check the url
        $b.= ' <returnURL>'. "http://". $_SERVER['HTTP_HOST'] ."/pay/accept.php?mpoint-id=". $this->getTxnInfo()->getID() .'</returnURL>';
//        $b.= ' <teaLeafGuid>452ef50f-68ef-4677-9b19-d7140c444d19</teaLeafGuid>'; // ???
        $b.= '</submit>';
        
        //TODO: send the request and follow the description in JIRA
        
		return $obj_XML;
	}
	
}
?>