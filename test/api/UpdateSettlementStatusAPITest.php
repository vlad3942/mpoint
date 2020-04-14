<?php
/**
 * User: Abhinav Shaha
 * Date: 13-04-20
 * Time: 11:00
 */
require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class UpdateSettlementStatusAPITest extends baseAPITest
{
	/**
	 * @var RDB
	 */
	private $_obj_DB;
	private $_obj_TXT;
	private $_aMPOINT_CONN_INFO;

	public function setUp()
	{
		$this->bIgnoreErrors = true;
		parent::setUp();
		global $aMPOINT_CONN_INFO;

		$this->_obj_DB = RDB::produceDatabase($this->mPointDBInfo);
		$this->_obj_TXT = new TranslateText(array(sLANGUAGE_PATH . sLANG ."/global.txt", sLANGUAGE_PATH . sLANG ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
		$aMPOINT_CONN_INFO['path'] = "/mApp/api/update-settlement-status.php";
		$aMPOINT_CONN_INFO["contenttype"] = "text/xml";
		$this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
		$this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
	}

	protected function getUpdateSettlementStatusDoc($clientId, $settlementId, $status, $description, $record_tracking_number)
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<root>';
		$xml .= '<updateSettlementStatus>';
		$xml .= '<clientId>';
		$xml .= "$clientId";
		$xml .= '</clientId>';
		$xml .= '<settlementFile>';
		$xml .= '<id>';
		$xml .= "$settlementId";
		$xml .= '</id>';
		$xml .= '<status>';
		$xml .= "$status";
		$xml .= '</status>';
		$xml .= '<description>';
		$xml .= "$description";
		$xml .= '</description>';
		$xml .= '<record_tracking_number>';
		$xml .= "$record_tracking_number";
		$xml .= '</record_tracking_number>';
		$xml .= '</settlementFile>';
		$xml .= '</updateSettlementStatus>';
		$xml .= '</root>';

		return $xml;
	}

	public function testSettlementIsAlreadyProcessed($pspID)
    {
		$alreadyProcessedSettlementId = 1;
		$currentProcessingSettlementId = 2;
		$settlementDescription = "File is already processed as part of settlement id $currentProcessingSettlementId";

		$sCurrentProcessingSettlementDate = str_replace('-','',date('Y-m-d'));
		$sAlreadyProcessedSettlementDate = str_replace('-','',date('Y-m-d', strtotime("-1 days")));

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Admin.User_Tbl (id, countryid, firstname, lastname, email, username, passwd) VALUES (1, 100, 'Test', 'TestTest', 'test@cellpointmobile.com', 'test', 'testtest')");
		$this->queryDB("INSERT INTO Admin.Access_Tbl (id, clientid, userid) VALUES (1, 113, 1)");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name, username, passwd) VALUES (1, 113, $pspID, '1', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (113, 17, $pspID)");
		$this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '1513-005', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid) VALUES (1001001, 100, 113, 1100, 100, $pspID, '1515', '1513-005', '', 5000, '127.0.0.1', TRUE, 1, 1)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");

		//Add entry in settlement_tbl with "accpeted" status which indicates settlement is already processed 
		$this->queryDB("INSERT INTO Log.settlement_tbl (id,record_number,file_reference_number,file_sequence_number,client_id,psp_id,record_tracking_number,record_type,status) VALUES ($alreadyProcessedSettlementId,0,'test1654',$sAlreadyProcessedSettlementDate,113,$pspID,123456,'CAPTURE','accepted');");

		//Add entry in settlement_tbl with "waiting" status which indicates settlement is going to process
		$this->queryDB("INSERT INTO Log.settlement_tbl (id,record_number,file_reference_number,file_sequence_number,client_id,psp_id,record_type,status) VALUES ($currentProcessingSettlementId,0,'test1654',$sCurrentProcessingSettlementDate,113,$pspID,'CAPTURE','waiting');");

		$xml = $this->getUpdateSettlementStatusDoc(113, $currentProcessingSettlementId, 'duplicate', $settlementDescription, $sAlreadyProcessedSettlementDate);
		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertEquals("", $sReplyBody);

		$Status = array();
		$res = $this->queryDB("SELECT status FROM Log.settlement_tbl where id = $currentProcessingSettlementId");
		$this->assertTrue(is_resource($res));
		while ($row = pg_fetch_assoc($res))
		{
			$Status[] = $row["status"];
		}
		//Settlement status should be in "duplicate" only for current processing settlement
		$this->assertEquals('duplicate', $Status[0]);
	}

	public function tearDown()
	{
		$this->_obj_DB->disConnect();
		parent::tearDown();
	}

}