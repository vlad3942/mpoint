/* ========== CONFIGURE PPRO START ========== */

/* START: Adding CARD Configuration Entries */

INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (42, 'PPRO', 23, -1, -1, -1,4);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (42, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 42, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 840;


/* END: Adding CARD Configuration Entries */

/*START: Adding PSP entries to the PSP_Tbl table for PPRO */

INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (46, 'PPRO',4);

/*END: Adding PSP entries to the PSP_Tbl table for PPRO*/

/*START: Adding Currency entries to the PSPCurrency_Tbl table for PPRO*/

INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (840,46,'DKK');

/*END: Adding Currency entries to the PSPCurrency_Tbl table for PPRO*/

/* ========== CONFIGURE DEMO ACCOUNT FOR PPRO START ========== */
-- Wire-Card
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 46, '', '', '');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 46, '-1');

/* ========== CONFIGURE DEMO ACCOUNT FOR PPRO END ====== */