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

	public function register()
	{
		$input = file_get_contents('php://input');

		$aMatches = array();
		$bMatches = preg_match('/<ns2:TransactionId>([0-9-]+)<\/ns2:TransactionId>/', $input, $aMatches);

		if ($bMatches)
		{
			$txnid = $aMatches[1];
		}
		else
		{
			throw new InvalidArgumentException("Unable to find transactionId, input: ". $input);
		}

		header("Content-Type: text/xml; charset=UTF-8");

		$response = '<?xml version="1.0" encoding="utf-8"?>';
		$response .= '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
						<s:Body>
							<RegisterResponse xmlns="http://BBS.EPayment">
								<RegisterResult xmlns:a="http://schemas.datacontract.org/2004/07/BBS.EPayment.ServiceLibrary"
												xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
									<a:TransactionId>'. $txnid .'</a:TransactionId>
								</RegisterResult>
							</RegisterResponse>
						</s:Body>
					</s:Envelope>';

		echo $response;
	}

	public function query()
	{
		$config = $this->loadSimulatorConfig();
		$config->ExpiryDate = date("m/y", strtotime('+2 years') );

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
								   <a:AmountCaptured>'. $config->AmountCaptured. '</a:AmountCaptured>
								   <a:AmountCredited>0</a:AmountCredited>
								   <a:Annulled>false</a:Annulled>
								   <a:AuthorizationId>232376</a:AuthorizationId>
								   <a:Authorized>true</a:Authorized>
					            </a:Summary>
					            <a:OrderInformation>
					            	<Amount>'. $config->AmountAuthorized. '</Amount>
					            </a:OrderInformation>
					            <a:CardInformation>
					            	<a:Issuer>'. $config->CardIssuer. '</a:Issuer>
					            	<a:MaskedPAN>4571XXXXXXXX1512</a:MaskedPAN>
					            	<a:ExpiryDate>'. $config->ExpiryDate .'</a:ExpiryDate>
					            </a:CardInformation>';
	if (isset($config->Recurring) === true)
	{
		$response .= 			'<a:Recurring>
									<a:PanHash>'. $config->Recurring->PanHash .'</a:PanHash>
								</a:Recurring>';
	}
		$response .= 		'</QueryResult>
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

	public function error(Exception $exception)
	{
		header("HTTP/1.0 500 Internal Server Error");
		trigger_error("Netaxept Simulator failed with exception: ". $exception->getMessage() ."\n".  $exception->getTraceAsString() );
	}

	private function loadSimulatorConfig()
	{
		$aLogLines = file(sERROR_LOG, FILE_IGNORE_NEW_LINES);

		foreach ($aLogLines as $line)
		{
			$pos = strpos($line, "NETAXEPT SIMULATOR CONFIG :: ");
			if ($pos !== false)
			{
				$sConf = substr($line, $pos+strlen("NETAXEPT SIMULATOR CONFIG :: ") );
				return json_decode(base64_decode($sConf) );
			}
		}

		throw new RuntimeException("Could not load netaxept simulator config - file config mark not found");
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
	try
	{
		$soapAction = @$aHeaders["SOAPAction"];
		switch ($soapAction)
		{
			case '"http://BBS.EPayment/INetaxept/Register"':
				$simulator->register();
				break;
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
	catch (Exception $e)
	{
		$simulator->error($e);
	}
}
