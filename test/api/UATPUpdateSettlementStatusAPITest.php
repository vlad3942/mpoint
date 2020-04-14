<?php
/**
 * User: Abhinav
 * Date: 13-04-20
 * Time: 11:00
 */

require_once __DIR__. '/UpdateSettlementStatusAPITest.php';

class UATPUpdateSettlementStatusAPITest extends UpdateSettlementStatusAPITest
{
	public function testSettlementIsAlreadyProcessed()
    {
    	parent::testSettlementIsAlreadyProcessed(Constants::iUATP_CARD_ACCOUNT);
    }
}