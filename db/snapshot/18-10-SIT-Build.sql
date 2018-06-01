/* ========== CONFIGURE UATP START ========== */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (49, 'UATP',2);

INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (208,49,'DKK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (840,49,'USD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (826,49,'GBP');

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 49, 'uatp_cellpoint', 'uatp_cellpoint', 'Had#wR4k');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 49, '-1');

-- TODO Additional property