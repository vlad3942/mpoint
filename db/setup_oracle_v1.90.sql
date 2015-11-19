/* ========== CONFIGURE URL TYPE FOR THE MOBILE ENTERPRISE SERVICE BUS START ========== */
INSERT INTO System_Ownr.URLType_Tbl (id, name) VALUES (4, 'Mobile Enterprise Service Bus');

-- Mobile Enterprise Service Bus
INSERT INTO Client_Ownr.URL_Tbl (urltypeid, clientid, url) SELECT 4, id, 'http://localhost:9000/' FROM Client_Ownr.Client_Tbl;
/* ========== CONFIGURE URL TYPE FOR THE MOBILE ENTERPRISE SERVICE BUS START ========== */

/* ========== CONFIGURE CARD DISCOVER START ========== */
INSERT INTO System_Ownr.Card_Tbl (id, name, position, logo, minlength, maxlength, cvclength) VALUES (22, 'Discover', 20, NULL, 16, 16, 3);
INSERT INTO System_Ownr.CardPricing_Tbl (cardid, pricepointid) SELECT 22, id FROM System_Ownr.PricePoint_Tbl WHERE amount = -1;
INSERT INTO System_Ownr.CardPrefix_Tbl (cardid, min, "max") VALUES (22, 6011, 6011);
INSERT INTO System_Ownr.CardPrefix_Tbl (cardid, min, "max") VALUES (22, 622126, 622925);
INSERT INTO System_Ownr.CardPrefix_Tbl (cardid, min, "max") VALUES (22, 644, 649);
INSERT INTO System_Ownr.CardPrefix_Tbl (cardid, min, "max") VALUES (22, 65, 65);
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

/* ========== ALTER TABLE FOR MERCHANT ACCOUNT TO HAVE PASSWORD OF 4000 CHARS START ====== */
ALTER TABLE Client_Ownr.MerchantAccount_Tbl MODIFY passwd VARCHAR2(4000);
ALTER TABLE Client_Ownr.MerchantAccount_Tbl MODIFY name VARCHAR2(100);
/* ========== ALTER TABLE FOR MERCHANT ACCOUNT TO HAVE PASSWORD OF 4000 CHARS END ====== */

/* ========== CONFIGURE CARD MASTER PASS START ========== */
INSERT INTO System_Ownr.Card_Tbl (id, name, position, logo) VALUES (23, 'Master Pass', 15, NULL);
/*Adding the dummy card prefix entry for Master Pass as a card*/
INSERT INTO System_Ownr.CardPricing_Tbl (cardid, pricepointid) SELECT 23, id FROM System_Ownr.PricePoint_Tbl WHERE amount = -1;
INSERT INTO System_Ownr.CardPrefix_Tbl (cardid, min, max) VALUES (23, 0, 0);
/* ========== CONFIGURE CARD MASTER PASS END ========== */

/* ========== CONFIGURE MASTER PASS START ========== */
/*START: Adding PSP entries to the PSP_Tbl table for Master Pass*/
INSERT INTO System_Ownr.PSP_Tbl (id, name) VALUES (15, 'Master Pass');
INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) 
SELECT id, 15, currency FROM System_Ownr.Country_Tbl;
/*END: Adding PSP entries to the PSP_Tbl table for Master Pass*/

/*START: Adding PSP to Card mapping to the PSPCard_Tbl table for Master Pass*/
-- Master Pass as PSP.
INSERT INTO System_Ownr.PSPCard_Tbl (cardid, pspid) SELECT id, 15 FROM System_Ownr.Card_Tbl WHERE name = 'American Express';
INSERT INTO System_Ownr.PSPCard_Tbl (cardid, pspid) SELECT id, 15 FROM System_Ownr.Card_Tbl WHERE name = 'Master Card';
INSERT INTO System_Ownr.PSPCard_Tbl (cardid, pspid) SELECT id, 15 FROM System_Ownr.Card_Tbl WHERE name = 'VISA';
INSERT INTO System_Ownr.PSPCard_Tbl (cardid, pspid) SELECT id, 15 FROM System_Ownr.Card_Tbl WHERE name = 'Discover';
INSERT INTO System_Ownr.PSPCard_Tbl (cardid, pspid) SELECT id, 15 FROM System_Ownr.Card_Tbl WHERE name = 'Diners Club';
INSERT INTO System_Ownr.PSPCard_Tbl (cardid, pspid) SELECT id, 15 FROM System_Ownr.Card_Tbl WHERE name = 'Maestro';
INSERT INTO System_Ownr.PSPCard_Tbl (cardid, pspid) SELECT id, 15 FROM System_Ownr.Card_Tbl WHERE name = 'Master Pass';

--CPG as PSP.
INSERT INTO System_Ownr.PSPCard_Tbl (cardid, pspid) SELECT id, 9 FROM System_Ownr.Card_Tbl WHERE name = 'Diners Club';
INSERT INTO System_Ownr.PSPCard_Tbl (cardid, pspid) SELECT id, 9 FROM System_Ownr.Card_Tbl WHERE name = 'Maestro';
INSERT INTO System_Ownr.PSPCard_Tbl (cardid, pspid) SELECT id, 9 FROM System_Ownr.Card_Tbl WHERE name = 'Master Pass';
/*END: Adding PSP to Card mapping to the PSPCard_Tbl table for Master Pass*/
/* ========== CONFIGURE MASTER PASS END ========== */

/* ========== CONFIGURE DEMO ACCOUNT FOR MASTER PASS START ========== */
-- Master Pass
INSERT INTO Client_Ownr.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 15, 'hPbBTn6RXkwR0HvigIqEL4D20FweaOqIYDLxU-jh171c17fd!4251484361307a2f586b515a537530577858615862413d3d', 'a466w4vhngfxpigdto1c71ighwulcy1e7t', 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQC3784sCmD/iULkI+H8Fy5E//2xRY7Yhbn6VR2auvfCSjrC/EcfGs00jOkQjQWLIzmbh6MMeVPBhLRbA4xiWzfLNhXetqQmkjN33jSXi6nxGp7xCKiZp97qNmCJHqUqZ8euKdZ/5DK5a4F28s/me8bfBWeaZbuEcFr/t+3QE5F1GgxcpJVJ/ME1aOl0G6CLDH02Two8+W6TxqBp/oxnvK/EKj7CTjgM9K/sOk/JM8hiuhy4ThneGnPnWOEmccAS0EZBoxR0qxZXDYLabEtP2eTD5o+IR/widAAoCaRQ3yVciZcbiXbBk0ErB95c55JAruD67ODv6B0Of6xkXhpHsUC1AgMBAAECggEATFwChGf+orcSDPIUk/nvnHeFkz1kMuE5NwJ02tJ5nrAAOwhOYkxXGlTRQKy6u2txM+8YMkqACduUoCAV/JMP043tgFrkRJr3QPD/dlZlw5EgoMHOdJOrSCIw61vMh5Ez5Uq7ILbUlANcaMweoPmLsvRkcUWAlleqf3SVBofJIApu5kNJDMgT/Lg0AhTcUaEq8RlAbhXOexrloTpKCuI82s9dr9gWhpg9TfM6+P9z6bonMhi+0og6gRn/cTEkY5eWNZaHqAQVdGQ+0xq1fJe82EvHR4kBIh0FrKPOqn6QumuSj91hrKU4G4ssQ7M4jVA/MAzngMRa66dW5O453qbUNQKBgQDkSMvDvmtEe/fSwi3/MZ1WnoY7lWFAs1q3jUhLlSawwDTaliKTYjItSFBWq2Yix03qlf7Eqhbjj+4e0Za2AxANE+2Kaq7XsTj7L/wZA6ZosCJoOEbvhX+LuP6rpWVM46TfnlDGEk4+15hfKA/ZCowr8TSvkEc4wcqqNniPyGowWwKBgQDORKkvfMj3hKm3ddw4HrvvXfF/Z/Ah5j4Ko/lu8hEjgSNtMbIldKiFzM+z9KIoYsmEt6yi+ms56d5lEtzGSdz67LikWg+wfKhhF/U/mYGT/tV/vRMUqDAtjATj2oBuFyClIJ1/Loz+LbrK0jMweEj/nRZ4IojyrVHba26/+yUgLwKBgA3PraxJD/pTubmhj+DZophD/QEL15dvgnSKcq5H9tBIwKnc3XinPzvoHRwxQHuoLTmdG43QcJQR+CkbKxAV/VmdNAjkzXE1QqpHy+vDgcThqyM9DGWfYQkWByphVlChkS8KR/7DysIYjxpqtRK/hZ1++V4Jz5VKfDVyGDcyu+HzAoGAaR2IcpDPAYRz0PCZN2hCMevYBCt9rmjdOSLzHFzz6voGicEHnhrjPrxvJLAIazhcpevMaInhVvQdx7hjFhHSMXWtauQSlsgQLtq8upqJ9FeriZtbO+2yD6QJYeyaAoB6vGvwlz2r0GSRioawW4UQ/mKZbsN6suEsk/sdx2w/MTUCgYEAvzC15f1K/zoOJvJMYRaKl1mUKDkfht4yjGrNOJrnrTmDByZAviiM/2C41R6M+VgN+/H1CpXgzzc88XL3F5uXpiJmqv7PqVRrdfZ5c91bioBetHJ3qbYrE75frLtxPsA5rfF63pjCVebAg1vz30sdg/FOoxL+IdRlbXDswmMMyTM=');
INSERT INTO Client_Ownr.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 15, '-1');
-- Adding Static Route entries for the client EK and cards with PSP as follows
-- CPG
INSERT INTO Client_Ownr.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10001, 23, 9);
INSERT INTO Client_Ownr.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10001, 1, 9);
INSERT INTO Client_Ownr.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10001, 4, 9);
INSERT INTO Client_Ownr.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10001, 6, 9);
INSERT INTO Client_Ownr.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10001, 7, 9);
INSERT INTO Client_Ownr.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10001, 8, 9);
INSERT INTO Client_Ownr.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10001, 22, 9);
INSERT INTO Client_Ownr.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10001, 23, 15);
/* ========== CONFIGURE DEMO ACCOUNT FOR MASTER PASS START ========== */