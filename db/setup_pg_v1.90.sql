/* ========== CONFIGURE CARD DISCOVER START ========== */
INSERT INTO System.Card_Tbl (id, name, position, logo, minlength, maxlength, cvclength) VALUES (22, 'Discover', 20, NULL, 16, 16, 3);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 22, id FROM System.PricePoint_Tbl WHERE amount = -1;
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (22, 6011, 6011);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (22, 622126, 622925);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (22, 644, 649);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (22, 65, 65);
/* ========== CONFIGURE CARD DISCOVER END ========== */

/* ========== CONFIGURE CARD VISA CHECKOUT START ========== */
/*Adding the dummy card prefix entry for VISA checkout as a card*/
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 16, id FROM System.PricePoint_Tbl WHERE amount = -1;
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (16, 0, 0);
/* ========== CONFIGURE CARD VISA CHECKOUT END ========== */

/* ========== CONFIGURE VISA CHECKOUT START ========== */
/*START: Adding PSP entries to the PSP_Tbl table for VISA Checkout*/
INSERT INTO System.PSP_Tbl (id, name) VALUES (13, 'VISA Checkout');
/*END: Adding PSP entries to the PSP_Tbl table for VISA Checkout*/

/*START: Updating the currency codes for Colombia and Mexico*/
UPDATE System.Country_Tbl SET currency = 'COP' WHERE name LIKE 'Colombia';
UPDATE System.Country_Tbl SET currency = 'MXN' WHERE name LIKE 'Mexico';
/*END: Updating the currency codes for Colombia and Mexico*/

/*START: Adding Currency entries to the PSPCurrency_Tbl table for VISA Checkout*/
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System.Country_Tbl WHERE currency LIKE 'AED';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System.Country_Tbl WHERE currency LIKE 'ARS';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System.Country_Tbl WHERE currency LIKE 'AUD';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System.Country_Tbl WHERE currency LIKE 'BRL';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System.Country_Tbl WHERE currency LIKE 'CAD';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System.Country_Tbl WHERE currency LIKE 'CNY';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System.Country_Tbl WHERE currency LIKE 'CLP';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System.Country_Tbl WHERE currency LIKE 'HKD';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System.Country_Tbl WHERE currency LIKE 'MYR';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System.Country_Tbl WHERE currency LIKE 'NZD';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System.Country_Tbl WHERE currency LIKE 'PEN';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System.Country_Tbl WHERE currency LIKE 'SGD';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System.Country_Tbl WHERE currency LIKE 'ZAR';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System.Country_Tbl WHERE currency LIKE 'USD';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System.Country_Tbl WHERE currency LIKE 'COP';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System.Country_Tbl WHERE currency LIKE 'MXN';

/*END: Adding Currency entries to the PSPCurrency_Tbl table for VISA Checkout*/

/*START: Adding PSP to Card mapping to the PSPCard_Tbl table for VISA Checkout*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) SELECT id, 13 FROM System.Card_Tbl WHERE name = 'American Express';
INSERT INTO System.PSPCard_Tbl (cardid, pspid) SELECT id, 13 FROM System.Card_Tbl WHERE name = 'Master Card';
INSERT INTO System.PSPCard_Tbl (cardid, pspid) SELECT id, 13 FROM System.Card_Tbl WHERE name = 'VISA';
INSERT INTO System.PSPCard_Tbl (cardid, pspid) SELECT id, 13 FROM System.Card_Tbl WHERE name = 'Discover';
/*END: Adding PSP to Card mapping to the PSPCard_Tbl table for VISA Checkout*/

-- Enable support for VISA Checkout through WorldPay
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (4, 16);
--CPG
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (9, 16);
--VISA Checkout
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (13, 16);
/* ========== CONFIGURE VISA CHECKOUT END ========== */

/* ========== CONFIGURE DEMO ACCOUNT FOR VISA CHECKOUT START ========== */
-- VISA Checkout
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 13, '2X5JJ0751LFJG3EMYRMS13h-QPSi0pUet0c2zoXupm10tRL28', '2X5JJ0751LFJG3EMYRMS13h-QPSi0pUet0c2zoXupm10tRL28', '5PH9i3cNJ8ZmFK0B-xuSsMzq{8uSkO$o#GZPA+M}');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 13, '-1');
-- WorldPay
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd, stored_card) VALUES (10001, 4, 'merchant.cpm.apple.pay', 'CELLPOINT', 'oisJona1', false);
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd, stored_card) VALUES (10001, 4, 'CELLPOINTREC', 'CELLPOINTREC', 'oisJona1', true);
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 4, '254294');
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10001, 16, 4);
--CPG
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd, stored_card) VALUES (10001, 9, 'CPG', 'CPG', 'oisJona1', true);
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 9, '-1');
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10001, 16, 9);

-- Mobile Enterprise Service Bus
INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (4, 10001, 'http://localhost:10080/');
/* ========== CONFIGURE DEMO ACCOUNT FOR VISA CHECKOUT END ====== */

/* ========== CONFIGURE APPLE PAY START ========== */
/*START: Adding PSP entries to the PSP_Tbl table for Apple Pay*/
INSERT INTO System.PSP_Tbl (id, name) VALUES (14, 'Apple Pay');
/*END: Adding PSP entries to the PSP_Tbl table for Apple Pay*/

/*START: Adding Currency entries to the PSPCurrency_Tbl table for Apple Pay*/
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System.Country_Tbl WHERE currency LIKE 'AED';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System.Country_Tbl WHERE currency LIKE 'ARS';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System.Country_Tbl WHERE currency LIKE 'AUD';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System.Country_Tbl WHERE currency LIKE 'BRL';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System.Country_Tbl WHERE currency LIKE 'CAD';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System.Country_Tbl WHERE currency LIKE 'CNY';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System.Country_Tbl WHERE currency LIKE 'CLP';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System.Country_Tbl WHERE currency LIKE 'HKD';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System.Country_Tbl WHERE currency LIKE 'MYR';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System.Country_Tbl WHERE currency LIKE 'NZD';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System.Country_Tbl WHERE currency LIKE 'PEN';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System.Country_Tbl WHERE currency LIKE 'SGD';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System.Country_Tbl WHERE currency LIKE 'ZAR';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System.Country_Tbl WHERE currency LIKE 'USD';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System.Country_Tbl WHERE currency LIKE 'COP';

INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System.Country_Tbl WHERE currency LIKE 'MXN';

/*END: Adding Currency entries to the PSPCurrency_Tbl table for Apple Pay*/

/*START: Adding PSP to Card mapping to the PSPCard_Tbl table for Apple Pay*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) SELECT id, 14 FROM System.Card_Tbl WHERE name = 'American Express';
INSERT INTO System.PSPCard_Tbl (cardid, pspid) SELECT id, 14 FROM System.Card_Tbl WHERE name = 'Master Card';
INSERT INTO System.PSPCard_Tbl (cardid, pspid) SELECT id, 14 FROM System.Card_Tbl WHERE name = 'VISA';
INSERT INTO System.PSPCard_Tbl (cardid, pspid) SELECT id, 14 FROM System.Card_Tbl WHERE name = 'Discover';
/*END: Adding PSP to Card mapping to the PSPCard_Tbl table for Apple Pay*/

-- Enable support for Apple Pay through WorldPay
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (4, 15);
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (14, 15);
/* ========== CONFIGURE APPLE PAY END ========== */

/* ========== CONFIGURE DEMO ACCOUNT FOR APPLE PAY START ========== */
-- Apple Pay
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name) VALUES (10001, 14, 'merchant.cpm.apple.pay');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 14, '-1');
-- WorldPay
UPDATE Client.MerchantAccount_Tbl SET name = username WHERE pspid = 4 AND name != username;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10001, 15, 4);
/* ========== CONFIGURE DEMO ACCOUNT FOR APPLE PAY END ====== */

/* ========== ALTER TABLE FOR MERCHANT ACCOUNT TO HAVE PASSWORD OF 4000 CHARS START ====== */
ALTER TABLE Client.MerchantAccount_Tbl ALTER COLUMN passwd TYPE character varying(4000);
/* ========== ALTER TABLE FOR MERCHANT ACCOUNT TO HAVE PASSWORD OF 4000 CHARS END ====== */

/* ========== CONFIGURE CARD MASTER PASS START ========== */
INSERT INTO System.Card_Tbl (id, name, position, logo) VALUES (23, 'Master Pass', 15, NULL);
/*Adding the dummy card prefix entry for VISA checkout as a card*/
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 23, id FROM System.PricePoint_Tbl WHERE amount = -1;
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (23, 0, 0);
/* ========== CONFIGURE CARD MASTER PASS END ========== */

/* ========== CONFIGURE MASTER PASS START ========== */
/*START: Adding PSP entries to the PSP_Tbl table for VISA Checkout*/
INSERT INTO System.PSP_Tbl (id, name) VALUES (15, 'Master Pass');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 15, currency FROM System.Country_Tbl;
/*END: Adding PSP entries to the PSP_Tbl table for VISA Checkout*/

/*START: Adding PSP to Card mapping to the PSPCard_Tbl table for VISA Checkout*/
-- Master Pass as PSP.
INSERT INTO System.PSPCard_Tbl (cardid, pspid) SELECT id, 15 FROM System.Card_Tbl WHERE name = 'American Express';
INSERT INTO System.PSPCard_Tbl (cardid, pspid) SELECT id, 15 FROM System.Card_Tbl WHERE name = 'Master Card';
INSERT INTO System.PSPCard_Tbl (cardid, pspid) SELECT id, 15 FROM System.Card_Tbl WHERE name = 'VISA';
INSERT INTO System.PSPCard_Tbl (cardid, pspid) SELECT id, 15 FROM System.Card_Tbl WHERE name = 'Discover';
INSERT INTO System.PSPCard_Tbl (cardid, pspid) SELECT id, 15 FROM System.Card_Tbl WHERE name = 'Diners Club';
INSERT INTO System.PSPCard_Tbl (cardid, pspid) SELECT id, 15 FROM System.Card_Tbl WHERE name = 'Maestro';
INSERT INTO System.PSPCard_Tbl (cardid, pspid) SELECT id, 15 FROM System.Card_Tbl WHERE name = 'Master Pass';

--CPG as PSP.
INSERT INTO System.PSPCard_Tbl (cardid, pspid) SELECT id, 9 FROM System.Card_Tbl WHERE name = 'Diners Club';
INSERT INTO System.PSPCard_Tbl (cardid, pspid) SELECT id, 9 FROM System.Card_Tbl WHERE name = 'Maestro';
INSERT INTO System.PSPCard_Tbl (cardid, pspid) SELECT id, 9 FROM System.Card_Tbl WHERE name = 'Master Pass';
/*END: Adding PSP to Card mapping to the PSPCard_Tbl table for VISA Checkout*/
/* ========== CONFIGURE MASTER PASS END ========== */

/* ========== CONFIGURE DEMO ACCOUNT FOR MASTER PASS START ========== */
-- VISA Checkout
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 15, 'nastar', 'nastar', 'oisJona1');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 15, '-1');
-- Adding Static Route entries for the client EK and cards with PSP as follows
-- CPG
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10001, 23, 9);
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10001, 1, 9);
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10001, 4, 9);
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10001, 6, 9);
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10001, 7, 9);
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10001, 8, 9);
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10001, 22, 9);
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10001, 23, 15);
/* ========== CONFIGURE DEMO ACCOUNT FOR MASTER PASS START ========== */