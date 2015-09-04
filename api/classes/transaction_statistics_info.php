<?php
/**
 * 
 *
 * @author Abhishek Sawant
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package mConsole
 * @subpackage Statistics
 * @version 1.00
 */

/**
 * Data class for hold all data relevant for a Transaction Statistics Entry
 *
 */
class TransactionStatisticsInfo
{

	private $_aTransactionStats = array();

	public function __construct($aTransactionStats)
	{
		$this->_aTransactionStats = $aTransactionStats;
	}

	public function getTransactionStats() { return $this->_aTransactionStats; }

	public function toXML()
	{
		$xml = '<transaction-statistics>';

		foreach ($this->_aTransactionStats as $createddate => $transactioncounts)
		{
			$xml .= '<transaction-stats-by-day date="'.htmlspecialchars($createddate, ENT_NOQUOTES).'">';
			foreach($transactioncounts as $stateid => $statevalue)
			{
				$xml .= '<state id="'.$stateid.'">'.$statevalue.'</state>';
			}
			$xml .= '</transaction-stats-by-day>';
		}

		$xml .= '</transaction-statistics>';
		return $xml;
	}

}
