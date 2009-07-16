<?php
/**
 * The Billing package provides features for charging the customer through alternatives to Credit Card such as Premium SMS and WAP Billing.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package TopUp
 * @version 1.00
 */

/**
 * Model Class containing all the Business Logic for handling Top-Up of an End-User's prepaid account
 *
 */
class TopUp extends Home
{	
	/**
	 * Fetches the available Deposit Options for the Country that may be into an End-User's prepaid account.
	 * The Deposit Options are returned as an XML document in the following format:
	 * 	<deposits>
	 * 		<option id="{UNIQUE ID OF THE DEPOSIT OPTION}">
	 * 			<amount currency="{CURRENCY AMOUNT IS CHARGED IN}">{AMOUNT THE CUSTOMER IS CHARGED FOR THE TOP-UP}</amount>
	 * 			<price>{AMOUNT FORMATTED FOR BEING DISPLAYED IN THE GIVEN COUNTRY}</price>
	 * 		</option>
	 * 		<option id="{UNIQUE ID OF THE DEPOSIT OPTION}">
	 * 			<amount currency="{CURRENCY AMOUNT IS CHARGED IN}">{AMOUNT THE CUSTOMER IS CHARGED FOR THE TOP-UP}</amount>
	 * 			<price>{AMOUNT FORMATTED FOR BEING DISPLAYED IN THE GIVEN COUNTRY}</price>
	 * 		</option>
	 * 		...
	 * 	</deposits>
	 *
	 * @return 	xml
	 */
	public function getDepositOptions()
	{
		$sql = "SELECT id, amount
				FROM System.DepositOption_Tbl
				WHERE countryid = ". $this->getCountryConfig()->getID() ." AND enabled = true";
//		echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);
		
		$xml = '<deposits>';
		while ($RS = $this->getDBConn()->fetchName($res) )
		{
			$xml .= '<option id="'. $RS["ID"] .'">';
			$xml .= '<amount currency="'. $this->getCountryConfig()->getCurrency() .'">'. $RS["AMOUNT"] .'</amount>';
			$xml .= '<price>'. General::formatAmount($this->getCountryConfig(), $RS["AMOUNT"]) .'</price>';
			$xml .= '</option>';
		}
		$xml .= '</deposits>';

		return $xml;
	}
}
?>