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
/*END: Adding PSP to Card mapping to the PSPCard_Tbl table for VISA Checkout*/
/* ========== CONFIGURE VISA CHECKOUT END ========== */