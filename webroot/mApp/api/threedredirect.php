<?php
/**
 * This files contains the for the Callback component which handles transactions processed through mPoints general PSP.
 * The file will update the Transaction status and add the following data fields:
 * 	- PSP Transaction ID
 * 	- ID of the card used for payment.
 * 
 *
 * Additionally the component sends out SMS Receipts and performs a callback to the client.
 */

/**
 * Input XML format
 *
<?xml version="1.0" encoding="UTF-8"?>
<root>
	<redirect>
		<transaction id="1825317">
		</transaction>
		<status code="2000"></status>
	</redirect>
</root>
 */

// Require Global Include File
require_once("../../inc/include.php");


// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

$i = 0;
while ( ($_OBJ_DB instanceof RDB) === false && $i < 5)
{
	// Instantiate connection to the Database
	$_OBJ_DB = RDB::produceDatabase($aDB_CONN_INFO["mpoint"]);
	$i++;
}
$obj_XML = simplexml_load_string(file_get_contents("php://input") );

$id = (integer)$obj_XML->redirect->transaction["id"];
$statuscode = (integer)$obj_XML->redirect->status["code"];
$xml = '';

try
{
	 $obj_TxnInfo = TxnInfo::produceInfo($id, $_OBJ_DB);
	 $obj_ClientConfig = $obj_TxnInfo->getClientConfig();
	 
	 $b = '<?xml version="1.0" encoding="UTF-8"?>';
	 $b .= '<root>';
	 $b .= $obj_TxnInfo->toXML();
	 $b .= $obj_ClientConfig->toXML(Constants::iPublicProperty);
	 $b .= '<status code="'.$statuscode.'"></status>';
	 $b .= '</root>';
	 
	 $aURLInfo = parse_url($obj_ClientConfig->getThreedRedirectURL());
	 $obj_ConnInfo =  new HTTPConnInfo( $aURLInfo["scheme"], $aURLInfo["host"], $aURLInfo["port"], 120, $aURLInfo["path"],"POST","text/xml", $obj_ClientConfig->getUsername(), $obj_ClientConfig->getPassword() );
	 
	 $obj_HTTP = new HTTPClient ( new Template (), $obj_ConnInfo );
	 $obj_HTTP->connect ();
	 
	 	/* ----- Construct HTTP Header Start ----- */
	 	$h = "{METHOD} {PATH} HTTP/1.0" .HTTPClient::CRLF;
	 	$h .= "host: {HOST}" .HTTPClient::CRLF;
	 	$h .= "referer: {REFERER}" .HTTPClient::CRLF;
	 	$h .= "content-length: {CONTENTLENGTH}" .HTTPClient::CRLF;
	 	$h .= "content-type: {CONTENTTYPE}; charset=\"UTF-8\"" .HTTPClient::CRLF;
	 	$h .= "user-agent: mpoint" .HTTPClient::CRLF;
	 	/* ----- Construct HTTP Header End ----- */
	 	
	 $code = $obj_HTTP->send ($h, $b );
	 $obj_HTTP->disconnect ();
//	 $obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() ); 

         echo '<?xml version="1.0" encoding="UTF-8"?>';
	 echo '<root>';
	 echo '<redirect-url>';
	 echo htmlspecialchars($obj_HTTP->getReplyBody()) ;
	 echo '</redirect-url>';
	 echo '</root>';
}
catch(mPointException $e){
	header("HTTP/1.1 500 Internal Server Error");
	$xml .= '<status code="'. $e->getCode() .'">'. htmlspecialchars($e->getMessage(), ENT_NOQUOTES). '</status>';
	trigger_error($e->getMessage() ."\n". $HTTP_RAW_POST_DATA, E_USER_WARNING);
}


