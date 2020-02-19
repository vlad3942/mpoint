<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$msg = null;
if(isset($_FILES['fileupload'])){
  $errors = $_FILES['fileupload']['error'];
  $file_name = $_FILES['fileupload']['name'];
  $file_size = $_FILES['fileupload']['size'];
  $file_tmp = $_FILES['fileupload']['tmp_name'];
  $file_type = $_FILES['fileupload']['type'];
  $file_ext=explode('.',$file_name);
  $file_ext=strtolower(end($file_ext));
  
  if(empty($errors)==true) {
      
      move_uploaded_file($file_tmp,getcwd().'/'.$file_name);
      
      if(!empty($_POST['submit'])){
          switch ($_POST['submit']){
              case 'Push Into DB':
                  require_once("conf/config.php");
                  require_once("api/classes/api.php");
                  require_once("api/classes/parser.php");
                  $obj = new api(sMPOINT_URL);
                  $objParser = new parser($file_name);
                  $rows = $objParser->parse();
                  $objParser->removeProcessedCards($rows);
                  
                  if(empty($rows)===false) 
                  {
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
	                  }
	                  fclose($file);
	              }
	              else 
	              {
	              	$msg = 'No data found.';
	              }
                  break;
              case 'Encrypt': 
                  header("Content-type: text/plain");
                  header("Content-Disposition: attachment; filename=encrypt.txt");
                  
                  require_once 'Crypt/GPG.php';
                  $gpg = new Crypt_GPG();
                  $publicKeyData = file_get_contents('resource/public.asc');
                  $result = $gpg->importKey($publicKeyData);
                  $gpg->addEncryptKey($result['fingerprint']);
                  $encryptedData = $gpg->encrypt(file_get_contents($file_name));
                  echo $encryptedData;
                  exit();
                  break;
              case 'Decrypt': 
                  header("Content-type: text/plain");
                  header("Content-Disposition: attachment; filename=decrypt.txt");
                  
                  require_once 'Crypt/GPG.php';
                  $gpg = new Crypt_GPG();
                  $privateKeyData = file_get_contents('resource/private.asc');
                  $result = $gpg->importKey($privateKeyData);
                  $gpg->addDecryptKey($result['fingerprint'],'Cell123!');
                  echo $decryptedData = $gpg->decrypt(file_get_contents($file_name));
                  exit();
                  break;
          }
      }
  }else{
      $msg = 'Please Upload Valid File.';
  }
} 
// Load View
include_once ('views/default.php');
?>