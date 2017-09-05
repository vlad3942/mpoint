UPDATE Client.Client_Tbl SET salt = 'Fh17_8sFgd' WHERE id = 10005;
UPDATE Client.Client_Tbl SET salt = 'Fh17_8sFgd' WHERE id = 10014;
UPDATE Client.Client_Tbl SET salt = '8sFgd_Fh17' WHERE id = 10019;


/**
 * CMP-1146 Support "Payment Settled" state for mConsole Search transaction API
 */ 
INSERT INTO Log.State_Tbl (id, name) VALUES (2020, 'Payment Settled');


/* ========== Global Configuration for MobilePay Online- Payment Method : START========== */
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength) VALUES (30, 'MobilePay Online', 23, -1, -1, -1);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (30, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) VALUES(30, -100);
/* ========== Global Configuration for MobilePay Online- Payment Method : END========== */

/* ========== Global Configuration for MobilePay Online = STARTS ========== */
INSERT INTO System.PSP_Tbl (id, name, system_type) VALUES (33, 'MobilePay Online', 4);
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (100,33,'DKK');

/*MobilePay Online*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (30, 33);

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 33, 'MobilePay Online', '19', '5K4506');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 33, '-1');

-- Route MobilePay Online Card to MobilePay Online with country Denmark
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, countryid) VALUES (10001, 30, 33, true, 100);
/* ========== Global Configuration for MobilePay Online = ENDS ========== */


/* ============= Configure / Update PayFort accounts per environment (UAT/SIT/DEV/POC) =============== */
-- UAT
Update Client.merchantaccount_tbl SET passwd = 'thE78UJRWmnGyxPSQGAT' WHERE username = 'CTjbJcSI' AND pspid = 23;
-- SIT
Update Client.merchantaccount_tbl SET passwd = 'DhZyZO6VP6A1z325jphn' WHERE username = 'CTjbJcSI' AND pspid = 23;
-- DEV PUNE
Update Client.merchantaccount_tbl SET passwd = 'BMMVFHwUGyfjDZk2PzMc' WHERE username = 'CTjbJcSI' AND pspid = 23;
-- POC
Update Client.merchantaccount_tbl SET passwd = 'rYBDTQunZRTgG2cVmMJZ' WHERE username = 'CTjbJcSI' AND pspid = 23;



/* ========== Global Configuration for SADAD - Payment Method : START========== */
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength) VALUES (31, 'SADAD', 23, -1, -1, -1);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (31, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 31, id FROM System.PricePoint_Tbl WHERE amount = -1 AND countryid = 608;

INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (31, 23);


/**
 * CMP-1276 Support "Payment Settled" state for mConsole Search transaction API
 */ 
INSERT INTO Log.State_Tbl (id, name) VALUES (1998, 'Account Validated');
INSERT INTO Log.State_Tbl (id, name) VALUES (19980, 'Account Validated and Cancelled');
INSERT INTO Log.State_Tbl (id, name) VALUES (1997, 'Account Validation Failed');


/* ========== Global Configuration for 2C2P = STARTS ========== */
INSERT INTO System.PSP_Tbl (id, name) VALUES (26, '2C2P');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (644,1,'THB');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (644,26,'THB');

/*Amex*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (1, 26);
/*MasterCard*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (7, 26);
/*VISA*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (8, 26);

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 26, 'gXPRPPam3j58', '764764000000278', 'CPMDemo');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 26, '-1');

-- Route VISA Card to 2C2P with country Thailand
UPDATE Client.CardAccess_Tbl SET pspid = 26, countryid = 644 WHERE clientid = 10001 AND cardid = 8;
/* ========== Global Configuration for 2C2P = ENDS ========== */


/*Myanmar*/
INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (652, 'Myanmar', 'MMK', '1000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-652, 652, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -652, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (652, 9, 'MMK');



/* ========== Global Configuration for MayBank = STARTS ========== */
INSERT INTO System.PSP_Tbl (id, name) VALUES (27, 'MayBank');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (638,1,'MYR');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (638,27,'MYR');

/*Amex*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (1, 27);
/*MasterCard*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (7, 27);
/*VISA*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (8, 27);

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 27, 'MayBank', '02700770202075001284', '4GkR2Hkk');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 27, '-1');

-- Route VISA Card to MayBank with country Malaysia
UPDATE Client.CardAccess_Tbl SET pspid = 27, countryid = 638 WHERE clientid = 10001 AND cardid = 8;
-- Route Master Card to MayBank with country Malaysia
UPDATE Client.CardAccess_Tbl SET pspid = 27, countryid = 638 WHERE clientid = 10001 AND cardid = 7;
-- Route AMEX Card to MayBank with country Malaysia
UPDATE Client.CardAccess_Tbl SET pspid = 27, countryid = 638 WHERE clientid = 10001 AND cardid = 1;

/* ========== Global Configuration for MayBank = ENDS ========== */
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (652, 9,'MMK');


/* ========== Global Configuration for Publicbank = STARTS ========== */
INSERT INTO System.PSP_Tbl (id, name) VALUES (28, 'PublicBank');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (638,28,'MYR');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (614,28,'HKD');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (648,28,'SGD');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (500,28,'AUD');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (500,28,'AUD');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (507,28,'AUD');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (508,28,'AUD');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (510,28,'AUD');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (511,28,'AUD');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (630,28,'AUD');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (634,28,'LKR');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (609,28,'CNY');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (644,28,'THB');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (646,28,'TWD');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (608,28,'SAR');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (505,28,'IDR');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (200,28,'USD');

/*Amex*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (1, 28);
/*MasterCard*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (7, 28);
/*VISA*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (8, 28);

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 28, 'PUBLICBANK', 'sandbox', 'APPLE001');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 28, '-1');

-- Route VISA Card to Publicbank with country Thailand
UPDATE Client.CardAccess_Tbl SET pspid = 28, countryid = 638 WHERE clientid = 10001 AND cardid = 1;
-- Route VISA Card to Publicbank with country Thailand
UPDATE Client.CardAccess_Tbl SET pspid = 28, countryid = 638 WHERE clientid = 10001 AND cardid = 7;
-- Route VISA Card to Publicbank with country Thailand
UPDATE Client.CardAccess_Tbl SET pspid = 28, countryid = 638 WHERE clientid = 10001 AND cardid = 8;

/* ========== Global Configuration for Publicbank = ENDS ========== */

/* ========== Global Configuration for AliPay - Payment Method : START========== */
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength) VALUES (32, 'AliPay', 23, -1, -1, -1);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (32, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 32, id FROM System.PricePoint_Tbl WHERE amount = -1 AND countryid = 200;
/* ========== Global Configuration for AliPay - Payment Method : END========== */

/* ========== Global Configuration for AliPay = STARTS ========== */
INSERT INTO System.PSP_Tbl (id, name) VALUES (30, 'AliPay');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (200,30,'USD');

/*AliPay*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (32, 30);

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 30, 'AliPay', '2088101122136241', '760bdzec6y9goq7ctyx96ezkz78287de');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 30, '-1');

-- Route AliPay Card to AliPay with country USA
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, countryid) VALUES (10001, 32, 30, true, 200);
/* ========== Global Configuration for AliPay = ENDS ========== */

/* ========== Global Configuration for POLi - Payment Method : START========== */
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength) VALUES (34, 'POLi', 23, -1, -1, -1);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (34, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) VALUES (34, -500);
/* ========== Global Configuration for POLi - Payment Method : END========== */
/* ========== Global Configuration for POLi = STARTS ========== */
INSERT INTO System.PSP_Tbl (id, name) VALUES (32, 'POLi');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (500,32,'AUD');

/*POLi*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (34, 32);

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 32, 'POLi', '6101816', 'MdXqHAM!Y2EWQvVC4WsT');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 32, '-1');

-- Route POLi Card to POLi with country USA
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, countryid) VALUES (10001, 34, 32, true, 500);
/* ========== Global Configuration for POLi = ENDS ========== */


  -- Insert data : system.processortype_tbl;
  
  INSERT INTO system.processortype_tbl(
            id, name)
    VALUES (1, 'PSP');
    INSERT INTO system.processortype_tbl(
            id, name)
    VALUES (2, 'Bank');
    INSERT INTO system.processortype_tbl(
            id, name)
    VALUES (3, 'Wallet');
    INSERT INTO system.processortype_tbl(
            id, name)
    VALUES (4, 'APM');
	
   -- Insert data : system.psp_tbl 
   -- Value : system_type;	
   
   UPDATE system.psp_tbl
   SET system_type=1
 WHERE id=0;
 UPDATE system.psp_tbl
   SET system_type=1
 WHERE id=1;
  UPDATE system.psp_tbl
   SET system_type=1
 WHERE id=2;
  UPDATE system.psp_tbl
   SET system_type=1
 WHERE id=3;
  UPDATE system.psp_tbl
   SET system_type=1
 WHERE id=4;
   UPDATE system.psp_tbl
   SET system_type=1
 WHERE id=5;
   UPDATE system.psp_tbl
   SET system_type=1
 WHERE id=6;
   UPDATE system.psp_tbl
   SET system_type=1
 WHERE id=7;
   UPDATE system.psp_tbl
   SET system_type=1
 WHERE id=8;
   UPDATE system.psp_tbl
   SET system_type=1
 WHERE id=9;
   UPDATE system.psp_tbl
   SET system_type=1
 WHERE id=10;
   UPDATE system.psp_tbl
   SET system_type=3
 WHERE id=11;
   UPDATE system.psp_tbl
   SET system_type=1
 WHERE id=12;
   UPDATE system.psp_tbl
   SET system_type=3
 WHERE id=13;
   UPDATE system.psp_tbl
   SET system_type=3
 WHERE id=14;
   UPDATE system.psp_tbl
   SET system_type=3
 WHERE id=15;
   UPDATE system.psp_tbl
   SET system_type=3
 WHERE id=16;
   UPDATE system.psp_tbl
   SET system_type=1
 WHERE id=17;
   UPDATE system.psp_tbl
   SET system_type=1
 WHERE id=18;
   UPDATE system.psp_tbl
   SET system_type=2
 WHERE id=19;
   UPDATE system.psp_tbl
   SET system_type=3
 WHERE id=20;
   UPDATE system.psp_tbl
   SET system_type=1
 WHERE id=21;
   UPDATE system.psp_tbl
   SET system_type=1
 WHERE id=22;
   UPDATE system.psp_tbl
   SET system_type=1
 WHERE id=23;
   UPDATE system.psp_tbl
   SET system_type=4
 WHERE id=24;
   UPDATE system.psp_tbl
   SET system_type=1
 WHERE id=25;
   UPDATE system.psp_tbl
   SET system_type=1
 WHERE id=26;
   UPDATE system.psp_tbl
   SET system_type=2
 WHERE id=27;
   UPDATE system.psp_tbl
   SET system_type=2
 WHERE id=28;
 
	
/* ========== Global Configuration for AliPay - Payment Method : START========== */
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength) VALUES (32, 'AliPay', 23, -1, -1, -1);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (32, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 32, id FROM System.PricePoint_Tbl WHERE amount = -1 AND countryid = 200;
/* ========== Global Configuration for AliPay - Payment Method : END========== */


/* ========== Global Configuration for Qiwi - Payment Method : START========== */
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength) VALUES (33, 'Qiwi', 23, -1, -1, -1);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (33, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 33, id FROM System.PricePoint_Tbl WHERE amount = -1 AND countryid = 200;
/* ========== Global Configuration for Qiwi - Payment Method : END========== */
/* ========== Global Configuration for Qiwi = STARTS ========== */
INSERT INTO System.PSP_Tbl (id, name) VALUES (31, 'Qiwi');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (200,31,'USD');

/*Qiwi*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (33, 31);

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 31, 'Qiwi', 'TBD', 'TBD');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 31, '-1');

-- Route Qiwi Card to Qiwi with country USA
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, countryid) VALUES (10001, 33, 31, true, 200);
/* ========== Global Configuration for Qiwi = ENDS ========== */INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (652, 9, 'MMK');
/*End Myanmar*/


/*Pushid in Enduser Account for PayByLink*/
ALTER TABLE enduser.account_tbl ADD COLUMN pushid character varying(100);
/*Pushid in Enduser Account for PayByLink*/


/*Merchant Name changed for Maybank (AMEX Implementation)*/
UPDATE client.merchantaccount_tbl SET name='sandbox' WHERE clientid=10007 and pspid=27;
/*Merchant Name changed for Maybank (AMEX Implementation)*/

/*MPO*/
INSERT INTO System.URLType_Tbl (id, name) VALUES (13, 'Merchant App return URL');

INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (13, 10007, 'com.mobilepayonline.return');

/*MPO*/




/* Update process type 2's name from Bank to Acquirer*/

UPDATE system.processortype_tbl SET name = 'Acquirer' WHERE id = 2;

/* ========== CONFIGURE NETS START ========== */
/*START: Adding PSP entries to the PSP_Tbl table for NETS*/

INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (35, 'NETS',2);

/*END: Adding PSP entries to the PSP_Tbl table for NETS*/

/*START: Adding Currency entries to the PSPCurrency_Tbl table for NETS*/

INSERT INTO system.pspcurrency_tbl (countryid, pspid, name) VALUES (100,35,'DKK');
INSERT INTO system.pspcurrency_tbl (countryid, pspid, name) VALUES (101,35,'SEK');
INSERT INTO system.pspcurrency_tbl (countryid, pspid, name) VALUES (102,35,'NOK');
INSERT INTO system.pspcurrency_tbl (countryid, pspid, name) VALUES (104,35,'EUR');
INSERT INTO system.pspcurrency_tbl (countryid, pspid, name) VALUES (127,35,'DKK');
INSERT INTO system.pspcurrency_tbl (countryid, pspid, name) VALUES (130,35,'DKK');
INSERT INTO system.pspcurrency_tbl (countryid, pspid, name) VALUES (132,35,'ISK');

/*END: Adding Currency entries to the PSPCurrency_Tbl table for NETS*/

/* ========== CONFIGURE DEMO ACCOUNT FOR NETS START ========== */
-- Wire-Card
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 35, '9105bb4f-ae68-4768-9c3b-3eda968f57ea', '70000-APILUHN-CARD', '8mhwavKVb91T');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 35, '-1');

/* ========== CONFIGURE DEMO ACCOUNT FOR NETS END ====== */

/* Additional Properties for client, merchant and PSP */


/*====================== Test Data =========================*/
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('PROCESSING_CODE', '000000', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CARD_ACCEPTOR_BUSINESS_CODE', '4511
', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CARD_ACCEPTOR_IDENTIFICATION_CODE', '1234', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CARD_ACCEPTOR_NAME', 'Test', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CARD_ACCEPTOR_ADDRESS', 'Test', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CARD_ACCEPTOR_CITY', 'Test', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CARD_ACCEPTOR_ZIP', '123456', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CARD_ACCEPTOR_REGION', 'ABC', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CARD_ACCEPTOR_COUNTRY', 'ABC', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('POS_DATA_CODE', '1234', 206, 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('FUNCTION_CODE', 'FULL', 206, 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CARD_ACCEPTOR_TERMINAL_ID', 'ABCD1234', 206, 'merchant');
/*====================== Test Data END =========================*/

/* END */