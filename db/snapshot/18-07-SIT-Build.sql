/* ----------------Adding Configurations for CHUBB PSP - START ------------------------------ */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (48, 'CHUBB', 1);
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (702,48,'SGD');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (840,48,'USD');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (764,48,'THB');

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10018, 48, 'CHUBB', '', '');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100075, 48, '-1');
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (8, 48, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (7, 48, true);
/* ----------------Adding Configurations for CHUBB PSP - END ------------------------------ */