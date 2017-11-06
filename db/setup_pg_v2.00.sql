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


/* ==================== LOG SCHEMA START ==================== */
INSERT INTO log.state_tbl (id, name, module, enabled) VALUES (2100, 'Card Tokenization Success ', 'Save Card', true);
INSERT INTO log.state_tbl (id, name, module, enabled) VALUES (2101, 'Card Tokenization Failed', 'Save Card', true);
/* ==================== LOG SCHEMA END ==================== */


INSERT INTO system.psp_tbl (id, name, system_type) VALUES (36, 'mVault', 3);
INSERT INTO system.pspcurrency_tbl (countryid, pspid, name) VALUES (200,36,'USA');
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 36, 'mVault', 'blank', 'blank');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 36, '-1');
INSERT INTO system.card_tbl (id, name, position) VALUES (35, 'mVault', -1);
INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position) VALUES (10007, 35, true, 36, null, 1, null);
INSERT INTO system.cardpricing_tbl ( pricepointid, cardid) VALUES ( -200, 35 );
INSERT INTO system.pspcard_tbl (cardid, pspid) VALUES (35, 36);
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

INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (1,'?No Specific Currency','XXX',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (2,'Afghani','AFN',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (3,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (4,'Lek','ALL',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (5,'Algerian Dinar','DZD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (6,'US Dollar','USD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (7,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (8,'Kwanza','AOA',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (9,'East Caribbean Dollar','XCD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (10,'East Caribbean Dollar','XCD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (11,'Argentine Peso','ARS',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (12,'Armenian Dram','AMD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (13,'Aruban Florin','AWG',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (14,'Australian Dollar','AUD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (15,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (16,'Azerbaijan Manat','AZN',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (17,'Bahamian Dollar','BSD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (18,'Bahraini Dinar','BHD',3);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (19,'Taka','BDT',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (20,'Barbados Dollar','BBD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (21,'Belarusian Ruble','BYN',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (22,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (23,'Belize Dollar','BZD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (24,'CFA Franc BCEAO','XOF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (25,'Bermudian Dollar','BMD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (26,'Indian Rupee','INR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (27,'Ngultrum','BTN',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (28,'Boliviano','BOB',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (29,'Mvdol','BOV',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (30,'US Dollar','USD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (31,'Convertible Mark','BAM',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (32,'Pula','BWP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (33,'Norwegian Krone','NOK',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (34,'Brazilian Real','BRL',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (35,'US Dollar','USD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (36,'Brunei Dollar','BND',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (37,'Bulgarian Lev','BGN',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (38,'CFA Franc BCEAO','XOF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (39,'Burundi Franc','BIF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (40,'Cabo Verde Escudo','CVE',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (41,'Riel','KHR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (42,'CFA Franc BEAC','XAF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (43,'Canadian Dollar','CAD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (44,'Cayman Islands Dollar','KYD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (45,'CFA Franc BEAC','XAF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (46,'CFA Franc BEAC','XAF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (47,'Chilean Peso','CLP',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (48,'Unidad de Fomento','CLF',4);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (49,'Yuan Renminbi','CNY',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (50,'Australian Dollar','AUD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (51,'Australian Dollar','AUD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (52,'Colombian Peso','COP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (53,'Unidad de Valor Real','COU',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (54,'Comorian Franc ','KMF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (55,'Congolese Franc','CDF',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (56,'CFA Franc BEAC','XAF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (57,'New Zealand Dollar','NZD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (58,'Costa Rican Colon','CRC',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (59,'CFA Franc BCEAO','XOF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (60,'Kuna','HRK',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (61,'Cuban Peso','CUP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (62,'Peso Convertible','CUC',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (63,'Netherlands Antillean Guilder','ANG',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (64,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (65,'Czech Koruna','CZK',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (66,'Danish Krone','DKK',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (67,'Djibouti Franc','DJF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (68,'East Caribbean Dollar','XCD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (69,'Dominican Peso','DOP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (70,'US Dollar','USD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (71,'Egyptian Pound','EGP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (72,'El Salvador Colon','SVC',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (73,'US Dollar','USD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (74,'CFA Franc BEAC','XAF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (75,'Nakfa','ERN',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (76,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (77,'Ethiopian Birr','ETB',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (78,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (79,'Falkland Islands Pound','FKP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (80,'Danish Krone','DKK',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (81,'Fiji Dollar','FJD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (82,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (83,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (84,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (85,'CFP Franc','XPF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (86,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (87,'CFA Franc BEAC','XAF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (88,'Dalasi','GMD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (89,'Lari','GEL',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (90,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (91,'Ghana Cedi','GHS',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (92,'Gibraltar Pound','GIP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (93,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (94,'Danish Krone','DKK',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (95,'East Caribbean Dollar','XCD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (96,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (97,'US Dollar','USD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (98,'Quetzal','GTQ',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (99,'Pound Sterling','GBP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (100,'Guinean Franc','GNF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (101,'CFA Franc BCEAO','XOF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (102,'Guyana Dollar','GYD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (103,'Gourde','HTG',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (104,'US Dollar','USD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (105,'Australian Dollar','AUD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (106,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (107,'Lempira','HNL',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (108,'Hong Kong Dollar','HKD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (109,'Forint','HUF',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (110,'Iceland Krona','ISK',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (111,'Indian Rupee','INR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (112,'Rupiah','IDR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (113,'SDR (Special Drawing Right)','XDR',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (114,'Iranian Rial','IRR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (115,'Iraqi Dinar','IQD',3);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (116,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (117,'Pound Sterling','GBP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (118,'New Israeli Sheqel','ILS',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (119,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (120,'Jamaican Dollar','JMD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (121,'Yen','JPY',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (122,'Pound Sterling','GBP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (123,'Jordanian Dinar','JOD',3);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (124,'Tenge','KZT',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (125,'Kenyan Shilling','KES',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (126,'Australian Dollar','AUD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (127,'North Korean Won','KPW',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (128,'Won','KRW',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (129,'Kuwaiti Dinar','KWD',3);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (130,'Som','KGS',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (131,'Lao Kip','LAK',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (132,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (133,'Lebanese Pound','LBP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (134,'Loti','LSL',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (135,'Rand','ZAR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (136,'Liberian Dollar','LRD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (137,'Libyan Dinar','LYD',3);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (138,'Swiss Franc','CHF',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (139,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (140,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (141,'Pataca','MOP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (142,'Denar','MKD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (143,'Malagasy Ariary','MGA',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (144,'Malawi Kwacha','MWK',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (145,'Malaysian Ringgit','MYR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (146,'Rufiyaa','MVR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (147,'CFA Franc BCEAO','XOF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (148,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (149,'US Dollar','USD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (150,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (151,'Ouguiya','MRO',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (152,'Mauritius Rupee','MUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (153,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (154,'ADB Unit of Account','XUA',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (155,'Mexican Peso','MXN',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (156,'Mexican Unidad de Inversion (UDI)','MXV',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (157,'US Dollar','USD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (158,'Moldovan Leu','MDL',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (159,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (160,'Tugrik','MNT',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (161,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (162,'East Caribbean Dollar','XCD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (163,'Moroccan Dirham','MAD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (164,'Mozambique Metical','MZN',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (165,'Kyat','MMK',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (166,'Namibia Dollar','NAD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (167,'Rand','ZAR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (168,'Australian Dollar','AUD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (169,'Nepalese Rupee','NPR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (170,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (171,'CFP Franc','XPF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (172,'New Zealand Dollar','NZD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (173,'Cordoba Oro','NIO',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (174,'CFA Franc BCEAO','XOF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (175,'Naira','NGN',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (176,'New Zealand Dollar','NZD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (177,'Australian Dollar','AUD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (178,'US Dollar','USD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (179,'Norwegian Krone','NOK',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (180,'Rial Omani','OMR',3);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (181,'Pakistan Rupee','PKR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (182,'US Dollar','USD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (183,'Balboa','PAB',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (184,'US Dollar','USD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (185,'Kina','PGK',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (186,'Guarani','PYG',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (187,'Sol','PEN',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (188,'Philippine Peso','PHP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (189,'New Zealand Dollar','NZD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (190,'Zloty','PLN',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (191,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (192,'US Dollar','USD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (193,'Qatari Rial','QAR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (194,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (195,'Romanian Leu','RON',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (196,'Russian Ruble','RUB',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (197,'Rwanda Franc','RWF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (198,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (199,'Saint Helena Pound','SHP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (200,'East Caribbean Dollar','XCD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (201,'East Caribbean Dollar','XCD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (202,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (203,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (204,'East Caribbean Dollar','XCD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (205,'Tala','WST',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (206,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (207,'Dobra','STD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (208,'Saudi Riyal','SAR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (209,'CFA Franc BCEAO','XOF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (210,'Serbian Dinar','RSD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (211,'Seychelles Rupee','SCR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (212,'Leone','SLL',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (213,'Singapore Dollar','SGD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (214,'Netherlands Antillean Guilder','ANG',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (215,'Sucre','XSU',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (216,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (217,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (218,'Solomon Islands Dollar','SBD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (219,'Somali Shilling','SOS',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (220,'Rand','ZAR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (221,'South Sudanese Pound','SSP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (222,'Euro','EUR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (223,'Sri Lanka Rupee','LKR',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (224,'Sudanese Pound','SDG',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (225,'Surinam Dollar','SRD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (226,'Norwegian Krone','NOK',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (227,'Lilangeni','SZL',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (228,'Swedish Krona','SEK',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (229,'Swiss Franc','CHF',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (230,'WIR Euro','CHE',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (231,'WIR Franc','CHW',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (232,'Syrian Pound','SYP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (233,'New Taiwan Dollar','TWD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (234,'Somoni','TJS',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (235,'Tanzanian Shilling','TZS',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (236,'Baht','THB',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (237,'US Dollar','USD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (238,'CFA Franc BCEAO','XOF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (239,'New Zealand Dollar','NZD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (240,'Pa’anga','TOP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (241,'Trinidad and Tobago Dollar','TTD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (242,'Tunisian Dinar','TND',3);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (243,'Turkish Lira','TRY',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (244,'Turkmenistan New Manat','TMT',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (245,'US Dollar','USD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (246,'Australian Dollar','AUD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (247,'Uganda Shilling','UGX',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (248,'Hryvnia','UAH',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (249,'UAE Dirham','AED',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (250,'Pound Sterling','GBP',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (251,'US Dollar','USD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (252,'US Dollar','USD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (253,'US Dollar (Next day)','USN',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (254,'Peso Uruguayo','UYU',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (255,'Uruguay Peso en Unidades Indexadas (URUIURUI)','UYI',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (256,'Uzbekistan Sum','UZS',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (257,'Vatu','VUV',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (258,'Bolívar','VEF',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (259,'Dong','VND',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (260,'US Dollar','USD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (261,'US Dollar','USD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (262,'CFP Franc','XPF',0);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (263,'Moroccan Dirham','MAD',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (264,'Yemeni Rial','YER',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (265,'Zambian Kwacha','ZMW',2);
INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (266,'Zimbabwe Dollar','ZWL',2);



UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000 , currencyid = 1 WHERE id = 0;
UPDATE System.Country_Tbl SET alpha2code = 'GB', alpha3code = 'GBR', code = 826, currencyid = 250 WHERE id = 103;
UPDATE System.Country_Tbl SET alpha2code = 'PM', alpha3code = 'SPM', code = 666, currencyid = 90 WHERE id = 145;
UPDATE System.Country_Tbl SET alpha2code = 'PS', alpha3code = 'PSE', code = 275, currencyid = 1 WHERE id = 146;
UPDATE System.Country_Tbl SET alpha2code = 'AQ', alpha3code = 'ATA', code = 10, currencyid = 1 WHERE id = 413;
UPDATE System.Country_Tbl SET alpha2code = 'SH', alpha3code = 'SHN', code = 654, currencyid = 199 WHERE id = 347;
UPDATE System.Country_Tbl SET alpha2code = 'KN', alpha3code = 'KNA', code = 659, currencyid = 200 WHERE id = 424;
UPDATE System.Country_Tbl SET alpha2code = 'IR', alpha3code = 'IRN', code = 364, currencyid = 114 WHERE id = 615;
UPDATE System.Country_Tbl SET alpha2code = 'MD', alpha3code = 'MDA', code = 498, currencyid = 158 WHERE id = 140;
UPDATE System.Country_Tbl SET alpha2code = 'MK', alpha3code = 'MKD', code = 807, currencyid = 142 WHERE id = 143;
UPDATE System.Country_Tbl SET alpha2code = 'MS', alpha3code = 'MSR', code = 500, currencyid = 200 WHERE id = 144;
UPDATE System.Country_Tbl SET alpha2code = 'RS', alpha3code = 'SRB', code = 688, currencyid = 210 WHERE id = 150;
UPDATE System.Country_Tbl SET alpha2code = 'BO', alpha3code = 'BOL', code = 68, currencyid = 29 WHERE id = 304;
UPDATE System.Country_Tbl SET alpha2code = 'CD', alpha3code = 'COD', code = 180, currencyid = 55 WHERE id = 315;
UPDATE System.Country_Tbl SET alpha2code = 'AD', alpha3code = 'AND', code = 20, currencyid = 90 WHERE id = 118;
UPDATE System.Country_Tbl SET alpha2code = 'AO', alpha3code = 'AGO', code = 24, currencyid = 8 WHERE id = 301;
UPDATE System.Country_Tbl SET alpha2code = 'AI', alpha3code = 'AIA', code = 660, currencyid = 200 WHERE id = 203;
UPDATE System.Country_Tbl SET alpha2code = 'AG', alpha3code = 'ATG', code = 28, currencyid = 200 WHERE id = 204;
UPDATE System.Country_Tbl SET alpha2code = 'AR', alpha3code = 'ARG', code = 32, currencyid = 11 WHERE id = 400;
UPDATE System.Country_Tbl SET alpha2code = 'AM', alpha3code = 'ARM', code = 51, currencyid = 12 WHERE id = 119;
UPDATE System.Country_Tbl SET alpha2code = 'AW', alpha3code = 'ABW', code = 533, currencyid = 13 WHERE id = 401;
UPDATE System.Country_Tbl SET alpha2code = 'AU', alpha3code = 'AUS', code = 36, currencyid = 105 WHERE id = 500;
UPDATE System.Country_Tbl SET alpha2code = 'AT', alpha3code = 'AUT', code = 40, currencyid = 90 WHERE id = 114;
UPDATE System.Country_Tbl SET alpha2code = 'AZ', alpha3code = 'AZE', code = 31, currencyid = 16 WHERE id = 611;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000 , currencyid = 137 WHERE id = 338;
UPDATE System.Country_Tbl SET alpha2code = 'BS', alpha3code = 'BHS', code = 44, currencyid = 17 WHERE id = 215;
UPDATE System.Country_Tbl SET alpha2code = 'BH', alpha3code = 'BHR', code = 48, currencyid = 18 WHERE id = 601;
UPDATE System.Country_Tbl SET alpha2code = 'BD', alpha3code = 'BGD', code = 50, currencyid = 19 WHERE id = 302;
UPDATE System.Country_Tbl SET alpha2code = 'BB', alpha3code = 'BRB', code = 52, currencyid = 20 WHERE id = 205;
UPDATE System.Country_Tbl SET alpha2code = 'BE', alpha3code = 'BEL', code = 56, currencyid = 90 WHERE id = 111;
UPDATE System.Country_Tbl SET alpha2code = 'BZ', alpha3code = 'BLZ', code = 84, currencyid = 23 WHERE id = 402;
UPDATE System.Country_Tbl SET alpha2code = 'BJ', alpha3code = 'BEN', code = 204, currencyid = 238 WHERE id = 303;
UPDATE System.Country_Tbl SET alpha2code = 'BM', alpha3code = 'BMU', code = 60, currencyid = 25 WHERE id = 214;
UPDATE System.Country_Tbl SET alpha2code = 'BT', alpha3code = 'BTN', code = 64, currencyid = 27 WHERE id = 612;
UPDATE System.Country_Tbl SET alpha2code = 'BA', alpha3code = 'BIH', code = 70, currencyid = 31 WHERE id = 121;
UPDATE System.Country_Tbl SET alpha2code = 'BW', alpha3code = 'BWA', code = 72, currencyid = 32 WHERE id = 305;
UPDATE System.Country_Tbl SET alpha2code = 'BV', alpha3code = 'BVT', code = 74, currencyid = 33 WHERE id = 416;
UPDATE System.Country_Tbl SET alpha2code = 'BR', alpha3code = 'BRA', code = 76, currencyid = 34 WHERE id = 403;
UPDATE System.Country_Tbl SET alpha2code = 'BN', alpha3code = 'BRN', code = 96, currencyid = 36 WHERE id = 501;
UPDATE System.Country_Tbl SET alpha2code = 'BG', alpha3code = 'BGR', code = 100, currencyid = 37 WHERE id = 122;
UPDATE System.Country_Tbl SET alpha2code = 'BF', alpha3code = 'BFA', code = 854, currencyid = 238 WHERE id = 306;
UPDATE System.Country_Tbl SET alpha2code = 'BI', alpha3code = 'BDI', code = 108, currencyid = 39 WHERE id = 307;
UPDATE System.Country_Tbl SET alpha2code = 'KH', alpha3code = 'KHM', code = 116, currencyid = 41 WHERE id = 613;
UPDATE System.Country_Tbl SET alpha2code = 'CM', alpha3code = 'CMR', code = 120, currencyid = 42 WHERE id = 308;
UPDATE System.Country_Tbl SET alpha2code = 'CA', alpha3code = 'CAN', code = 124, currencyid = 43 WHERE id = 202;
UPDATE System.Country_Tbl SET alpha2code = 'CV', alpha3code = 'CPV', code = 132, currencyid = 40 WHERE id = 309;
UPDATE System.Country_Tbl SET alpha2code = 'KY', alpha3code = 'CYM', code = 136, currencyid = 44 WHERE id = 207;
UPDATE System.Country_Tbl SET alpha2code = 'CF', alpha3code = 'CAF', code = 140, currencyid = 42 WHERE id = 310;
UPDATE System.Country_Tbl SET alpha2code = 'TD', alpha3code = 'TCD', code = 148, currencyid = 42 WHERE id = 311;
UPDATE System.Country_Tbl SET alpha2code = 'CL', alpha3code = 'CHL', code = 152, currencyid = 47 WHERE id = 404;
UPDATE System.Country_Tbl SET alpha2code = 'CN', alpha3code = 'CHN', code = 156, currencyid = 49 WHERE id = 609;
UPDATE System.Country_Tbl SET alpha2code = 'CX', alpha3code = 'CXR', code = 162, currencyid = 105 WHERE id = 508;
UPDATE System.Country_Tbl SET alpha2code = 'CC', alpha3code = 'CCK', code = 166, currencyid = 105 WHERE id = 507;
UPDATE System.Country_Tbl SET alpha2code = 'CO', alpha3code = 'COL', code = 170, currencyid = 52 WHERE id = 405;
UPDATE System.Country_Tbl SET alpha2code = 'KM', alpha3code = 'COM', code = 174, currencyid = 54 WHERE id = 312;
UPDATE System.Country_Tbl SET alpha2code = 'CG', alpha3code = 'COG', code = 178, currencyid = 42 WHERE id = 313;
UPDATE System.Country_Tbl SET alpha2code = 'CK', alpha3code = 'COK', code = 184, currencyid = 172 WHERE id = 502;
UPDATE System.Country_Tbl SET alpha2code = 'CR', alpha3code = 'CRI', code = 188, currencyid = 58 WHERE id = 406;
UPDATE System.Country_Tbl SET alpha2code = 'CI', alpha3code = 'CIV', code = 384, currencyid = 238 WHERE id = 314;
UPDATE System.Country_Tbl SET alpha2code = 'HR', alpha3code = 'HRV', code = 191, currencyid = 60 WHERE id = 123;
UPDATE System.Country_Tbl SET alpha2code = 'CU', alpha3code = 'CUB', code = 192, currencyid = 61 WHERE id = 208;
UPDATE System.Country_Tbl SET alpha2code = 'CY', alpha3code = 'CYP', code = 196, currencyid = 90 WHERE id = 124;
UPDATE System.Country_Tbl SET alpha2code = 'CZ', alpha3code = 'CZE', code = 203, currencyid = 65 WHERE id = 125;
UPDATE System.Country_Tbl SET alpha2code = 'DK', alpha3code = 'DNK', code = 208, currencyid = 66 WHERE id = 100;
UPDATE System.Country_Tbl SET alpha2code = 'DJ', alpha3code = 'DJI', code = 262, currencyid = 67 WHERE id = 316;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000 , currencyid = 42 WHERE id = 357;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000 , currencyid = 200 WHERE id = 414;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000 , currencyid = 172 WHERE id = 417;
UPDATE System.Country_Tbl SET alpha2code = 'DM', alpha3code = 'DMA', code = 212, currencyid = 200 WHERE id = 418;
UPDATE System.Country_Tbl SET alpha2code = 'DO', alpha3code = 'DOM', code = 214, currencyid = 69 WHERE id = 209;
UPDATE System.Country_Tbl SET alpha2code = 'TZ', alpha3code = 'TZA', code = 834, currencyid = 235 WHERE id = 353;
UPDATE System.Country_Tbl SET alpha2code = 'FK', alpha3code = 'FLK', code = 238, currencyid = 79 WHERE id = 419;
UPDATE System.Country_Tbl SET alpha2code = 'LC', alpha3code = 'LCA', code = 662, currencyid = 200 WHERE id = 425;
UPDATE System.Country_Tbl SET alpha2code = 'PN', alpha3code = 'PCN', code = 612, currencyid = 172 WHERE id = 430;
UPDATE System.Country_Tbl SET alpha2code = 'SX', alpha3code = 'SXM', code = 534, currencyid = 214 WHERE id = 436;
UPDATE System.Country_Tbl SET alpha2code = 'VC', alpha3code = 'VCT', code = 670, currencyid = 200 WHERE id = 440;
UPDATE System.Country_Tbl SET alpha2code = 'VE', alpha3code = 'VEN', code = 862, currencyid = 258 WHERE id = 441;
UPDATE System.Country_Tbl SET alpha2code = 'EC', alpha3code = 'ECU', code = 218, currencyid = 35 WHERE id = 407;
UPDATE System.Country_Tbl SET alpha2code = 'EG', alpha3code = 'EGY', code = 818, currencyid = 71 WHERE id = 317;
UPDATE System.Country_Tbl SET alpha2code = 'SV', alpha3code = 'SLV', code = 222, currencyid = 35 WHERE id = 408;
UPDATE System.Country_Tbl SET alpha2code = 'GQ', alpha3code = 'GNQ', code = 226, currencyid = 42 WHERE id = 318;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000 , currencyid = 90 WHERE id = 409;
UPDATE System.Country_Tbl SET alpha2code = 'ER', alpha3code = 'ERI', code = 232, currencyid = 75 WHERE id = 337;
UPDATE System.Country_Tbl SET alpha2code = 'EE', alpha3code = 'EST', code = 233, currencyid = 90 WHERE id = 126;
UPDATE System.Country_Tbl SET alpha2code = 'ET', alpha3code = 'ETH', code = 231, currencyid = 77 WHERE id = 319;
UPDATE System.Country_Tbl SET alpha2code = 'FO', alpha3code = 'FRO', code = 234, currencyid = 66 WHERE id = 127;
UPDATE System.Country_Tbl SET alpha2code = 'FJ', alpha3code = 'FJI', code = 242, currencyid = 81 WHERE id = 503;
UPDATE System.Country_Tbl SET alpha2code = 'FI', alpha3code = 'FIN', code = 246, currencyid = 90 WHERE id = 104;
UPDATE System.Country_Tbl SET alpha2code = 'FR', alpha3code = 'FRA', code = 250, currencyid = 90 WHERE id = 108;
UPDATE System.Country_Tbl SET alpha2code = 'GF', alpha3code = 'GUF', code = 254, currencyid = 90 WHERE id = 421;
UPDATE System.Country_Tbl SET alpha2code = 'PF', alpha3code = 'PYF', code = 258, currencyid = 262 WHERE id = 504;
UPDATE System.Country_Tbl SET alpha2code = 'GA', alpha3code = 'GAB', code = 266, currencyid = 42 WHERE id = 320;
UPDATE System.Country_Tbl SET alpha2code = 'GM', alpha3code = 'GMB', code = 270, currencyid = 88 WHERE id = 321;
UPDATE System.Country_Tbl SET alpha2code = 'GE', alpha3code = 'GEO', code = 268, currencyid = 250 WHERE id = 128;
UPDATE System.Country_Tbl SET alpha2code = 'DE', alpha3code = 'DEU', code = 276, currencyid = 90 WHERE id = 115;
UPDATE System.Country_Tbl SET alpha2code = 'GH', alpha3code = 'GHA', code = 288, currencyid = 91 WHERE id = 322;
UPDATE System.Country_Tbl SET alpha2code = 'GI', alpha3code = 'GIB', code = 292, currencyid = 92 WHERE id = 129;
UPDATE System.Country_Tbl SET alpha2code = 'GR', alpha3code = 'GRC', code = 300, currencyid = 90 WHERE id = 105;
UPDATE System.Country_Tbl SET alpha2code = 'GL', alpha3code = 'GRL', code = 304, currencyid = 66 WHERE id = 130;
UPDATE System.Country_Tbl SET alpha2code = 'GD', alpha3code = 'GRD', code = 308, currencyid = 200 WHERE id = 420;
UPDATE System.Country_Tbl SET alpha2code = 'GP', alpha3code = 'GLP', code = 312, currencyid = 90 WHERE id = 210;
UPDATE System.Country_Tbl SET alpha2code = 'GU', alpha3code = 'GUM', code = 316, currencyid = 35 WHERE id = 627;
UPDATE System.Country_Tbl SET alpha2code = 'GT', alpha3code = 'GTM', code = 320, currencyid = 98 WHERE id = 410;
UPDATE System.Country_Tbl SET alpha2code = 'GN', alpha3code = 'GIN', code = 324, currencyid = 100 WHERE id = 323;
UPDATE System.Country_Tbl SET alpha2code = 'GW', alpha3code = 'GNB', code = 624, currencyid = 238 WHERE id = 324;
UPDATE System.Country_Tbl SET alpha2code = 'GY', alpha3code = 'GUY', code = 328, currencyid = 102 WHERE id = 411;
UPDATE System.Country_Tbl SET alpha2code = 'HT', alpha3code = 'HTI', code = 332, currencyid = 35 WHERE id = 211;
UPDATE System.Country_Tbl SET alpha2code = 'HN', alpha3code = 'HND', code = 340, currencyid = 107 WHERE id = 412;
UPDATE System.Country_Tbl SET alpha2code = 'HK', alpha3code = 'HKG', code = 344, currencyid = 108 WHERE id = 614;
UPDATE System.Country_Tbl SET alpha2code = 'HU', alpha3code = 'HUN', code = 348, currencyid = 109 WHERE id = 131;
UPDATE System.Country_Tbl SET alpha2code = 'IS', alpha3code = 'ISL', code = 352, currencyid = 110 WHERE id = 132;
UPDATE System.Country_Tbl SET alpha2code = 'IN', alpha3code = 'IND', code = 356, currencyid = 26 WHERE id = 603;
UPDATE System.Country_Tbl SET alpha2code = 'ID', alpha3code = 'IDN', code = 360, currencyid = 112 WHERE id = 505;
UPDATE System.Country_Tbl SET alpha2code = 'IQ', alpha3code = 'IRQ', code = 368, currencyid = 115 WHERE id = 628;
UPDATE System.Country_Tbl SET alpha2code = 'IE', alpha3code = 'IRL', code = 372, currencyid = 90 WHERE id = 133;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000 , currencyid = 214 WHERE id = 415;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000 , currencyid = 249 WHERE id = 600;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000 , currencyid = 249 WHERE id = 602;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000 , currencyid = 165 WHERE id = 625;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000 , currencyid = 127 WHERE id = 631;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000 , currencyid = 128 WHERE id = 632;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000 , currencyid = 124 WHERE id = 633;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000 , currencyid = 233 WHERE id = 646;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000 , currencyid = 259 WHERE id = 649;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000 , currencyid = 90 WHERE id = 148;
UPDATE System.Country_Tbl SET alpha2code = 'IM', alpha3code = 'IMN', code = 833, currencyid = 250 WHERE id = 134;
UPDATE System.Country_Tbl SET alpha2code = 'IL', alpha3code = 'ISR', code = 376, currencyid = 118 WHERE id = 106;
UPDATE System.Country_Tbl SET alpha2code = 'IT', alpha3code = 'ITA', code = 380, currencyid = 90 WHERE id = 107;
UPDATE System.Country_Tbl SET alpha2code = 'JM', alpha3code = 'JAM', code = 388, currencyid = 120 WHERE id = 212;
UPDATE System.Country_Tbl SET alpha2code = 'JP', alpha3code = 'JPN', code = 392, currencyid = 121 WHERE id = 616;
UPDATE System.Country_Tbl SET alpha2code = 'JO', alpha3code = 'JOR', code = 400, currencyid = 123 WHERE id = 617;
UPDATE System.Country_Tbl SET alpha2code = 'KZ', alpha3code = 'KAZ', code = 398, currencyid = 124 WHERE id = 618;
UPDATE System.Country_Tbl SET alpha2code = 'VA', alpha3code = 'VAT', code = 336, currencyid = 90 WHERE id = 156;
UPDATE System.Country_Tbl SET alpha2code = 'RE', alpha3code = 'REU', code = 638, currencyid = 90 WHERE id = 343;
UPDATE System.Country_Tbl SET alpha2code = 'WF', alpha3code = 'WLF', code = 876, currencyid = 262 WHERE id = 518;
UPDATE System.Country_Tbl SET alpha2code = 'QA', alpha3code = 'QAT', code = 634, currencyid = 193 WHERE id = 606;
UPDATE System.Country_Tbl SET alpha2code = 'RU', alpha3code = 'RUS', code = 643, currencyid = 196 WHERE id = 607;
UPDATE System.Country_Tbl SET alpha2code = 'SA', alpha3code = 'SAU', code = 682, currencyid = 208 WHERE id = 608;
UPDATE System.Country_Tbl SET alpha2code = 'LA', alpha3code = 'LAO', code = 418, currencyid = 131 WHERE id = 620;
UPDATE System.Country_Tbl SET alpha2code = 'MO', alpha3code = 'MAC', code = 446, currencyid = 141 WHERE id = 636;
UPDATE System.Country_Tbl SET alpha2code = 'KE', alpha3code = 'KEN', code = 404, currencyid = 125 WHERE id = 325;
UPDATE System.Country_Tbl SET alpha2code = 'KI', alpha3code = 'KIR', code = 296, currencyid = 105 WHERE id = 630;
UPDATE System.Country_Tbl SET alpha2code = 'KW', alpha3code = 'KWT', code = 414, currencyid = 129 WHERE id = 604;
UPDATE System.Country_Tbl SET alpha2code = 'KG', alpha3code = 'KGZ', code = 417, currencyid = 130 WHERE id = 619;
UPDATE System.Country_Tbl SET alpha2code = 'LB', alpha3code = 'LBN', code = 422, currencyid = 133 WHERE id = 621;
UPDATE System.Country_Tbl SET alpha2code = 'LS', alpha3code = 'LSO', code = 426, currencyid = 134 WHERE id = 326;
UPDATE System.Country_Tbl SET alpha2code = 'LR', alpha3code = 'LBR', code = 430, currencyid = 136 WHERE id = 327;
UPDATE System.Country_Tbl SET alpha2code = 'LI', alpha3code = 'LIE', code = 438, currencyid = 138 WHERE id = 136;
UPDATE System.Country_Tbl SET alpha2code = 'LU', alpha3code = 'LUX', code = 442, currencyid = 90 WHERE id = 138;
UPDATE System.Country_Tbl SET alpha2code = 'MO', alpha3code = 'MAC', code = 446, currencyid = 141 WHERE id = 622;
UPDATE System.Country_Tbl SET alpha2code = 'MG', alpha3code = 'MDG', code = 450, currencyid = 143 WHERE id = 328;
UPDATE System.Country_Tbl SET alpha2code = 'MW', alpha3code = 'MWI', code = 454, currencyid = 144 WHERE id = 329;
UPDATE System.Country_Tbl SET alpha2code = 'MY', alpha3code = 'MYS', code = 458, currencyid = 145 WHERE id = 638;
UPDATE System.Country_Tbl SET alpha2code = 'MV', alpha3code = 'MDV', code = 462, currencyid = 146 WHERE id = 623;
UPDATE System.Country_Tbl SET alpha2code = 'ML', alpha3code = 'MLI', code = 466, currencyid = 238 WHERE id = 330;
UPDATE System.Country_Tbl SET alpha2code = 'MT', alpha3code = 'MLT', code = 470, currencyid = 90 WHERE id = 139;
UPDATE System.Country_Tbl SET alpha2code = 'MH', alpha3code = 'MHL', code = 584, currencyid = 35 WHERE id = 635;
UPDATE System.Country_Tbl SET alpha2code = 'MQ', alpha3code = 'MTQ', code = 474, currencyid = 90 WHERE id = 426;
UPDATE System.Country_Tbl SET alpha2code = 'MR', alpha3code = 'MRT', code = 478, currencyid = 151 WHERE id = 331;
UPDATE System.Country_Tbl SET alpha2code = 'MU', alpha3code = 'MUS', code = 480, currencyid = 152 WHERE id = 332;
UPDATE System.Country_Tbl SET alpha2code = 'YT', alpha3code = 'MYT', code = 175, currencyid = 90 WHERE id = 339;
UPDATE System.Country_Tbl SET alpha2code = 'MX', alpha3code = 'MEX', code = 484, currencyid = 155 WHERE id = 201;
UPDATE System.Country_Tbl SET alpha2code = 'MC', alpha3code = 'MCO', code = 492, currencyid = 90 WHERE id = 141;
UPDATE System.Country_Tbl SET alpha2code = 'MN', alpha3code = 'MNG', code = 496, currencyid = 160 WHERE id = 624;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000 , currencyid = 90 WHERE id = 629;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000 , currencyid = 35 WHERE id = 216;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000 , currencyid = 35 WHERE id = 217;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000 , currencyid = 35 WHERE id = 423;
UPDATE System.Country_Tbl SET alpha2code = '', alpha3code = '', code = 000 , currencyid = 35 WHERE id = 626;
UPDATE System.Country_Tbl SET alpha2code = 'AF', alpha3code = 'AFG', code = 4, currencyid = 2 WHERE id = 116;
UPDATE System.Country_Tbl SET alpha2code = 'AL', alpha3code = 'ALB', code = 8, currencyid = 4 WHERE id = 117;
UPDATE System.Country_Tbl SET alpha2code = 'DZ', alpha3code = 'DZA', code = 12, currencyid = 5 WHERE id = 300;
UPDATE System.Country_Tbl SET alpha2code = 'AS', alpha3code = 'ASM', code = 16, currencyid = 35 WHERE id = 213;
UPDATE System.Country_Tbl SET alpha2code = 'BY', alpha3code = 'BLR', code = 112, currencyid = 21 WHERE id = 120;
UPDATE System.Country_Tbl SET alpha2code = 'PK', alpha3code = 'PAK', code = 586, currencyid = 181 WHERE id = 610;
UPDATE System.Country_Tbl SET alpha2code = 'LT', alpha3code = 'LTU', code = 440, currencyid = 90 WHERE id = 137;
UPDATE System.Country_Tbl SET alpha2code = 'LV', alpha3code = 'LVA', code = 428, currencyid = 90 WHERE id = 135;
UPDATE System.Country_Tbl SET alpha2code = 'ST', alpha3code = 'STP', code = 678, currencyid = 207 WHERE id = 349;
UPDATE System.Country_Tbl SET alpha2code = 'SN', alpha3code = 'SEN', code = 686, currencyid = 238 WHERE id = 433;
UPDATE System.Country_Tbl SET alpha2code = 'SC', alpha3code = 'SYC', code = 690, currencyid = 211 WHERE id = 345;
UPDATE System.Country_Tbl SET alpha2code = 'SL', alpha3code = 'SLE', code = 694, currencyid = 212 WHERE id = 348;
UPDATE System.Country_Tbl SET alpha2code = 'SG', alpha3code = 'SGP', code = 702, currencyid = 213 WHERE id = 642;
UPDATE System.Country_Tbl SET alpha2code = 'SK', alpha3code = 'SVK', code = 703, currencyid = 90 WHERE id = 152;
UPDATE System.Country_Tbl SET alpha2code = 'ZM', alpha3code = 'ZMB', code = 894, currencyid = 265 WHERE id = 356;
UPDATE System.Country_Tbl SET alpha2code = 'ZW', alpha3code = 'ZWE', code = 716, currencyid = 35 WHERE id = 358;
UPDATE System.Country_Tbl SET alpha2code = 'ME', alpha3code = 'MNE', code = 499, currencyid = 90 WHERE id = 142;
UPDATE System.Country_Tbl SET alpha2code = 'MA', alpha3code = 'MAR', code = 504, currencyid = 263 WHERE id = 333;
UPDATE System.Country_Tbl SET alpha2code = 'MZ', alpha3code = 'MOZ', code = 508, currencyid = 164 WHERE id = 334;
UPDATE System.Country_Tbl SET alpha2code = 'NA', alpha3code = 'NAM', code = 516, currencyid = 166 WHERE id = 340;
UPDATE System.Country_Tbl SET alpha2code = 'NR', alpha3code = 'NRU', code = 520, currencyid = 105 WHERE id = 511;
UPDATE System.Country_Tbl SET alpha2code = 'NP', alpha3code = 'NPL', code = 524, currencyid = 169 WHERE id = 639;
UPDATE System.Country_Tbl SET alpha2code = 'NL', alpha3code = 'NLD', code = 528, currencyid = 90 WHERE id = 110;
UPDATE System.Country_Tbl SET alpha2code = 'NC', alpha3code = 'NCL', code = 540, currencyid = 262 WHERE id = 509;
UPDATE System.Country_Tbl SET alpha2code = 'NZ', alpha3code = 'NZL', code = 554, currencyid = 172 WHERE id = 513;
UPDATE System.Country_Tbl SET alpha2code = 'NI', alpha3code = 'NIC', code = 558, currencyid = 173 WHERE id = 427;
UPDATE System.Country_Tbl SET alpha2code = 'NE', alpha3code = 'NER', code = 562, currencyid = 238 WHERE id = 341;
UPDATE System.Country_Tbl SET alpha2code = 'US', alpha3code = 'USA', code = 840, currencyid = 35 WHERE id = 200;
UPDATE System.Country_Tbl SET alpha2code = 'VG', alpha3code = 'VGB', code = 92, currencyid = 35 WHERE id = 206;
UPDATE System.Country_Tbl SET alpha2code = 'VI', alpha3code = 'VIR', code = 850, currencyid = 35 WHERE id = 442;
UPDATE System.Country_Tbl SET alpha2code = 'FM', alpha3code = 'FSM', code = 583, currencyid = 35 WHERE id = 506;
UPDATE System.Country_Tbl SET alpha2code = 'SH', alpha3code = 'SHN', code = 654, currencyid = 250 WHERE id = 335;
UPDATE System.Country_Tbl SET alpha2code = 'GS', alpha3code = 'SGS', code = 239, currencyid = 250 WHERE id = 422;
UPDATE System.Country_Tbl SET alpha2code = 'NG', alpha3code = 'NGA', code = 566, currencyid = 175 WHERE id = 342;
UPDATE System.Country_Tbl SET alpha2code = 'NU', alpha3code = 'NIU', code = 570, currencyid = 172 WHERE id = 512;
UPDATE System.Country_Tbl SET alpha2code = 'NF', alpha3code = 'NFK', code = 574, currencyid = 105 WHERE id = 510;
UPDATE System.Country_Tbl SET alpha2code = 'MP', alpha3code = 'MNP', code = 580, currencyid = 35 WHERE id = 637;
UPDATE System.Country_Tbl SET alpha2code = 'NO', alpha3code = 'NOR', code = 578, currencyid = 33 WHERE id = 102;
UPDATE System.Country_Tbl SET alpha2code = 'OM', alpha3code = 'OMN', code = 512, currencyid = 180 WHERE id = 605;
UPDATE System.Country_Tbl SET alpha2code = 'PW', alpha3code = 'PLW', code = 585, currencyid = 35 WHERE id = 641;
UPDATE System.Country_Tbl SET alpha2code = 'PA', alpha3code = 'PAN', code = 591, currencyid = 35 WHERE id = 428;
UPDATE System.Country_Tbl SET alpha2code = 'PY', alpha3code = 'PRY', code = 600, currencyid = 186 WHERE id = 432;
UPDATE System.Country_Tbl SET alpha2code = 'PE', alpha3code = 'PER', code = 604, currencyid = 187 WHERE id = 429;
UPDATE System.Country_Tbl SET alpha2code = 'PH', alpha3code = 'PHL', code = 608, currencyid = 188 WHERE id = 640;
UPDATE System.Country_Tbl SET alpha2code = 'PL', alpha3code = 'POL', code = 616, currencyid = 190 WHERE id = 112;
UPDATE System.Country_Tbl SET alpha2code = 'PT', alpha3code = 'PRT', code = 620, currencyid = 90 WHERE id = 147;
UPDATE System.Country_Tbl SET alpha2code = 'PR', alpha3code = 'PRI', code = 630, currencyid = 35 WHERE id = 431;
UPDATE System.Country_Tbl SET alpha2code = 'RO', alpha3code = 'ROU', code = 642, currencyid = 195 WHERE id = 149;
UPDATE System.Country_Tbl SET alpha2code = 'RW', alpha3code = 'RWA', code = 646, currencyid = 197 WHERE id = 344;
UPDATE System.Country_Tbl SET alpha2code = 'WS', alpha3code = 'WSM', code = 882, currencyid = 205 WHERE id = 519;
UPDATE System.Country_Tbl SET alpha2code = 'SM', alpha3code = 'SMR', code = 674, currencyid = 90 WHERE id = 151;
UPDATE System.Country_Tbl SET alpha2code = 'SI', alpha3code = 'SVN', code = 705, currencyid = 90 WHERE id = 153;
UPDATE System.Country_Tbl SET alpha2code = 'SB', alpha3code = 'SLB', code = 90, currencyid = 218 WHERE id = 514;
UPDATE System.Country_Tbl SET alpha2code = 'SO', alpha3code = 'SOM', code = 706, currencyid = 219 WHERE id = 434;
UPDATE System.Country_Tbl SET alpha2code = 'ZA', alpha3code = 'ZAF', code = 710, currencyid = 135 WHERE id = 355;
UPDATE System.Country_Tbl SET alpha2code = 'ES', alpha3code = 'ESP', code = 724, currencyid = 90 WHERE id = 113;
UPDATE System.Country_Tbl SET alpha2code = 'LK', alpha3code = 'LKA', code = 144, currencyid = 223 WHERE id = 634;
UPDATE System.Country_Tbl SET alpha2code = 'SD', alpha3code = 'SDN', code = 729, currencyid = 224 WHERE id = 346;
UPDATE System.Country_Tbl SET alpha2code = 'SR', alpha3code = 'SUR', code = 740, currencyid = 225 WHERE id = 435;
UPDATE System.Country_Tbl SET alpha2code = 'SZ', alpha3code = 'SWZ', code = 748, currencyid = 227 WHERE id = 350;
UPDATE System.Country_Tbl SET alpha2code = 'SE', alpha3code = 'SWE', code = 752, currencyid = 228 WHERE id = 101;
UPDATE System.Country_Tbl SET alpha2code = 'CH', alpha3code = 'CHE', code = 756, currencyid = 231 WHERE id = 109;
UPDATE System.Country_Tbl SET alpha2code = 'SY', alpha3code = 'SYR', code = 760, currencyid = 232 WHERE id = 643;
UPDATE System.Country_Tbl SET alpha2code = 'TH', alpha3code = 'THA', code = 764, currencyid = 236 WHERE id = 644;
UPDATE System.Country_Tbl SET alpha2code = 'TG', alpha3code = 'TGO', code = 768, currencyid = 238 WHERE id = 351;
UPDATE System.Country_Tbl SET alpha2code = 'TO', alpha3code = 'TON', code = 776, currencyid = 240 WHERE id = 515;
UPDATE System.Country_Tbl SET alpha2code = 'TT', alpha3code = 'TTO', code = 780, currencyid = 241 WHERE id = 438;
UPDATE System.Country_Tbl SET alpha2code = 'TN', alpha3code = 'TUN', code = 788, currencyid = 242 WHERE id = 352;
UPDATE System.Country_Tbl SET alpha2code = 'TR', alpha3code = 'TUR', code = 792, currencyid = 243 WHERE id = 154;
UPDATE System.Country_Tbl SET alpha2code = 'TM', alpha3code = 'TKM', code = 795, currencyid = 244 WHERE id = 645;
UPDATE System.Country_Tbl SET alpha2code = 'TC', alpha3code = 'TCA', code = 796, currencyid = 35 WHERE id = 437;
UPDATE System.Country_Tbl SET alpha2code = 'TV', alpha3code = 'TUV', code = 798, currencyid = 105 WHERE id = 516;
UPDATE System.Country_Tbl SET alpha2code = 'UG', alpha3code = 'UGA', code = 800, currencyid = 247 WHERE id = 354;
UPDATE System.Country_Tbl SET alpha2code = 'UA', alpha3code = 'UKR', code = 804, currencyid = 248 WHERE id = 155;
UPDATE System.Country_Tbl SET alpha2code = 'AE', alpha3code = 'ARE', code = 784, currencyid = 249 WHERE id = 647;
UPDATE System.Country_Tbl SET alpha2code = 'UM', alpha3code = 'UMI', code = 581, currencyid = 35 WHERE id = 218;
UPDATE System.Country_Tbl SET alpha2code = 'UY', alpha3code = 'URY', code = 858, currencyid = 254 WHERE id = 439;
UPDATE System.Country_Tbl SET alpha2code = 'UZ', alpha3code = 'UZB', code = 860, currencyid = 256 WHERE id = 648;
UPDATE System.Country_Tbl SET alpha2code = 'VU', alpha3code = 'VUT', code = 548, currencyid = 257 WHERE id = 517;
UPDATE System.Country_Tbl SET alpha2code = 'EH', alpha3code = 'ESH', code = 732, currencyid = 263 WHERE id = 336;
UPDATE System.Country_Tbl SET alpha2code = 'YE', alpha3code = 'YEM', code = 887, currencyid = 264 WHERE id = 650;
/*---------END : ADDED CHANGE FOR SUPPORTING CURRENCY SCHEMA-------------*/



/* Update process type 2's name from Bank to Acquirer*/

UPDATE system.processortype_tbl SET name = 'Acquirer' WHERE id = 2;

/* ========== CONFIGURE NETS START ========== */
/*START: Adding PSP entries to the PSP_Tbl table for NETS*/

INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (35, 'NETS',2);

/*END: Adding PSP entries to the PSP_Tbl table for NETS*/

/*START: Adding Currency entries to the PSPCurrency_Tbl table for NETS*/


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
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CARD_ACCEPTOR_BUSINESS_CODE', '4816', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CARD_ACCEPTOR_IDENTIFICATION_CODE', '1978551', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CARD_ACCEPTOR_NAME', 'NETS TESTER', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CARD_ACCEPTOR_ADDRESS', 'Boulevard 4', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CARD_ACCEPTOR_CITY', 'Broby', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CARD_ACCEPTOR_ZIP', '3266', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CARD_ACCEPTOR_REGION', 'DK', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CARD_ACCEPTOR_COUNTRY', 'DNK', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('POS_DATA_CODE', 'K00500K00130', 206, 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CARD_ACCEPTOR_TERMINAL_ID', '208752', 206, 'merchant');
/*====================== Test Data END =========================*/


/*=========================PayTabs===================================== */

INSERT INTO System.PSP_Tbl (id, name) VALUES (38, 'PayTabs');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (602,38,'AED');
INSERT INTO System.PspCard_Tbl(cardid, pspid) VALUES (31, 38);
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 38, 'PayTabs', 'Arun123', 'zoVCrg1wOzCN22cXIZt5YM3TnAKoA5paulNWBOtqo6eq8roRqSWoEZh1A2qb7PlCa9yMX2cm8qMgSb7i34HH3ZID19P9YaL9jkVh');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 38, '-1');
UPDATE Client.CardAccess_Tbl SET pspid = 38, countryid = 602 WHERE clientid = 10007 AND cardid = 31;



/* END */
