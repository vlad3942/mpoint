/* ========== CONFIGURE NETS START ========== */
/*START: Adding PSP entries to the PSP_Tbl table for AMEX*/

INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (45, 'Amex',2);

/*END: Adding PSP entries to the PSP_Tbl table for AMEX*/

/*START: Adding Currency entries to the PSPCurrency_Tbl table for AMEX*/

INSERT INTO system.pspcurrency_tbl (countryid, pspid, name) VALUES (100,45,'DKK');
INSERT INTO system.pspcurrency_tbl (countryid, pspid, name) VALUES (101,45,'SEK');
INSERT INTO system.pspcurrency_tbl (countryid, pspid, name) VALUES (102,45,'NOK');
INSERT INTO system.pspcurrency_tbl (countryid, pspid, name) VALUES (103,45,'USD');
INSERT INTO system.pspcurrency_tbl (countryid, pspid, name) VALUES (104,45,'EUR');
INSERT INTO system.pspcurrency_tbl (countryid, pspid, name) VALUES (127,45,'DKK');
INSERT INTO system.pspcurrency_tbl (countryid, pspid, name) VALUES (130,45,'DKK');
INSERT INTO system.pspcurrency_tbl (countryid, pspid, name) VALUES (132,45,'ISK');

/*END: Adding Currency entries to the PSPCurrency_Tbl table for AMEX*/

/* ========== CONFIGURE DEMO ACCOUNT FOR AMEX START ========== */
-- Wire-Card
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 45, '9105bb4f-ae68-4768-9c3b-3eda968f57ea', '70000-APILUHN-CARD', '8mhwavKVb91T');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 45, '-1');

/* ========== CONFIGURE DEMO ACCOUNT FOR AMEX END ====== */