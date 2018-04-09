/* ========== CONFIGURE AMEX START ========== */
/*START: Adding PSP entries to the PSP_Tbl table for AMEX*/

INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (45, 'Amex',2);

/*END: Adding PSP entries to the PSP_Tbl table for AMEX*/

/*START: Adding Currency entries to the PSPCurrency_Tbl table for AMEX*/

INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (208,45,'DKK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (752,45,'SEK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (578,45,'NOK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (826,45,'USD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (978,45,'EUR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (208,45,'DKK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (352,45,'ISK');


/*END: Adding Currency entries to the PSPCurrency_Tbl table for AMEX*/

INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (1, 45, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, stateid, enabled) VALUES (10007, 1, 45, 200, 1, true);

/* ========== CONFIGURE DEMO ACCOUNT FOR AMEX START ========== */
-- Wire-Card
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 45, '9105bb4f-ae68-4768-9c3b-3eda968f57ea', '70000-APILUHN-CARD', '8mhwavKVb91T');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 45, '-1');

/* ========== CONFIGURE DEMO ACCOUNT FOR AMEX END ====== */