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
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 30, id FROM System.PricePoint_Tbl WHERE amount = -1 AND countryid = 103;

INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (30, 24);
/* ========== Global Configuration for MobilePay Online- Payment Method : END========== */

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

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 26, 'CPMDemo', 'gXPRPPam3j58', 'BMMVFHwUGyfjDZk2PzMc');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 26, '-1');

-- Route VISA Card to 2C2P with country Thailand
UPDATE Client.CardAccess_Tbl SET pspid = 26, countryid = 644 WHERE clientid = 10001 AND cardid = 8;
/* ========== Global Configuration for 2C2P = ENDS ========== */


/*Myanmar*/
INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (652, 'Myanmar', 'MMK', '1000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-652, 652, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -652, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (652, 9, 'MMK');