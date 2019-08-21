<?php

class parser {
    
    private $data;
    private $file;
    private $fieldNames;
    
    function __construct($file_name = null) {
        $this->data = null;
        $this->file = $file_name;
        $this->fieldNames = array('MerchantID', 'CustomerProfileID', 'CustomerPaymentProfileID', 'CustomerID', 'Description, Email', 'CardNumber', 'CardExpirationDate', 'CardType', 'BankAccountNumber', 'BankRoutingNumber', 'NameOnAccount', 'BankAccountType', 'ECheckRecordTypeID', 'BankName', 'Company', 'FirstName', 'LastName', 'Address', 'City', 'StateProv', 'Zip', 'Country', 'Phone', 'Fax');
    }
    
    function parse(){
        if(!empty($this->file)){
            //Open the file.
            $fileHandle = fopen($this->file, "r");
            $headers = array();
            //Loop through the CSV rows.
            while (($row = fgetcsv($fileHandle, 0, ",")) !== FALSE) { 
            	if(empty(array_filter($row))===false) {
            	
	            	$dataObj = new stdClass();
	            	
	                if(empty($headers)===true) {
	                	$headers = $row;
	                	continue;
	                }
	                
	                for($i=0;$i<count($row);$i++){
	                    $dataObj->{trim($headers[$i])} = isset($row[$i])?$row[$i]:'';
	                }
	              
	                //Generate/Create custom fields
	                $isExpired = $this->validateCardExpiry($dataObj->CardExpirationDate);
	                if($isExpired === false){
	                	$dataObj->ExpiryMonth = substr($dataObj->CardExpirationDate,5,2);
	                	$dataObj->ExpiryYear = strstr($dataObj->CardExpirationDate, '-', true);
		                $dataObj->CardType = $this->getCreditCardType($dataObj->CardNumber);
		                $dataObj->cardHolderName = trim($dataObj->FirstName." ".$dataObj->LastName);
		                if(empty($dataObj->cardHolderName) === true) {
		                	$dataObj->cardHolderName = preg_replace('/[^A-Za-z]/', '', strstr($dataObj->Email, '@', true));
		                }
		                $this->data[] = $dataObj;
	                } else {
	                
	                  	// Write log
                      	$log = array();
                      	$log[] = $dataObj->CustomerProfileID;
                      	$log[] = $dataObj->CustomerID;
                      	$log[] = $dataObj->CardExpirationDate;
                      	$log[] = 'Card has been expired';
	                	
	                	$file = fopen("log/log.csv","a");
                      	fputcsv($file,$log);
                      	fclose($file);
	                }
	                	
                }
            }
            
            // Close the file
            fclose($fileHandle);
            return $this->data;
        }
        return false;
    }
    
    /**
     * Get card type based on given card number
     * @param integer $num credit card number
     * @return string Credit card type
     */
    function getCreditCardType($num)
    {
        if (empty($num)) {
            return false;
        }
        
        $matchingPatterns = [
            'visa' => '/^4[0-9]{12}(?:[0-9]{3})?$/',
            'mastercard' => '/^5[1-5][0-9]{14}$/',
            'amex' => '/^3[47][0-9]{13}$/',
            'diners' => '/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/',
            'discover' => '/^6(?:011|5[0-9]{2})[0-9]{12}$/',
            'jcb' => '/^(?:2131|1800|35\d{3})\d{11}$/',
            'any' => '/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6(?:011|5[0-9][0-9])[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|(?:2131|1800|35\d{3})\d{11})$/'
        ];
        
        foreach ($matchingPatterns as $key=>$pattern) {
            if (preg_match($pattern, $num)) {
                return $key;
            }
        }
        return false;
    }
    
    private function validateCardExpiry($cardExpirationDate)
    {
    	if(strtotime("{$cardExpirationDate}") < strtotime( date("Y-m")  ))
		{
		  return true;
		}
		return false;
    }
    
    /**
     * Get list of processed customer id
     * @return (array) customer id
     */
    private function getProcessedCustomerID()
    {
        $processedCustomerID = array();
    	$filename = 'log/log.csv';
    	if (file_exists($filename)) {
		    $fileHandle = fopen($filename, "r");
            $headers = array();
            //Loop through the CSV rows.
            while (($row = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
               $processedCustomerID[] =  array('CustomerProfileID' => $row[0], 'CustomerPaymentProfileID' => $row[1], 'CustomerID' => $row[2]);
            }
            return $processedCustomerID;
		}
    }
    
    /**
     * Remove processed card from the list of cards to be processed.
     * @param (array) list of cards
     */
    function removeProcessedCards(&$cards = NULL){
    	$processedCards = $this->getProcessedCustomerID();
    	if(empty($cards) === false && empty($processedCards)===false){
    		foreach($cards as $k => $card){
    			foreach($processedCards as $key => $value){
    			    $card = (array)$card;
    				$result = array_intersect($card, $value);
					if(count($result)==3){
						unset($processedCards[$key]);
						unset($cards[$k]);
						break;
					}
    			}
    			
    			if(empty($processedCards)=== true)
    				break;
    		}
    	}
    }
    
    
    
}

?>