<?php

  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  
  require_once("conf/config.php");
  require_once("api/classes/api.php");
  require_once("api/classes/parser.php");
  require_once 'Crypt/GPG.php';

  // Decrypt file and store it into root directory 
  $file_name = 'decrypt.csv';
  $gpg = new Crypt_GPG(); 
  $privateKeyData = file_get_contents('resource/private.asc');
  $result = $gpg->importKey($privateKeyData);
  $gpg->addDecryptKey($result['fingerprint'],'Cell123!');
  $decryptedData = $gpg->decrypt(file_get_contents('encrypt.txt'));
  file_put_contents($file_name, $decryptedData);
  
  // Create Account (if not exist) and store card against account
  $obj = new api(sMPOINT_URL);
  $objParser = new parser($file_name);
  $rows = $objParser->parse();
  $objParser->removeProcessedCards($rows);
  
  if(empty($rows)===false) 
  {
	  $counter = 0;
	  foreach ($rows as $row){
	      $obj->createRequest($row);
	      $response = $obj->call();
	      //convert into json
	      $json  = json_encode($response->status);
	      //convert into associative array
	      $xmlArr = json_decode($json, true);
	      
	      // Write log
	      $log = array();
	      $log[] = $row->CustomerProfileID;
	      $log[] = $row->CustomerPaymentProfileID;
	      $log[] = $row->CustomerID;
	      $log[] = isset($xmlArr[0])?$xmlArr[0]:'null';
	      $log[] = isset($xmlArr['@attributes']['eua-id'])?$xmlArr['@attributes']['eua-id']:'null';
	      $log[] = isset($xmlArr['@attributes']['code'])?$xmlArr['@attributes']['code']:'null';
	      
	      $file = fopen("log/log.csv","a");
	      fputcsv($file,$log);
	      
	      $counter += 1;
	  }
	  fclose($file);
	  
	  echo "Total Number of Save Card = $counter";
  } 
  else 
  {
  	echo "No data found";
  }
  
?>