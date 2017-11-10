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

/*---------START : ADDED CHANGE FOR SUPPORTING CURRENCY SCHEMA-------------*/

INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (001,'﻿No Specific Currency','XXX',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (008,'Lek','ALL',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (012,'Algerian Dinar','DZD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (032,'Argentine Peso','ARS',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (036,'Australian Dollar','AUD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (044,'Bahamian Dollar','BSD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (048,'Bahraini Dinar','BHD',3);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (050,'Taka','BDT',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (051,'Armenian Dram','AMD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (052,'Barbados Dollar','BBD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (060,'Bermudian Dollar','BMD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (064,'Ngultrum','BTN',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (068,'Boliviano','BOB',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (072,'Pula','BWP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (084,'Belize Dollar','BZD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (090,'Solomon Islands Dollar','SBD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (096,'Brunei Dollar','BND',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (104,'Kyat','MMK',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (108,'Burundi Franc','BIF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (116,'Riel','KHR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (124,'Canadian Dollar','CAD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (132,'Cabo Verde Escudo','CVE',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (136,'Cayman Islands Dollar','KYD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (144,'Sri Lanka Rupee','LKR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (152,'Chilean Peso','CLP',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (156,'Yuan Renminbi','CNY',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (170,'Colombian Peso','COP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (174,'Comorian Franc ','KMF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (188,'Costa Rican Colon','CRC',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (191,'Kuna','HRK',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (192,'Cuban Peso','CUP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (203,'Czech Koruna','CZK',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (208,'Danish Krone','DKK',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (214,'Dominican Peso','DOP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (222,'El Salvador Colon','SVC',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (230,'Ethiopian Birr','ETB',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (232,'Nakfa','ERN',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (238,'Falkland Islands Pound','FKP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (242,'Fiji Dollar','FJD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (262,'Djibouti Franc','DJF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (270,'Dalasi','GMD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (292,'Gibraltar Pound','GIP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (320,'Quetzal','GTQ',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (324,'Guinean Franc','GNF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (328,'Guyana Dollar','GYD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (332,'Gourde','HTG',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (340,'Lempira','HNL',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (344,'Hong Kong Dollar','HKD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (348,'Forint','HUF',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (352,'Iceland Krona','ISK',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (356,'Indian Rupee','INR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (360,'Rupiah','IDR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (364,'Iranian Rial','IRR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (368,'Iraqi Dinar','IQD',3);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (376,'New Israeli Sheqel','ILS',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (388,'Jamaican Dollar','JMD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (392,'Yen','JPY',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (398,'Tenge','KZT',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (400,'Jordanian Dinar','JOD',3);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (404,'Kenyan Shilling','KES',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (408,'North Korean Won','KPW',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (410,'Won','KRW',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (414,'Kuwaiti Dinar','KWD',3);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (417,'Som','KGS',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (418,'Lao Kip','LAK',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (422,'Lebanese Pound','LBP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (426,'Loti','LSL',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (430,'Liberian Dollar','LRD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (434,'Libyan Dinar','LYD',3);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (446,'Pataca','MOP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (454,'Malawi Kwacha','MWK',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (458,'Malaysian Ringgit','MYR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (462,'Rufiyaa','MVR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (478,'Ouguiya','MRO',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (480,'Mauritius Rupee','MUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (484,'Mexican Peso','MXN',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (496,'Tugrik','MNT',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (498,'Moldovan Leu','MDL',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (504,'Moroccan Dirham','MAD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (512,'Rial Omani','OMR',3);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (516,'Namibia Dollar','NAD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (524,'Nepalese Rupee','NPR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (532,'Netherlands Antillean Guilder','ANG',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (533,'Aruban Florin','AWG',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (548,'Vatu','VUV',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (554,'New Zealand Dollar','NZD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (558,'Cordoba Oro','NIO',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (566,'Naira','NGN',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (578,'Norwegian Krone','NOK',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (586,'Pakistan Rupee','PKR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (590,'Balboa','PAB',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (598,'Kina','PGK',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (600,'Guarani','PYG',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (604,'Sol','PEN',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (608,'Philippine Peso','PHP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (634,'Qatari Rial','QAR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (643,'Russian Ruble','RUB',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (646,'Rwanda Franc','RWF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (654,'Saint Helena Pound','SHP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (678,'Dobra','STD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (682,'Saudi Riyal','SAR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (690,'Seychelles Rupee','SCR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (694,'Leone','SLL',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (702,'Singapore Dollar','SGD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (704,'Dong','VND',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (706,'Somali Shilling','SOS',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (710,'Rand','ZAR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (728,'South Sudanese Pound','SSP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (748,'Lilangeni','SZL',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (752,'Swedish Krona','SEK',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (756,'Swiss Franc','CHF',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (760,'Syrian Pound','SYP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (764,'Baht','THB',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (776,'Pa’anga','TOP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (780,'Trinidad and Tobago Dollar','TTD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (784,'UAE Dirham','AED',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (788,'Tunisian Dinar','TND',3);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (800,'Uganda Shilling','UGX',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (807,'Denar','MKD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (818,'Egyptian Pound','EGP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (826,'Pound Sterling','GBP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (834,'Tanzanian Shilling','TZS',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (840,'US Dollar','USD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (858,'Peso Uruguayo','UYU',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (860,'Uzbekistan Sum','UZS',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (882,'Tala','WST',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (886,'Yemeni Rial','YER',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (901,'New Taiwan Dollar','TWD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (931,'Peso Convertible','CUC',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (932,'Zimbabwe Dollar','ZWL',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (933,'Belarusian Ruble','BYN',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (934,'Turkmenistan New Manat','TMT',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (936,'Ghana Cedi','GHS',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (937,'Bolívar','VEF',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (938,'Sudanese Pound','SDG',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (940,'Uruguay Peso en Unidades Indexadas (URUIURUI)','UYI',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (941,'Serbian Dinar','RSD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (943,'Mozambique Metical','MZN',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (944,'Azerbaijan Manat','AZN',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (946,'Romanian Leu','RON',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (947,'WIR Euro','CHE',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (948,'WIR Franc','CHW',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (949,'Turkish Lira','TRY',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (950,'CFA Franc BEAC','XAF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (951,'East Caribbean Dollar','XCD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (952,'CFA Franc BCEAO','XOF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (953,'CFP Franc','XPF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (960,'SDR (Special Drawing Right)','XDR',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (965,'ADB Unit of Account','XUA',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (967,'Zambian Kwacha','ZMW',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (968,'Surinam Dollar','SRD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (969,'Malagasy Ariary','MGA',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (970,'Unidad de Valor Real','COU',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (971,'Afghani','AFN',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (972,'Somoni','TJS',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (973,'Kwanza','AOA',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (975,'Bulgarian Lev','BGN',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (976,'Congolese Franc','CDF',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (977,'Convertible Mark','BAM',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (978,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (979,'Mexican Unidad de Inversion (UDI)','MXV',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (980,'Hryvnia','UAH',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (981,'Lari','GEL',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (984,'Mvdol','BOV',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (985,'Zloty','PLN',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (986,'Brazilian Real','BRL',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (990,'Unidad de Fomento','CLF',4);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (994,'Sucre','XSU',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (997,'US Dollar (Next day)','USN',2);


UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000, currencyid = 001 WHERE id = 0;
UPDATE System.Country_Tbl SET alpha2code = 'PS', alpha3code = 'PSE', code = 275, currencyid = 001 WHERE id = 146;
UPDATE System.Country_Tbl SET alpha2code = 'AQ', alpha3code = 'ATA', code = 10, currencyid = 001 WHERE id = 413;
UPDATE System.Country_Tbl SET alpha2code = 'AM', alpha3code = 'ARM', code = 51, currencyid = 051 WHERE id = 119;
UPDATE System.Country_Tbl SET alpha2code = 'AR', alpha3code = 'ARG', code = 32, currencyid = 032 WHERE id = 400;
UPDATE System.Country_Tbl SET alpha2code = 'AU', alpha3code = 'AUS', code = 36, currencyid = 036 WHERE id = 500;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000, currencyid = 784 WHERE id = 602;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000, currencyid = 784 WHERE id = 600;
UPDATE System.Country_Tbl SET alpha2code = 'AF', alpha3code = 'AFG', code = 4, currencyid = 971 WHERE id = 116;
UPDATE System.Country_Tbl SET alpha2code = 'AE', alpha3code = 'ARE', code = 784, currencyid = 784 WHERE id = 647;
UPDATE System.Country_Tbl SET alpha2code = 'AL', alpha3code = 'ALB', code = 8, currencyid = 008 WHERE id = 117;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000, currencyid = 532 WHERE id = 415;
UPDATE System.Country_Tbl SET alpha2code = 'SX', alpha3code = 'SXM', code = 534, currencyid = 532 WHERE id = 436;
UPDATE System.Country_Tbl SET alpha2code = 'CC', alpha3code = 'CCK', code = 166, currencyid = 036 WHERE id = 507;
UPDATE System.Country_Tbl SET alpha2code = 'TV', alpha3code = 'TUV', code = 798, currencyid = 036 WHERE id = 516;
UPDATE System.Country_Tbl SET alpha2code = 'CX', alpha3code = 'CXR', code = 162, currencyid = 036 WHERE id = 508;
UPDATE System.Country_Tbl SET alpha2code = 'KI', alpha3code = 'KIR', code = 296, currencyid = 036 WHERE id = 630;
UPDATE System.Country_Tbl SET alpha2code = 'NR', alpha3code = 'NRU', code = 520, currencyid = 036 WHERE id = 511;
UPDATE System.Country_Tbl SET alpha2code = 'AW', alpha3code = 'ABW', code = 533, currencyid = 533 WHERE id = 401;
UPDATE System.Country_Tbl SET alpha2code = 'AZ', alpha3code = 'AZE', code = 31, currencyid = 944 WHERE id = 611;
UPDATE System.Country_Tbl SET alpha2code = 'BA', alpha3code = 'BIH', code = 70, currencyid = 977 WHERE id = 121;
UPDATE System.Country_Tbl SET alpha2code = 'BB', alpha3code = 'BRB', code = 52, currencyid = 052 WHERE id = 205;
UPDATE System.Country_Tbl SET alpha2code = 'BD', alpha3code = 'BGD', code = 50, currencyid = 050 WHERE id = 302;
UPDATE System.Country_Tbl SET alpha2code = 'AO', alpha3code = 'AGO', code = 24, currencyid = 973 WHERE id = 301;
UPDATE System.Country_Tbl SET alpha2code = 'BG', alpha3code = 'BGR', code = 100, currencyid = 975 WHERE id = 122;
UPDATE System.Country_Tbl SET alpha2code = 'BH', alpha3code = 'BHR', code = 48, currencyid = 048 WHERE id = 601;
UPDATE System.Country_Tbl SET alpha2code = 'BI', alpha3code = 'BDI', code = 108, currencyid = 108 WHERE id = 307;
UPDATE System.Country_Tbl SET alpha2code = 'BM', alpha3code = 'BMU', code = 60, currencyid = 060 WHERE id = 214;
UPDATE System.Country_Tbl SET alpha2code = 'BN', alpha3code = 'BRN', code = 96, currencyid = 096 WHERE id = 501;
UPDATE System.Country_Tbl SET alpha2code = 'BO', alpha3code = 'BOL', code = 68, currencyid = 984 WHERE id = 304;
UPDATE System.Country_Tbl SET alpha2code = 'BR', alpha3code = 'BRA', code = 76, currencyid = 986 WHERE id = 403;
UPDATE System.Country_Tbl SET alpha2code = 'BS', alpha3code = 'BHS', code = 44, currencyid = 044 WHERE id = 215;
UPDATE System.Country_Tbl SET alpha2code = 'BT', alpha3code = 'BTN', code = 64, currencyid = 064 WHERE id = 612;
UPDATE System.Country_Tbl SET alpha2code = 'BW', alpha3code = 'BWA', code = 72, currencyid = 072 WHERE id = 305;
UPDATE System.Country_Tbl SET alpha2code = 'BY', alpha3code = 'BLR', code = 112, currencyid = 933 WHERE id = 120;
UPDATE System.Country_Tbl SET alpha2code = 'BZ', alpha3code = 'BLZ', code = 84, currencyid = 084 WHERE id = 402;
UPDATE System.Country_Tbl SET alpha2code = 'CA', alpha3code = 'CAN', code = 124, currencyid = 124 WHERE id = 202;
UPDATE System.Country_Tbl SET alpha2code = 'CD', alpha3code = 'COD', code = 180, currencyid = 976 WHERE id = 315;
UPDATE System.Country_Tbl SET alpha2code = 'LI', alpha3code = 'LIE', code = 438, currencyid = 756 WHERE id = 136;
UPDATE System.Country_Tbl SET alpha2code = 'CH', alpha3code = 'CHE', code = 756, currencyid = 948 WHERE id = 109;
UPDATE System.Country_Tbl SET alpha2code = 'CL', alpha3code = 'CHL', code = 152, currencyid = 152 WHERE id = 404;
UPDATE System.Country_Tbl SET alpha2code = 'CN', alpha3code = 'CHN', code = 156, currencyid = 156 WHERE id = 609;
UPDATE System.Country_Tbl SET alpha2code = 'CO', alpha3code = 'COL', code = 170, currencyid = 170 WHERE id = 405;
UPDATE System.Country_Tbl SET alpha2code = 'CR', alpha3code = 'CRI', code = 188, currencyid = 188 WHERE id = 406;
UPDATE System.Country_Tbl SET alpha2code = 'CU', alpha3code = 'CUB', code = 192, currencyid = 192 WHERE id = 208;
UPDATE System.Country_Tbl SET alpha2code = 'CV', alpha3code = 'CPV', code = 132, currencyid = 132 WHERE id = 309;
UPDATE System.Country_Tbl SET alpha2code = 'CZ', alpha3code = 'CZE', code = 203, currencyid = 203 WHERE id = 125;
UPDATE System.Country_Tbl SET alpha2code = 'DJ', alpha3code = 'DJI', code = 262, currencyid = 262 WHERE id = 316;
UPDATE System.Country_Tbl SET alpha2code = 'FO', alpha3code = 'FRO', code = 234, currencyid = 208 WHERE id = 127;
UPDATE System.Country_Tbl SET alpha2code = 'GL', alpha3code = 'GRL', code = 304, currencyid = 208 WHERE id = 130;
UPDATE System.Country_Tbl SET alpha2code = 'DK', alpha3code = 'DNK', code = 208, currencyid = 208 WHERE id = 100;
UPDATE System.Country_Tbl SET alpha2code = 'DO', alpha3code = 'DOM', code = 214, currencyid = 214 WHERE id = 209;
UPDATE System.Country_Tbl SET alpha2code = 'DZ', alpha3code = 'DZA', code = 12, currencyid = 012 WHERE id = 300;
UPDATE System.Country_Tbl SET alpha2code = 'EG', alpha3code = 'EGY', code = 818, currencyid = 818 WHERE id = 317;
UPDATE System.Country_Tbl SET alpha2code = 'ER', alpha3code = 'ERI', code = 232, currencyid = 232 WHERE id = 337;
UPDATE System.Country_Tbl SET alpha2code = 'ET', alpha3code = 'ETH', code = 231, currencyid = 230 WHERE id = 319;
UPDATE System.Country_Tbl SET alpha2code = 'LU', alpha3code = 'LUX', code = 442, currencyid = 978 WHERE id = 138;
UPDATE System.Country_Tbl SET alpha2code = 'AT', alpha3code = 'AUT', code = 40, currencyid = 978 WHERE id = 114;
UPDATE System.Country_Tbl SET alpha2code = 'BE', alpha3code = 'BEL', code = 56, currencyid = 978 WHERE id = 111;
UPDATE System.Country_Tbl SET alpha2code = 'PM', alpha3code = 'SPM', code = 666, currencyid = 978 WHERE id = 145;
UPDATE System.Country_Tbl SET alpha2code = 'CY', alpha3code = 'CYP', code = 196, currencyid = 978 WHERE id = 124;
UPDATE System.Country_Tbl SET alpha2code = 'ES', alpha3code = 'ESP', code = 724, currencyid = 978 WHERE id = 113;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000, currencyid = 978 WHERE id = 409;
UPDATE System.Country_Tbl SET alpha2code = 'EE', alpha3code = 'EST', code = 233, currencyid = 978 WHERE id = 126;
UPDATE System.Country_Tbl SET alpha2code = 'FI', alpha3code = 'FIN', code = 246, currencyid = 978 WHERE id = 104;
UPDATE System.Country_Tbl SET alpha2code = 'FR', alpha3code = 'FRA', code = 250, currencyid = 978 WHERE id = 108;
UPDATE System.Country_Tbl SET alpha2code = 'GF', alpha3code = 'GUF', code = 254, currencyid = 978 WHERE id = 421;
UPDATE System.Country_Tbl SET alpha2code = 'DE', alpha3code = 'DEU', code = 276, currencyid = 978 WHERE id = 115;
UPDATE System.Country_Tbl SET alpha2code = 'GR', alpha3code = 'GRC', code = 300, currencyid = 978 WHERE id = 105;
UPDATE System.Country_Tbl SET alpha2code = 'GP', alpha3code = 'GLP', code = 312, currencyid = 978 WHERE id = 210;
UPDATE System.Country_Tbl SET alpha2code = 'IE', alpha3code = 'IRL', code = 372, currencyid = 978 WHERE id = 133;
UPDATE System.Country_Tbl SET alpha2code = 'SI', alpha3code = 'SVN', code = 705, currencyid = 978 WHERE id = 153;
UPDATE System.Country_Tbl SET alpha2code = 'SM', alpha3code = 'SMR', code = 674, currencyid = 978 WHERE id = 151;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000, currencyid = 978 WHERE id = 148;
UPDATE System.Country_Tbl SET alpha2code = 'IT', alpha3code = 'ITA', code = 380, currencyid = 978 WHERE id = 107;
UPDATE System.Country_Tbl SET alpha2code = 'VA', alpha3code = 'VAT', code = 336, currencyid = 978 WHERE id = 156;
UPDATE System.Country_Tbl SET alpha2code = 'RE', alpha3code = 'REU', code = 638, currencyid = 978 WHERE id = 343;
UPDATE System.Country_Tbl SET alpha2code = 'MT', alpha3code = 'MLT', code = 470, currencyid = 978 WHERE id = 139;
UPDATE System.Country_Tbl SET alpha2code = 'MQ', alpha3code = 'MTQ', code = 474, currencyid = 978 WHERE id = 426;
UPDATE System.Country_Tbl SET alpha2code = 'YT', alpha3code = 'MYT', code = 175, currencyid = 978 WHERE id = 339;
UPDATE System.Country_Tbl SET alpha2code = 'MC', alpha3code = 'MCO', code = 492, currencyid = 978 WHERE id = 141;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000, currencyid = 978 WHERE id = 629;
UPDATE System.Country_Tbl SET alpha2code = 'AD', alpha3code = 'AND', code = 20, currencyid = 978 WHERE id = 118;
UPDATE System.Country_Tbl SET alpha2code = 'LT', alpha3code = 'LTU', code = 440, currencyid = 978 WHERE id = 137;
UPDATE System.Country_Tbl SET alpha2code = 'LV', alpha3code = 'LVA', code = 428, currencyid = 978 WHERE id = 135;
UPDATE System.Country_Tbl SET alpha2code = 'SK', alpha3code = 'SVK', code = 703, currencyid = 978 WHERE id = 152;
UPDATE System.Country_Tbl SET alpha2code = 'ME', alpha3code = 'MNE', code = 499, currencyid = 978 WHERE id = 142;
UPDATE System.Country_Tbl SET alpha2code = 'NL', alpha3code = 'NLD', code = 528, currencyid = 978 WHERE id = 110;
UPDATE System.Country_Tbl SET alpha2code = 'FJ', alpha3code = 'FJI', code = 242, currencyid = 242 WHERE id = 503;
UPDATE System.Country_Tbl SET alpha2code = 'FK', alpha3code = 'FLK', code = 238, currencyid = 238 WHERE id = 419;
UPDATE System.Country_Tbl SET alpha2code = 'GS', alpha3code = 'SGS', code = 239, currencyid = 826 WHERE id = 422;
UPDATE System.Country_Tbl SET alpha2code = 'GE', alpha3code = 'GEO', code = 268, currencyid = 826 WHERE id = 128;
UPDATE System.Country_Tbl SET alpha2code = 'GB', alpha3code = 'GBR', code = 826, currencyid = 826 WHERE id = 103;
UPDATE System.Country_Tbl SET alpha2code = 'SH', alpha3code = 'SHN', code = 654, currencyid = 826 WHERE id = 335;
UPDATE System.Country_Tbl SET alpha2code = 'IM', alpha3code = 'IMN', code = 833, currencyid = 826 WHERE id = 134;
UPDATE System.Country_Tbl SET alpha2code = 'GH', alpha3code = 'GHA', code = 288, currencyid = 936 WHERE id = 322;
UPDATE System.Country_Tbl SET alpha2code = 'GI', alpha3code = 'GIB', code = 292, currencyid = 292 WHERE id = 129;
UPDATE System.Country_Tbl SET alpha2code = 'GM', alpha3code = 'GMB', code = 270, currencyid = 270 WHERE id = 321;
UPDATE System.Country_Tbl SET alpha2code = 'GN', alpha3code = 'GIN', code = 324, currencyid = 324 WHERE id = 323;
UPDATE System.Country_Tbl SET alpha2code = 'GT', alpha3code = 'GTM', code = 320, currencyid = 320 WHERE id = 410;
UPDATE System.Country_Tbl SET alpha2code = 'GY', alpha3code = 'GUY', code = 328, currencyid = 328 WHERE id = 411;
UPDATE System.Country_Tbl SET alpha2code = 'HK', alpha3code = 'HKG', code = 344, currencyid = 344 WHERE id = 614;
UPDATE System.Country_Tbl SET alpha2code = 'HN', alpha3code = 'HND', code = 340, currencyid = 340 WHERE id = 412;
UPDATE System.Country_Tbl SET alpha2code = 'HR', alpha3code = 'HRV', code = 191, currencyid = 191 WHERE id = 123;
UPDATE System.Country_Tbl SET alpha2code = 'HU', alpha3code = 'HUN', code = 348, currencyid = 348 WHERE id = 131;
UPDATE System.Country_Tbl SET alpha2code = 'ID', alpha3code = 'IDN', code = 360, currencyid = 360 WHERE id = 505;
UPDATE System.Country_Tbl SET alpha2code = 'IL', alpha3code = 'ISR', code = 376, currencyid = 376 WHERE id = 106;
UPDATE System.Country_Tbl SET alpha2code = 'IN', alpha3code = 'IND', code = 356, currencyid = 356 WHERE id = 603;
UPDATE System.Country_Tbl SET alpha2code = 'IQ', alpha3code = 'IRQ', code = 368, currencyid = 368 WHERE id = 628;
UPDATE System.Country_Tbl SET alpha2code = 'IR', alpha3code = 'IRN', code = 364, currencyid = 364 WHERE id = 615;
UPDATE System.Country_Tbl SET alpha2code = 'IS', alpha3code = 'ISL', code = 352, currencyid = 352 WHERE id = 132;
UPDATE System.Country_Tbl SET alpha2code = 'JM', alpha3code = 'JAM', code = 388, currencyid = 388 WHERE id = 212;
UPDATE System.Country_Tbl SET alpha2code = 'JO', alpha3code = 'JOR', code = 400, currencyid = 400 WHERE id = 617;
UPDATE System.Country_Tbl SET alpha2code = 'JP', alpha3code = 'JPN', code = 392, currencyid = 392 WHERE id = 616;
UPDATE System.Country_Tbl SET alpha2code = 'KG', alpha3code = 'KGZ', code = 417, currencyid = 417 WHERE id = 619;
UPDATE System.Country_Tbl SET alpha2code = 'KH', alpha3code = 'KHM', code = 116, currencyid = 116 WHERE id = 613;
UPDATE System.Country_Tbl SET alpha2code = 'KM', alpha3code = 'COM', code = 174, currencyid = 174 WHERE id = 312;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000, currencyid = 408 WHERE id = 631;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000, currencyid = 410 WHERE id = 632;
UPDATE System.Country_Tbl SET alpha2code = 'KW', alpha3code = 'KWT', code = 414, currencyid = 414 WHERE id = 604;
UPDATE System.Country_Tbl SET alpha2code = 'KY', alpha3code = 'CYM', code = 136, currencyid = 136 WHERE id = 207;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000, currencyid = 398 WHERE id = 633;
UPDATE System.Country_Tbl SET alpha2code = 'KZ', alpha3code = 'KAZ', code = 398, currencyid = 398 WHERE id = 618;
UPDATE System.Country_Tbl SET alpha2code = 'LA', alpha3code = 'LAO', code = 418, currencyid = 418 WHERE id = 620;
UPDATE System.Country_Tbl SET alpha2code = 'LB', alpha3code = 'LBN', code = 422, currencyid = 422 WHERE id = 621;
UPDATE System.Country_Tbl SET alpha2code = 'LK', alpha3code = 'LKA', code = 144, currencyid = 144 WHERE id = 634;
UPDATE System.Country_Tbl SET alpha2code = 'LR', alpha3code = 'LBR', code = 430, currencyid = 430 WHERE id = 327;
UPDATE System.Country_Tbl SET alpha2code = 'LS', alpha3code = 'LSO', code = 426, currencyid = 426 WHERE id = 326;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000, currencyid = 434 WHERE id = 338;
UPDATE System.Country_Tbl SET alpha2code = 'EH', alpha3code = 'ESH', code = 732, currencyid = 504 WHERE id = 336;
UPDATE System.Country_Tbl SET alpha2code = 'MA', alpha3code = 'MAR', code = 504, currencyid = 504 WHERE id = 333;
UPDATE System.Country_Tbl SET alpha2code = 'MD', alpha3code = 'MDA', code = 498, currencyid = 498 WHERE id = 140;
UPDATE System.Country_Tbl SET alpha2code = 'MG', alpha3code = 'MDG', code = 450, currencyid = 969 WHERE id = 328;
UPDATE System.Country_Tbl SET alpha2code = 'MK', alpha3code = 'MKD', code = 807, currencyid = 807 WHERE id = 143;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000, currencyid = 104 WHERE id = 625;
UPDATE System.Country_Tbl SET alpha2code = 'MN', alpha3code = 'MNG', code = 496, currencyid = 496 WHERE id = 624;
UPDATE System.Country_Tbl SET alpha2code = 'MO', alpha3code = 'MAC', code = 446, currencyid = 446 WHERE id = 636;
UPDATE System.Country_Tbl SET alpha2code = 'MO', alpha3code = 'MAC', code = 446, currencyid = 446 WHERE id = 622;
UPDATE System.Country_Tbl SET alpha2code = 'MR', alpha3code = 'MRT', code = 478, currencyid = 478 WHERE id = 331;
UPDATE System.Country_Tbl SET alpha2code = 'MU', alpha3code = 'MUS', code = 480, currencyid = 480 WHERE id = 332;
UPDATE System.Country_Tbl SET alpha2code = 'MV', alpha3code = 'MDV', code = 462, currencyid = 462 WHERE id = 623;
UPDATE System.Country_Tbl SET alpha2code = 'MW', alpha3code = 'MWI', code = 454, currencyid = 454 WHERE id = 329;
UPDATE System.Country_Tbl SET alpha2code = 'MX', alpha3code = 'MEX', code = 484, currencyid = 484 WHERE id = 201;
UPDATE System.Country_Tbl SET alpha2code = 'MY', alpha3code = 'MYS', code = 458, currencyid = 458 WHERE id = 638;
UPDATE System.Country_Tbl SET alpha2code = 'MZ', alpha3code = 'MOZ', code = 508, currencyid = 943 WHERE id = 334;
UPDATE System.Country_Tbl SET alpha2code = 'NA', alpha3code = 'NAM', code = 516, currencyid = 516 WHERE id = 340;
UPDATE System.Country_Tbl SET alpha2code = 'KE', alpha3code = 'KEN', code = 404, currencyid = 404 WHERE id = 325;
UPDATE System.Country_Tbl SET alpha2code = 'NI', alpha3code = 'NIC', code = 558, currencyid = 558 WHERE id = 427;
UPDATE System.Country_Tbl SET alpha2code = 'BV', alpha3code = 'BVT', code = 74, currencyid = 578 WHERE id = 416;
UPDATE System.Country_Tbl SET alpha2code = 'NP', alpha3code = 'NPL', code = 524, currencyid = 524 WHERE id = 639;
UPDATE System.Country_Tbl SET alpha2code = 'PN', alpha3code = 'PCN', code = 612, currencyid = 554 WHERE id = 430;
UPDATE System.Country_Tbl SET alpha2code = 'CK', alpha3code = 'COK', code = 184, currencyid = 554 WHERE id = 502;
UPDATE System.Country_Tbl SET alpha2code = 'NZ', alpha3code = 'NZL', code = 554, currencyid = 554 WHERE id = 513;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000, currencyid = 554 WHERE id = 417;
UPDATE System.Country_Tbl SET alpha2code = 'PK', alpha3code = 'PAK', code = 586, currencyid = 586 WHERE id = 610;
UPDATE System.Country_Tbl SET alpha2code = 'QA', alpha3code = 'QAT', code = 634, currencyid = 634 WHERE id = 606;
UPDATE System.Country_Tbl SET alpha2code = 'RS', alpha3code = 'SRB', code = 688, currencyid = 941 WHERE id = 150;
UPDATE System.Country_Tbl SET alpha2code = 'RU', alpha3code = 'RUS', code = 643, currencyid = 643 WHERE id = 607;
UPDATE System.Country_Tbl SET alpha2code = 'RW', alpha3code = 'RWA', code = 646, currencyid = 646 WHERE id = 344;
UPDATE System.Country_Tbl SET alpha2code = 'SA', alpha3code = 'SAU', code = 682, currencyid = 682 WHERE id = 608;
UPDATE System.Country_Tbl SET alpha2code = 'SB', alpha3code = 'SLB', code = 90, currencyid = 90 WHERE id = 514;
UPDATE System.Country_Tbl SET alpha2code = 'SC', alpha3code = 'SYC', code = 690, currencyid = 690 WHERE id = 345;
UPDATE System.Country_Tbl SET alpha2code = 'SD', alpha3code = 'SDN', code = 729, currencyid = 938 WHERE id = 346;
UPDATE System.Country_Tbl SET alpha2code = 'SE', alpha3code = 'SWE', code = 752, currencyid = 752 WHERE id = 101;
UPDATE System.Country_Tbl SET alpha2code = 'SG', alpha3code = 'SGP', code = 702, currencyid = 702 WHERE id = 642;
UPDATE System.Country_Tbl SET alpha2code = 'SH', alpha3code = 'SHN', code = 654, currencyid = 654 WHERE id = 347;
UPDATE System.Country_Tbl SET alpha2code = 'SL', alpha3code = 'SLE', code = 694, currencyid = 694 WHERE id = 348;
UPDATE System.Country_Tbl SET alpha2code = 'SO', alpha3code = 'SOM', code = 706, currencyid = 706 WHERE id = 434;
UPDATE System.Country_Tbl SET alpha2code = 'SR', alpha3code = 'SUR', code = 740, currencyid = 968 WHERE id = 435;
UPDATE System.Country_Tbl SET alpha2code = 'ST', alpha3code = 'STP', code = 678, currencyid = 678 WHERE id = 349;
UPDATE System.Country_Tbl SET alpha2code = 'SY', alpha3code = 'SYR', code = 760, currencyid = 760 WHERE id = 643;
UPDATE System.Country_Tbl SET alpha2code = 'SZ', alpha3code = 'SWZ', code = 748, currencyid = 748 WHERE id = 350;
UPDATE System.Country_Tbl SET alpha2code = 'TH', alpha3code = 'THA', code = 764, currencyid = 764 WHERE id = 644;
UPDATE System.Country_Tbl SET alpha2code = 'TM', alpha3code = 'TKM', code = 795, currencyid = 934 WHERE id = 645;
UPDATE System.Country_Tbl SET alpha2code = 'TN', alpha3code = 'TUN', code = 788, currencyid = 788 WHERE id = 352;
UPDATE System.Country_Tbl SET alpha2code = 'TO', alpha3code = 'TON', code = 776, currencyid = 776 WHERE id = 515;
UPDATE System.Country_Tbl SET alpha2code = 'TR', alpha3code = 'TUR', code = 792, currencyid = 949 WHERE id = 154;
UPDATE System.Country_Tbl SET alpha2code = 'TT', alpha3code = 'TTO', code = 780, currencyid = 780 WHERE id = 438;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000, currencyid = 901 WHERE id = 646;
UPDATE System.Country_Tbl SET alpha2code = 'TZ', alpha3code = 'TZA', code = 834, currencyid = 834 WHERE id = 353;
UPDATE System.Country_Tbl SET alpha2code = 'UA', alpha3code = 'UKR', code = 804, currencyid = 980 WHERE id = 155;
UPDATE System.Country_Tbl SET alpha2code = 'UG', alpha3code = 'UGA', code = 800, currencyid = 800 WHERE id = 354;
UPDATE System.Country_Tbl SET alpha2code = 'UM', alpha3code = 'UMI', code = 581, currencyid = 840 WHERE id = 218;
UPDATE System.Country_Tbl SET alpha2code = 'US', alpha3code = 'USA', code = 840, currencyid = 840 WHERE id = 200;
UPDATE System.Country_Tbl SET alpha2code = 'VG', alpha3code = 'VGB', code = 92, currencyid = 840 WHERE id = 206;
UPDATE System.Country_Tbl SET alpha2code = 'VI', alpha3code = 'VIR', code = 850, currencyid = 840 WHERE id = 442;
UPDATE System.Country_Tbl SET alpha2code = 'FM', alpha3code = 'FSM', code = 583, currencyid = 840 WHERE id = 506;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000, currencyid = 840 WHERE id = 216;
UPDATE System.Country_Tbl SET alpha2code = 'MH', alpha3code = 'MHL', code = 584, currencyid = 840 WHERE id = 635;
UPDATE System.Country_Tbl SET alpha2code = 'EC', alpha3code = 'ECU', code = 218, currencyid = 840 WHERE id = 407;
UPDATE System.Country_Tbl SET alpha2code = 'HT', alpha3code = 'HTI', code = 332, currencyid = 840 WHERE id = 211;
UPDATE System.Country_Tbl SET alpha2code = 'GU', alpha3code = 'GUM', code = 316, currencyid = 840 WHERE id = 627;
UPDATE System.Country_Tbl SET alpha2code = 'SV', alpha3code = 'SLV', code = 222, currencyid = 840 WHERE id = 408;
UPDATE System.Country_Tbl SET alpha2code = 'TC', alpha3code = 'TCA', code = 796, currencyid = 840 WHERE id = 437;
UPDATE System.Country_Tbl SET alpha2code = 'ZW', alpha3code = 'ZWE', code = 716, currencyid = 840 WHERE id = 358;
UPDATE System.Country_Tbl SET alpha2code = 'AS', alpha3code = 'ASM', code = 16, currencyid = 840 WHERE id = 213;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000, currencyid = 840 WHERE id = 626;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000, currencyid = 840 WHERE id = 423;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000, currencyid = 840 WHERE id = 217;
UPDATE System.Country_Tbl SET alpha2code = 'UY', alpha3code = 'URY', code = 858, currencyid = 858 WHERE id = 439;
UPDATE System.Country_Tbl SET alpha2code = 'UZ', alpha3code = 'UZB', code = 860, currencyid = 860 WHERE id = 648;
UPDATE System.Country_Tbl SET alpha2code = 'VE', alpha3code = 'VEN', code = 862, currencyid = 937 WHERE id = 441;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000, currencyid = 704 WHERE id = 649;
UPDATE System.Country_Tbl SET alpha2code = 'VU', alpha3code = 'VUT', code = 548, currencyid = 548 WHERE id = 517;
UPDATE System.Country_Tbl SET alpha2code = 'WS', alpha3code = 'WSM', code = 882, currencyid = 882 WHERE id = 519;
UPDATE System.Country_Tbl SET alpha2code = 'CM', alpha3code = 'CMR', code = 120, currencyid = 950 WHERE id = 308;
UPDATE System.Country_Tbl SET alpha2code = 'CF', alpha3code = 'CAF', code = 140, currencyid = 950 WHERE id = 310;
UPDATE System.Country_Tbl SET alpha2code = 'NF', alpha3code = 'NFK', code = 574, currencyid = 36 WHERE id = 510;
UPDATE System.Country_Tbl SET alpha2code = 'PT', alpha3code = 'PRT', code = 620, currencyid = 978 WHERE id = 147;
UPDATE System.Country_Tbl SET alpha2code = 'NG', alpha3code = 'NGA', code = 566, currencyid = 566 WHERE id = 342;
UPDATE System.Country_Tbl SET alpha2code = 'NO', alpha3code = 'NOR', code = 578, currencyid = 578 WHERE id = 102;
UPDATE System.Country_Tbl SET alpha2code = 'NU', alpha3code = 'NIU', code = 570, currencyid = 554 WHERE id = 512;
UPDATE System.Country_Tbl SET alpha2code = 'OM', alpha3code = 'OMN', code = 512, currencyid = 512 WHERE id = 605;
UPDATE System.Country_Tbl SET alpha2code = 'PE', alpha3code = 'PER', code = 604, currencyid = 604 WHERE id = 429;
UPDATE System.Country_Tbl SET alpha2code = 'PH', alpha3code = 'PHL', code = 608, currencyid = 608 WHERE id = 640;
UPDATE System.Country_Tbl SET alpha2code = 'PL', alpha3code = 'POL', code = 616, currencyid = 985 WHERE id = 112;
UPDATE System.Country_Tbl SET alpha2code = 'PY', alpha3code = 'PRY', code = 600, currencyid = 600 WHERE id = 432;
UPDATE System.Country_Tbl SET alpha2code = 'RO', alpha3code = 'ROU', code = 642, currencyid = 946 WHERE id = 149;
UPDATE System.Country_Tbl SET alpha2code = 'MP', alpha3code = 'MNP', code = 580, currencyid = 840 WHERE id = 637;
UPDATE System.Country_Tbl SET alpha2code = 'PW', alpha3code = 'PLW', code = 585, currencyid = 840 WHERE id = 641;
UPDATE System.Country_Tbl SET alpha2code = 'PR', alpha3code = 'PRI', code = 630, currencyid = 840 WHERE id = 431;
UPDATE System.Country_Tbl SET alpha2code = 'PA', alpha3code = 'PAN', code = 591, currencyid = 840 WHERE id = 428;
UPDATE System.Country_Tbl SET alpha2code = 'TD', alpha3code = 'TCD', code = 148, currencyid = 950 WHERE id = 311;
UPDATE System.Country_Tbl SET alpha2code = 'CG', alpha3code = 'COG', code = 178, currencyid = 950 WHERE id = 313;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000, currencyid = 950 WHERE id = 357;
UPDATE System.Country_Tbl SET alpha2code = 'GQ', alpha3code = 'GNQ', code = 226, currencyid = 950 WHERE id = 318;
UPDATE System.Country_Tbl SET alpha2code = 'GA', alpha3code = 'GAB', code = 266, currencyid = 950 WHERE id = 320;
UPDATE System.Country_Tbl SET alpha2code = 'KN', alpha3code = 'KNA', code = 659, currencyid = 951 WHERE id = 424;
UPDATE System.Country_Tbl SET alpha2code = 'AG', alpha3code = 'ATG', code = 28, currencyid = 951 WHERE id = 204;
UPDATE System.Country_Tbl SET alpha2code = 'GD', alpha3code = 'GRD', code = 308, currencyid = 951 WHERE id = 420;
UPDATE System.Country_Tbl SET alpha2code = 'LC', alpha3code = 'LCA', code = 662, currencyid = 951 WHERE id = 425;
UPDATE System.Country_Tbl SET alpha2code = 'VC', alpha3code = 'VCT', code = 670, currencyid = 951 WHERE id = 440;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000, currencyid = 951 WHERE id = 414;
UPDATE System.Country_Tbl SET alpha2code = 'AI', alpha3code = 'AIA', code = 660, currencyid = 951 WHERE id = 203;
UPDATE System.Country_Tbl SET alpha2code = 'DM', alpha3code = 'DMA', code = 212, currencyid = 951 WHERE id = 418;
UPDATE System.Country_Tbl SET alpha2code = 'MS', alpha3code = 'MSR', code = 500, currencyid = 951 WHERE id = 144;
UPDATE System.Country_Tbl SET alpha2code = 'TG', alpha3code = 'TGO', code = 768, currencyid = 952 WHERE id = 351;
UPDATE System.Country_Tbl SET alpha2code = 'NE', alpha3code = 'NER', code = 562, currencyid = 952 WHERE id = 341;
UPDATE System.Country_Tbl SET alpha2code = 'SN', alpha3code = 'SEN', code = 686, currencyid = 952 WHERE id = 433;
UPDATE System.Country_Tbl SET alpha2code = 'ML', alpha3code = 'MLI', code = 466, currencyid = 952 WHERE id = 330;
UPDATE System.Country_Tbl SET alpha2code = 'GW', alpha3code = 'GNB', code = 624, currencyid = 952 WHERE id = 324;
UPDATE System.Country_Tbl SET alpha2code = 'CI', alpha3code = 'CIV', code = 384, currencyid = 952 WHERE id = 314;
UPDATE System.Country_Tbl SET alpha2code = 'BF', alpha3code = 'BFA', code = 854, currencyid = 952 WHERE id = 306;
UPDATE System.Country_Tbl SET alpha2code = 'BJ', alpha3code = 'BEN', code = 204, currencyid = 952 WHERE id = 303;
UPDATE System.Country_Tbl SET alpha2code = 'PF', alpha3code = 'PYF', code = 258, currencyid = 953 WHERE id = 504;
UPDATE System.Country_Tbl SET alpha2code = 'NC', alpha3code = 'NCL', code = 540, currencyid = 953 WHERE id = 509;
UPDATE System.Country_Tbl SET alpha2code = 'WF', alpha3code = 'WLF', code = 876, currencyid = 953 WHERE id = 518;
UPDATE System.Country_Tbl SET alpha2code = 'YE', alpha3code = 'YEM', code = 887, currencyid = 886 WHERE id = 650;
UPDATE System.Country_Tbl SET alpha2code = 'ZA', alpha3code = 'ZAF', code = 710, currencyid = 710 WHERE id = 355;
UPDATE System.Country_Tbl SET alpha2code = 'ZM', alpha3code = 'ZMB', code = 894, currencyid = 967 WHERE id = 356;

/*---------END : ADDED CHANGE FOR SUPPORTING CURRENCY SCHEMA-------------*/



