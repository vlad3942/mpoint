/* ========== CONFIGURE UATP START ========== */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (49, 'UATP',2);
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (21, 49);


INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (208,49,'DKK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (840,49,'USD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (826,49,'GBP');

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 49, 'uatp_cellpoint', 'uatp_cellpoint', 'Had#wR4k');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 49, '-1');

-- UATP Additional property
-- CardAcceptorIdentificationCode  - Varies by Airline/Merchant
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CardAcceptorIdentificationCode', 'A02', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10007 and pspid = 49), 'merchant');
--provided by UATP, value 826100001 assigned for CPM
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('InstitutionIdentificationCode', '826100001', 10007, 'client');
/* ========== CONFIGURE UATP END ========== */
