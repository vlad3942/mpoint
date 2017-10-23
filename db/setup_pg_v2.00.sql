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

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 27, 'sandbox', '02700770202075001284', '4GkR2Hkk');
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


/* Maybank configuration in additional configuration for Malindo (sandbox/production has same set of mid and password)  */

INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'mid.8', '02700770202075001284',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 27),'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'mid.7', '02700770202075001284',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 27),'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'mid.1', '02701700290875100472',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 27),'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'pwd.8', '4GkR2Hkk',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 27),'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'pwd.7', '4GkR2Hkk',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 27),'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'pwd.1', '6sjhPN9X',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 27),'merchant');

/* Maybank configuration in additional configuration for merchant:production  */



/*
--Public bank additional config for MID based on payment method and currency
 */

INSERT INTO client.additionalproperty_tbl( property_key, property_value,merchantaccountid)VALUES ( 'mid.7.MYR', '5500003631',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28));
INSERT INTO client.additionalproperty_tbl( property_key, property_value,merchantaccountid)VALUES ( 'mid.7.HKD', '5500003798',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28));
INSERT INTO client.additionalproperty_tbl( property_key, property_value,merchantaccountid)VALUES ( 'mid.7.SGD', '5500003658',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28));
INSERT INTO client.additionalproperty_tbl( property_key, property_value,merchantaccountid)VALUES ( 'mid.7.AUD', '5500003771',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28));
INSERT INTO client.additionalproperty_tbl( property_key, property_value,merchantaccountid)VALUES ( 'mid.7.LKR', '5500003895',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28));
INSERT INTO client.additionalproperty_tbl( property_key, property_value,merchantaccountid)VALUES ( 'mid.7.CNY', '5500003909',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28));
INSERT INTO client.additionalproperty_tbl( property_key, property_value,merchantaccountid)VALUES ( 'mid.7.THB', '5500003887',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28));
INSERT INTO client.additionalproperty_tbl( property_key, property_value,merchantaccountid)VALUES ( 'mid.7.TWD', '5500004077',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28));
INSERT INTO client.additionalproperty_tbl( property_key, property_value,merchantaccountid)VALUES ( 'mid.7.SAR', '5500004492',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28));
INSERT INTO client.additionalproperty_tbl( property_key, property_value,merchantaccountid)VALUES ( 'mid.7.USD', '5500003666',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28));
INSERT INTO client.additionalproperty_tbl( property_key, property_value,merchantaccountid)VALUES ( 'mid.7.IDR', '5500004239',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28));
---
INSERT INTO client.additionalproperty_tbl( property_key, property_value,merchantaccountid)VALUES ( 'mid.8.MYR', '3300004667',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28));
INSERT INTO client.additionalproperty_tbl( property_key, property_value,merchantaccountid)VALUES ( 'mid.8.HKD', '3300004802',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28));
INSERT INTO client.additionalproperty_tbl( property_key, property_value,merchantaccountid)VALUES ( 'mid.8.SGD', '3300004675',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28));
INSERT INTO client.additionalproperty_tbl( property_key, property_value,merchantaccountid)VALUES ( 'mid.8.AUD', '3300004799',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28));
INSERT INTO client.additionalproperty_tbl( property_key, property_value,merchantaccountid)VALUES ( 'mid.8.LKR', '3300004918',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28));
INSERT INTO client.additionalproperty_tbl( property_key, property_value,merchantaccountid)VALUES ( 'mid.8.CNY', '3300004942',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28));
INSERT INTO client.additionalproperty_tbl( property_key, property_value,merchantaccountid)VALUES ( 'mid.8.THB', '3300004896',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28));
INSERT INTO client.additionalproperty_tbl( property_key, property_value,merchantaccountid)VALUES ( 'mid.8.TWD', '3300005116',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28));
INSERT INTO client.additionalproperty_tbl( property_key, property_value,merchantaccountid)VALUES ( 'mid.8.SAR', '3300005574',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28));
INSERT INTO client.additionalproperty_tbl( property_key, property_value,merchantaccountid)VALUES ( 'mid.8.USD', '3300004683',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28));
INSERT INTO client.additionalproperty_tbl( property_key, property_value,merchantaccountid)VALUES ( 'mid.8.IDR', '3300005302',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28));
/*
End of Public Bank additional configuration
 */

/*
--CCAvenue additional config for Working and access key
--Access key is same for all envs
 */
INSERT INTO client.additionalproperty_tbl( property_key, property_value,merchantaccountid)VALUES ( 'ccavenue.access.key', 'AVBV69EB32BP61VBPB',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 25));
/*--working key for all envs except PROD*/
INSERT INTO client.additionalproperty_tbl( property_key, property_value,merchantaccountid)VALUES ( 'ccavenue.working.key', '93C3C30ED0AF63F7D222ACB2A53DB025',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 25));
/*--PROD working key-*/
INSERT INTO client.additionalproperty_tbl( property_key, property_value,merchantaccountid)VALUES ( 'ccavenue.working.key', 'F9D8D501AB87FC404EAC9C5CA682C1D9',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 25));

/*
End of CCAvenue additional config
 */

/*
--WireCard additional config for enrollment mid key - same for all environments
 */

INSERT INTO client.additionalproperty_tbl( property_key, property_value,merchantaccountid)VALUES ( 'enrollment.mid.key', '33f6d473-3036-4ca5-acb5-8c64dac862d1',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 18));

/*
End of WireCard additional config
 */