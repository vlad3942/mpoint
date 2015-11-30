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

/* ========== CONFIGURE DATA CASH START ========== */
INSERT INTO System.PSP_Tbl (id, name) VALUES (17, 'Data Cash');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) SELECT countryid, 17, name FROM System.PSPCurrency_Tbl WHERE pspid = 4;
INSERT INTO System.PSPCard_Tbl (cardid, pspid) SELECT cardid, 17 FROM System.PSPCard_Tbl WHERE pspid = 4;
/* ========== CONFIGURE DATA CASH END ========== */

/* ========== CONFIGURE DEMO ACCOUNT FOR DATA CASH START ========== */
-- Data Cash
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, password) VALUES (10001, 17, 'TESTCellpoint_02', 'merchant.TESTCellpoint_02', '684d51b8db5870b514404055d988512a');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 17, '-1');
-- Route VISA Card to Data Cash
UPDATE Client.CardAccess_Tbl SET pspid = 17 WHERE clientid = 10001 AND cardid = 8;
/* ========== CONFIGURE DEMO ACCOUNT FOR DATA CASH END ====== */