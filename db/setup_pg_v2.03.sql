/*START: Fixing incorrect values for USD in PSPCurrency_tbl in system schema */

UPDATE system.pspcurrency_tbl sp
SET name = sc.code
FROM system.currency_tbl sc
WHERE sc.id = sp.currencyid;

/*END: Fixing incorrect values for USD in PSPCurrency_tbl in system schema*/

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



--PPro PSPCard

INSERT INTO system.pspcard_tbl (pspid, cardid, enabled) VALUES (46, 39, true);
INSERT INTO system.pspcard_tbl (pspid, cardid, enabled) VALUES (46, 34, true);

--PPro PSPCurrency

INSERT INTO system.pspcurrency_tbl (pspid, currencyid, name, enabled) VALUES (46, 840, 'USD', true);
INSERT INTO system.pspcurrency_tbl (pspid, currencyid, name, enabled) VALUES (46, 458, 'MYR', true);
INSERT INTO system.pspcurrency_tbl (pspid, currencyid, name, enabled) VALUES (46, 978, 'EUR', true);
INSERT INTO system.pspcurrency_tbl (pspid, currencyid, name, enabled) VALUES (46, 608, 'PHP', true);
INSERT INTO system.pspcurrency_tbl (pspid, currencyid, name, enabled) VALUES (46, 702, 'SGD', true);
INSERT INTO system.pspcurrency_tbl (pspid, currencyid, name, enabled) VALUES (46, 752, 'SEK', true);
INSERT INTO system.pspcurrency_tbl (pspid, currencyid, name, enabled) VALUES (46, 764, 'THB', true);
INSERT INTO system.pspcurrency_tbl (pspid, currencyid, name, enabled) VALUES (46, 985, 'PLN', true);
INSERT INTO system.pspcurrency_tbl (pspid, currencyid, name, enabled) VALUES (46, 203, 'CZK', true);
INSERT INTO system.pspcurrency_tbl (pspid, currencyid, name, enabled) VALUES (46, 36, 'AUD', true);

-- PPro SR

INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, psp_type, stateid) VALUES (10047, 39, true, 46, 609, 7, 4);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, psp_type, stateid) VALUES (10047, 34, true, 46, 500, 7, 4);


-- Update AMEX PSP
UPDATE system.psp_tbl SET  capture_method = 6 WHERE id = 45;


INSERT INTO log.state_tbl (id, name, module, func) VALUES (20032, 'Refund Initialized', 'Payment', 'refund');
INSERT INTO log.state_tbl (id, name, module, func) VALUES (20022, 'Cancel Initialized', 'Payment', 'cancel');
INSERT INTO log.state_tbl (id, name, module, func) VALUES (20012, 'Capture Initialized', 'Payment', 'capture');

ALTER TABLE log.transaction_tbl ALTER COLUMN attempt SET DEFAULT 0;
/*======= ADD NEW PROCESSOR TYPE FOR TOKENIZATION SYSTEM ======== */
INSERT INTO system.processortype_tbl (id, name) VALUES (8, 'Tokenize');
/*======= END NEW PROCESSOR TYPE FOR TOKENIZATION SYSTEM ======== */

/* ========== CONFIGURE UATP START FOR SOUTHWEST========== */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (50, 'UATP CardAccount',8);
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (15, 50); /*With Apple-Pay*/

INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (208,50,'DKK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (840,50,'USD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (826,50,'GBP');

/*=================== Adding new states for tokenization used for UATP SUVTP generation : START =======================*/
INSERT INTO log.state_tbl (id, name, module, enabled) VALUES (2020 , 'Tokenization complete - Virtual card created', 'Authorization', true);
INSERT INTO log.state_tbl (id, name, module, enabled) VALUES (2021 , 'Tokenization Failed', 'Authorization', true);
/*=================== Adding new states for tokenization used for UATP SUVTP generation : END =======================*/



