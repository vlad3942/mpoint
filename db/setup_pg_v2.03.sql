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


-- Paytabs Start

-- IF not mechant account is exsit
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 38, 'Paytabs', '10028311', 'wTMJj6gbalSZdnSWzdMF6m0Q1dkkFzlct7WMecXoyQoLegIOuutBRurTHMemlzyPHuCVHAkfqI1EZJtteX45rwZ8iduEM3tuy5qf');
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (100007, 38, '-1', true);

-- IF mechant account is exsit
UPDATE client.merchantaccount_tbl SET username = '10028311', passwd = 'wTMJj6gbalSZdnSWzdMF6m0Q1dkkFzlct7WMecXoyQoLegIOuutBRurTHMemlzyPHuCVHAkfqI1EZJtteX45rwZ8iduEM3tuy5qf' WHERE clientid = 10007  AND pspid = 38;

-- KNet Start
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (48,38,'BHD');

INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (70, 'BENEFIT', 23, -1, -1, -1,4);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (70, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 70, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 48;

INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position, psp_type) VALUES (10007, 70, true, 38, 601, 1, null, 1);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (70, 38, true);

-- KNet End

-- BENEFIT Start

INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (414,38,'KWD');

INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (69, 'KNet', 23, -1, -1, -1,4);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (69, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 69, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 414;

INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position, psp_type) VALUES (10007, 69, true, 38, 604, 1, null, 1);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (69, 38, true);

-- BENEFIT Stop

-- Paytabs StopINSERT INTO log.state_tbl (id, name, module, func) VALUES (20012, 'Capture Initialized', 'Payment', 'capture');

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

--CMP-2558
DROP INDEX client.cardaccess_card_country_uq RESTRICT;
CREATE UNIQUE INDEX cardaccess_card_country_uq ON client.cardaccess_tbl (clientid, cardid, pspid, countryid, psp_type) WHERE enabled='true';

---- MADA Integration Start ---

INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (71, 'MADA', 23, -1, -1, -1,4);

INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position, psp_type) VALUES (10007, 71, true, 38, 601, 1, null, 1);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (71, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 71, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 48;
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (71, 38, true);

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 71, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 414;
-- MADA Integration stop --

-- Paytabs SADAD v2 --
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (72, 'SADAD v2', 23, -1, -1, -1,4);

INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position, psp_type) VALUES (10007, 72, true, 38, 608, 1, null, 1);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (72, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 72, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 682;
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (72, 38, true);

-- End --

--Additional Properties for Paytabs Integration --
--To be configured based on Sandbox or PROD account configured for each of the APMs.
--SADAD v2 and MADA may have same account while KNET and BENEFIT likely to have separate accounts,
-- so expecting 3 accounts to be configured and hence the need for additional property to store merchant psp config.
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('KNET_MID', '10028311', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10007 and pspid = 38), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('KNET_Secret_Key', 'wTMJj6gbalSZdnSWzdMF6m0Q1dkkFzlct7WMecXoyQoLegIOuutBRurTHMemlzyPHuCVHAkfqI1EZJtteX45rwZ8iduEM3tuy5qf', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10007 and pspid = 38), 'merchant');

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('BENEFIT_MID', '10028311', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10007 and pspid = 38), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('BENEFIT_Secret_Key', 'wTMJj6gbalSZdnSWzdMF6m0Q1dkkFzlct7WMecXoyQoLegIOuutBRurTHMemlzyPHuCVHAkfqI1EZJtteX45rwZ8iduEM3tuy5qf', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10007 and pspid = 38), 'merchant');

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('SADAD_V2_MID', '10028311', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10007 and pspid = 38), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('SADAD_V2_Secret_Key', 'wTMJj6gbalSZdnSWzdMF6m0Q1dkkFzlct7WMecXoyQoLegIOuutBRurTHMemlzyPHuCVHAkfqI1EZJtteX45rwZ8iduEM3tuy5qf', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10007 and pspid = 38), 'merchant');

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('MADA_MID', 'urmila.s@cellpointmobile.com', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10007 and pspid = 38), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('MADA_Secret_Key', 'EZJZnZpSxpusvsKxtjFsTJedIyexXweBvXBSW1LuqgXWIHtb19DUSR7Bqm8oSn0okBhhC3M3jJZ1qtdsCRmvIt79QgxJDx7X9pfM', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10007 and pspid = 38), 'merchant');
-- Paytabs Additional Property  - end --