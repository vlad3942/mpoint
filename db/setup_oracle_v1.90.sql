/* ========== CONFIGURE URL TYPE FOR THE MOBILE ENTERPRISE SERVICE BUS START ========== */
INSERT INTO System_Ownr.URLType_Tbl (id, name) VALUES (4, 'Mobile Enterprise Service Bus');

-- Mobile Enterprise Service Bus
INSERT INTO Client_Ownr.URL_Tbl (urltypeid, clientid, url) SELECT 4, id, 'http://localhost:9000/' FROM Client_Ownr.Client_Tbl;
/* ========== CONFIGURE URL TYPE FOR THE MOBILE ENTERPRISE SERVICE BUS START ========== */

/* ========== CONFIGURE CARD DISCOVER START ========== */
INSERT INTO System_Ownr.Card_Tbl (id, name, position, logo, minlength, maxlength, cvclength) VALUES (23, 'Discover', 20, NULL, 16, 16, 3);
INSERT INTO System_Ownr.CardPricing_Tbl (cardid, pricepointid) SELECT 23, id FROM System_Ownr.PricePoint_Tbl WHERE amount = -1;
INSERT INTO System_Ownr.CardPrefix_Tbl (cardid, min, "max") VALUES (23, 6011, 6011);
INSERT INTO System_Ownr.CardPrefix_Tbl (cardid, min, "max") VALUES (23, 622126, 622925);
INSERT INTO System_Ownr.CardPrefix_Tbl (cardid, min, "max") VALUES (23, 644, 649);
INSERT INTO System_Ownr.CardPrefix_Tbl (cardid, min, "max") VALUES (23, 65, 65);
/* ========== CONFIGURE CARD DISCOVER END ========== */

/* ========== CONFIGURE CARD VISA CHECKOUT START ========== */
INSERT INTO System_Ownr.Card_Tbl (id, name, position, logo) VALUES (16, 'VISA Checkout', 14, NULL);

/*Adding the dummy card prefix entry for VISA checkout as a card*/
INSERT INTO System_Ownr.CardPrefix_Tbl (cardid, min, "max") VALUES (16, 0, 0);

/* Enable the same pricing for VISA Checkout as for Apple Pay */ 
INSERT INTO System_Ownr.CardPricing_Tbl (pricepointid, cardid) SELECT pricepointid, 16 FROM System_Ownr.CardPricing_Tbl WHERE cardid = 15;
/* ========== CONFIGURE CARD VISA CHECKOUT END ========== */

/* ========== CONFIGURE VISA CHECKOUT START ========== */
/*START: Adding PSP entries to the PSP_Tbl table for VISA Checkout*/
INSERT INTO System_Ownr.PSP_Tbl (id, name) VALUES (13, 'VISA Checkout');
/*END: Adding PSP entries to the PSP_Tbl table for VISA Checkout*/

/*START: Updating the currency codes for Colombia and Mexico*/
UPDATE System_Ownr.Country_Tbl SET currency = 'COP' WHERE name LIKE 'Colombia';
UPDATE System_Ownr.Country_Tbl SET currency = 'MXN' WHERE name LIKE 'Mexico';
/*END: Updating the currency codes for Colombia and Mexico*/

/*START: Adding Currency entries to the PSPCurrency_Tbl table for VISA Checkout*/
INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'AED';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'ARS';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'AUD';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'BRL';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'CAD';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'CNY';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'CLP';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'HKD';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'MYR';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'NZD';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'PEN';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'SGD';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'ZAR';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'USD';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'COP';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id,13,currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'MXN';

/*END: Adding Currency entries to the PSPCurrency_Tbl table for VISA Checkout*/

/*START: Adding PSP to Card mapping to the PSPCard_Tbl table for VISA Checkout*/
INSERT INTO System_Ownr.PSPCard_Tbl (cardid, pspid) SELECT id, 13 FROM System_Ownr.Card_Tbl WHERE name = 'American Express';
INSERT INTO System_Ownr.PSPCard_Tbl (cardid, pspid) SELECT id, 13 FROM System_Ownr.Card_Tbl WHERE name = 'Master Card';
INSERT INTO System_Ownr.PSPCard_Tbl (cardid, pspid) SELECT id, 13 FROM System_Ownr.Card_Tbl WHERE name = 'VISA';
INSERT INTO System_Ownr.PSPCard_Tbl (cardid, pspid) SELECT id, 13 FROM System_Ownr.Card_Tbl WHERE name = 'Discover';
/*END: Adding PSP to Card mapping to the PSPCard_Tbl table for VISA Checkout*/

-- Enable support for VISA Checkout through WorldPay, CPG and VISA Checkout
INSERT INTO System_Ownr.PSPCard_Tbl (pspid, cardid) VALUES (4, 16);
INSERT INTO System_Ownr.PSPCard_Tbl (pspid, cardid) VALUES (9, 16);
INSERT INTO System_Ownr.PSPCard_Tbl (pspid, cardid) VALUES (13, 16);
/* ========== CONFIGURE VISA CHECKOUT END ========== */

/* ========== CONFIGURE IBE ACCOUNT FOR VISA CHECKOUT START ========== */
-- VISA Checkout
INSERT INTO Client_Ownr.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 13, '2X5JJ0751LFJG3EMYRMS13h-QPSi0pUet0c2zoXupm10tRL28', '2X5JJ0751LFJG3EMYRMS13h-QPSi0pUet0c2zoXupm10tRL28', '5PH9i3cNJ8ZmFK0B-xuSsMzq{8uSkO$o#GZPA+M}');
INSERT INTO Client_Ownr.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100010, 13, '-1');
INSERT INTO Client_Ownr.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10001, 16, 9);
/* ========== CONFIGURE IBE ACCOUNT FOR VISA CHECKOUT END ====== */

/* ========== CONFIGURE APPLE PAY START ========== */
/*START: Adding PSP entries to the PSP_Tbl table for Apple Pay*/
INSERT INTO System_Ownr.PSP_Tbl (id, name) VALUES (14, 'Apple Pay');
/*END: Adding PSP entries to the PSP_Tbl table for Apple Pay*/

/*START: Adding Currency entries to the PSPCurrency_Tbl table for Apple Pay*/
INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'AED';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'ARS';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'AUD';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'BRL';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'CAD';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'CNY';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'CLP';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'HKD';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'MYR';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'NZD';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'PEN';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'SGD';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'ZAR';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'USD';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'COP';

INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 14, currency FROM System_Ownr.Country_Tbl WHERE currency LIKE 'MXN';

/*END: Adding Currency entries to the PSPCurrency_Tbl table for Apple Pay*/

/*START: Adding PSP to Card mapping to the PSPCard_Tbl table for Apple Pay*/
INSERT INTO System_Ownr.PSPCard_Tbl (cardid, pspid) SELECT id, 14 FROM System_Ownr.Card_Tbl WHERE name = 'American Express';
INSERT INTO System_Ownr.PSPCard_Tbl (cardid, pspid) SELECT id, 14 FROM System_Ownr.Card_Tbl WHERE name = 'Master Card';
INSERT INTO System_Ownr.PSPCard_Tbl (cardid, pspid) SELECT id, 14 FROM System_Ownr.Card_Tbl WHERE name = 'VISA';
INSERT INTO System_Ownr.PSPCard_Tbl (cardid, pspid) SELECT id, 14 FROM System_Ownr.Card_Tbl WHERE name = 'Discover';
/*END: Adding PSP to Card mapping to the PSPCard_Tbl table for Apple Pay*/

-- Enable support for Apple Pay through Apple Pay
INSERT INTO System_Ownr.PSPCard_Tbl (pspid, cardid) VALUES (14, 15);
/* ========== CONFIGURE APPLE PAY END ========== */

/* ========== CONFIGURE IPAD ACCOUNT FOR APPLE PAY START ========== */
-- Apple Pay
INSERT INTO Client_Ownr.MerchantAccount_Tbl (clientid, pspid, name) VALUES (10004, 14, 'merchant.com.emirates.EKiPhone');
INSERT INTO Client_Ownr.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100040, 14, '-1');
/* ========== CONFIGURE IPAD ACCOUNT FOR APPLE PAY END ====== */