/* ----------------Adding Configurations for CHUBB PSP - START ------------------------------ */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (48, 'CHUBB', 1);
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (702,48,'SGD');
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 48, 'CHUBB', '', '');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 48, '-1');
INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position, psp_type) VALUES (10007, 8, true, 48, 642, 1, null, 1);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (8, 48, true);
/* ----------------Adding Configurations for CHUBB PSP - END ------------------------------ */