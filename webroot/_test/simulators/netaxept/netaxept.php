<?php

require_once(dirname(__FILE__). '/../../../inc/include.php');

require_once(sAPI_CLASS_PATH ."/simpledom.php");

class NetaxeptSimulator
{
	public function process()
	{
		header("Content-Type: text/xml; charset=UTF-8");

		$response = '<?xml version="1.0" encoding="utf-8"?>';
		$response .= '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
					<soap:Body>
						<ProcessResponse xmlns="http://BBS.EPayment">
							<ProcessResult xmlns:a="http://schemas.datacontract.org/2004/07/BBS.EPayment.ServiceLibrary">
								<a:ResponseCode>OK</a:ResponseCode>
							</ProcessResult>
						</ProcessResponse>
					</soap:Body>
				</soap:Envelope>';

		echo $response;
	}

	public function query()
	{
		header("Content-Type: text/xml; charset=UTF-8");

		$response = '<?xml version="1.0" encoding="utf-8"?>';
		$response .= '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
					<soap:Body>
						<QueryResponse xmlns="http://BBS.EPayment">
							<QueryResult i:type="a:PaymentInfo" xmlns:a="http://schemas.datacontract.org/2004/07/BBS.EPayment.ServiceLibrary" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
							    <a:MerchantId>12001561</a:MerchantId>
            					<a:QueryFinished>2015-04-24T11:28:07.6948093+02:00</a:QueryFinished>
            					<a:TransactionId>9430db88c00b476b969316c90faf00fb</a:TransactionId>
								<a:Summary>
								   <a:AmountCaptured>5147</a:AmountCaptured>
								   <a:AmountCredited>0</a:AmountCredited>
								   <a:Annulled>false</a:Annulled>
								   <a:AuthorizationId>232376</a:AuthorizationId>
								   <a:Authorized>true</a:Authorized>
					            </a:Summary>
							</QueryResult>
						</QueryResponse>
					</soap:Body>
				</soap:Envelope>';

		echo $response;
	}

	public function unknown($action)
	{
		header("HTTP/1.0 400 Bad Request");
		trigger_error("Netaxept Simulator doesn't recognize soap action: ". $action);
	}


}


if (isset($_GET['wsdl']) === true)
{
	//$netaxept_url = isset($_SERVER["HTTPS"]) ? "https" : "http" ."://". $_SERVER["HTTP_HOST"]. strtok($_SERVER["REQUEST_URI"],'?');
	header("Content-Type: text/xml; charset=UTF-8");
	readfile(dirname(__FILE__). '/netaxept.wsdl');
}
else
{
	$aHeaders = getallheaders();
	$simulator = new NetaxeptSimulator();
	$soapAction = @$aHeaders["SOAPAction"];
	switch ($soapAction)
	{
		case '"http://BBS.EPayment/INetaxept/Process"':
			$simulator->process();
			break;
		case '"http://BBS.EPayment/INetaxept/Query"';
			$simulator->query();
			break;
		default:
			$simulator->unknown($soapAction);
	}

}
