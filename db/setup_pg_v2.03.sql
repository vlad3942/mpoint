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

-- For new merchant config
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 38, 'Paytabs-MADA', 'urmila.s@cellpointmobile.com', 'EZJZnZpSxpusvsKxtjFsTJedIyexXweBvXBSW1LuqgXWIHtb19DUSR7Bqm8oSn0okBhhC3M3jJZ1qtdsCRmvIt79QgxJDx7X9pfM');
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (100007, 38, '-1', true);

-- Update if merchant account is exists
UPDATE client.merchantaccount_tbl SET username = 'urmila.s@cellpointmobile.com', passwd = 'EZJZnZpSxpusvsKxtjFsTJedIyexXweBvXBSW1LuqgXWIHtb19DUSR7Bqm8oSn0okBhhC3M3jJZ1qtdsCRmvIt79QgxJDx7X9pfM' WHERE clientid = 10007  AND pspid = 38;

-- MADA Integration stop --

-- Paytabs SADAD v2 --
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (72, 'SADAD v2', 23, -1, -1, -1,4);

INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position, psp_type) VALUES (10007, 72, true, 38, 608, 1, null, 1);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (72, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 72, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 682;
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (72, 38, true);

-- For new merchant config
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 38, 'Paytabs', '10028311', 'wTMJj6gbalSZdnSWzdMF6m0Q1dkkFzlct7WMecXoyQoLegIOuutBRurTHMemlzyPHuCVHAkfqI1EZJtteX45rwZ8iduEM3tuy5qf');
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (100007, 38, '-1', true);

-- Update if merchant account is exists
UPDATE client.merchantaccount_tbl SET username = '10028311', passwd = 'wTMJj6gbalSZdnSWzdMF6m0Q1dkkFzlct7WMecXoyQoLegIOuutBRurTHMemlzyPHuCVHAkfqI1EZJtteX45rwZ8iduEM3tuy5qf' WHERE clientid = 10007  AND pspid = 38;;

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


-- country calling code
ALTER TABLE system.country_tbl ADD country_calling_code INTEGER NULL;

update system.country_tbl set country_calling_code=297 where alpha3code='ABW';
update system.country_tbl set country_calling_code=93 where alpha3code='AFG';
update system.country_tbl set country_calling_code=244 where alpha3code='AGO';
update system.country_tbl set country_calling_code=355 where alpha3code='ALB';
update system.country_tbl set country_calling_code=376 where alpha3code='AND';
update system.country_tbl set country_calling_code=971 where alpha3code='ARE';
update system.country_tbl set country_calling_code=54 where alpha3code='ARG';
update system.country_tbl set country_calling_code=374 where alpha3code='ARM';
update system.country_tbl set country_calling_code=61 where alpha3code='AUS';
update system.country_tbl set country_calling_code=43 where alpha3code='AUT';
update system.country_tbl set country_calling_code=994 where alpha3code='AZE';
update system.country_tbl set country_calling_code=257 where alpha3code='BDI';
update system.country_tbl set country_calling_code=32 where alpha3code='BEL';
update system.country_tbl set country_calling_code=229 where alpha3code='BEN';
update system.country_tbl set country_calling_code=226 where alpha3code='BFA';
update system.country_tbl set country_calling_code=880 where alpha3code='BGD';
update system.country_tbl set country_calling_code=359 where alpha3code='BGR';
update system.country_tbl set country_calling_code=973 where alpha3code='BHR';
update system.country_tbl set country_calling_code=387 where alpha3code='BIH';
update system.country_tbl set country_calling_code=375 where alpha3code='BLR';
update system.country_tbl set country_calling_code=501 where alpha3code='BLZ';
update system.country_tbl set country_calling_code=591 where alpha3code='BOL';
update system.country_tbl set country_calling_code=55 where alpha3code='BRA';
update system.country_tbl set country_calling_code=673 where alpha3code='BRN';
update system.country_tbl set country_calling_code=975 where alpha3code='BTN';
update system.country_tbl set country_calling_code=267 where alpha3code='BWA';
update system.country_tbl set country_calling_code=236 where alpha3code='CAF';
update system.country_tbl set country_calling_code=41 where alpha3code='CHE';
update system.country_tbl set country_calling_code=56 where alpha3code='CHL';
update system.country_tbl set country_calling_code=86 where alpha3code='CHN';
update system.country_tbl set country_calling_code=225 where alpha3code='CIV';
update system.country_tbl set country_calling_code=237 where alpha3code='CMR';
update system.country_tbl set country_calling_code=243 where alpha3code='COD';
update system.country_tbl set country_calling_code=242 where alpha3code='COG';
update system.country_tbl set country_calling_code=682 where alpha3code='COK';
update system.country_tbl set country_calling_code=57 where alpha3code='COL';
update system.country_tbl set country_calling_code=269 where alpha3code='COM';
update system.country_tbl set country_calling_code=238 where alpha3code='CPV';
update system.country_tbl set country_calling_code=506 where alpha3code='CRI';
update system.country_tbl set country_calling_code=53 where alpha3code='CUB';
update system.country_tbl set country_calling_code=996 where alpha3code='CYM';
update system.country_tbl set country_calling_code=357 where alpha3code='CYP';
update system.country_tbl set country_calling_code=420 where alpha3code='CZE';
update system.country_tbl set country_calling_code=49 where alpha3code='DEU';
update system.country_tbl set country_calling_code=253 where alpha3code='DJI';
update system.country_tbl set country_calling_code=45 where alpha3code='DNK';
update system.country_tbl set country_calling_code=213 where alpha3code='DZA';
update system.country_tbl set country_calling_code=593 where alpha3code='ECU';
update system.country_tbl set country_calling_code=20 where alpha3code='EGY';
update system.country_tbl set country_calling_code=291 where alpha3code='ERI';
update system.country_tbl set country_calling_code=34 where alpha3code='ESP';
update system.country_tbl set country_calling_code=372 where alpha3code='EST';
update system.country_tbl set country_calling_code=251 where alpha3code='ETH';
update system.country_tbl set country_calling_code=358 where alpha3code='FIN';
update system.country_tbl set country_calling_code=679 where alpha3code='FJI';
update system.country_tbl set country_calling_code=500 where alpha3code='FLK';
update system.country_tbl set country_calling_code=33 where alpha3code='FRA';
update system.country_tbl set country_calling_code=298 where alpha3code='FRO';
update system.country_tbl set country_calling_code=691 where alpha3code='FSM';
update system.country_tbl set country_calling_code=241 where alpha3code='GAB';
update system.country_tbl set country_calling_code=995 where alpha3code='GEO';
update system.country_tbl set country_calling_code=233 where alpha3code='GHA';
update system.country_tbl set country_calling_code=350 where alpha3code='GIB';
update system.country_tbl set country_calling_code=224 where alpha3code='GIN';
update system.country_tbl set country_calling_code=590 where alpha3code='GLP';
update system.country_tbl set country_calling_code=220 where alpha3code='GMB';
update system.country_tbl set country_calling_code=245 where alpha3code='GNB';
update system.country_tbl set country_calling_code=240 where alpha3code='GNQ';
update system.country_tbl set country_calling_code=30 where alpha3code='GRC';
update system.country_tbl set country_calling_code=299 where alpha3code='GRL';
update system.country_tbl set country_calling_code=502 where alpha3code='GTM';
update system.country_tbl set country_calling_code=594 where alpha3code='GUF';
update system.country_tbl set country_calling_code=592 where alpha3code='GUY';
update system.country_tbl set country_calling_code=852 where alpha3code='HKG';
update system.country_tbl set country_calling_code=504 where alpha3code='HND';
update system.country_tbl set country_calling_code=385 where alpha3code='HRV';
update system.country_tbl set country_calling_code=509 where alpha3code='HTI';
update system.country_tbl set country_calling_code=36 where alpha3code='HUN';
update system.country_tbl set country_calling_code=62 where alpha3code='IDN';
update system.country_tbl set country_calling_code=91 where alpha3code='IND';
update system.country_tbl set country_calling_code=353 where alpha3code='IRL';
update system.country_tbl set country_calling_code=98 where alpha3code='IRN';
update system.country_tbl set country_calling_code=964 where alpha3code='IRQ';
update system.country_tbl set country_calling_code=354 where alpha3code='ISL';
update system.country_tbl set country_calling_code=972 where alpha3code='ISR';
update system.country_tbl set country_calling_code=39 where alpha3code='ITA';
update system.country_tbl set country_calling_code=962 where alpha3code='JOR';
update system.country_tbl set country_calling_code=81 where alpha3code='JPN';
update system.country_tbl set country_calling_code=254 where alpha3code='KEN';
update system.country_tbl set country_calling_code=855 where alpha3code='KHM';
update system.country_tbl set country_calling_code=686 where alpha3code='KIR';
update system.country_tbl set country_calling_code=965 where alpha3code='KWT';
update system.country_tbl set country_calling_code=856 where alpha3code='LAO';
update system.country_tbl set country_calling_code=961 where alpha3code='LBN';
update system.country_tbl set country_calling_code=231 where alpha3code='LBR';
update system.country_tbl set country_calling_code=423 where alpha3code='LIE';
update system.country_tbl set country_calling_code=94 where alpha3code='LKA';
update system.country_tbl set country_calling_code=266 where alpha3code='LSO';
update system.country_tbl set country_calling_code=370 where alpha3code='LTU';
update system.country_tbl set country_calling_code=352 where alpha3code='LUX';
update system.country_tbl set country_calling_code=371 where alpha3code='LVA';
update system.country_tbl set country_calling_code=853 where alpha3code='MAC';
update system.country_tbl set country_calling_code=853 where alpha3code='MAC';
update system.country_tbl set country_calling_code=212 where alpha3code='MAR';
update system.country_tbl set country_calling_code=377 where alpha3code='MCO';
update system.country_tbl set country_calling_code=373 where alpha3code='MDA';
update system.country_tbl set country_calling_code=261 where alpha3code='MDG';
update system.country_tbl set country_calling_code=960 where alpha3code='MDV';
update system.country_tbl set country_calling_code=52 where alpha3code='MEX';
update system.country_tbl set country_calling_code=692 where alpha3code='MHL';
update system.country_tbl set country_calling_code=389 where alpha3code='MKD';
update system.country_tbl set country_calling_code=223 where alpha3code='MLI';
update system.country_tbl set country_calling_code=356 where alpha3code='MLT';
update system.country_tbl set country_calling_code=382 where alpha3code='MNE';
update system.country_tbl set country_calling_code=976 where alpha3code='MNG';
update system.country_tbl set country_calling_code=258 where alpha3code='MOZ';
update system.country_tbl set country_calling_code=222 where alpha3code='MRT';
update system.country_tbl set country_calling_code=596 where alpha3code='MTQ';
update system.country_tbl set country_calling_code=230 where alpha3code='MUS';
update system.country_tbl set country_calling_code=265 where alpha3code='MWI';
update system.country_tbl set country_calling_code=60 where alpha3code='MYS';
update system.country_tbl set country_calling_code=262 where alpha3code='MYT';
update system.country_tbl set country_calling_code=264 where alpha3code='NAM';
update system.country_tbl set country_calling_code=687 where alpha3code='NCL';
update system.country_tbl set country_calling_code=227 where alpha3code='NER';
update system.country_tbl set country_calling_code=234 where alpha3code='NGA';
update system.country_tbl set country_calling_code=505 where alpha3code='NIC';
update system.country_tbl set country_calling_code=683 where alpha3code='NIU';
update system.country_tbl set country_calling_code=31 where alpha3code='NLD';
update system.country_tbl set country_calling_code=47 where alpha3code='NOR';
update system.country_tbl set country_calling_code=977 where alpha3code='NPL';
update system.country_tbl set country_calling_code=674 where alpha3code='NRU';
update system.country_tbl set country_calling_code=64 where alpha3code='NZL';
update system.country_tbl set country_calling_code=968 where alpha3code='OMN';
update system.country_tbl set country_calling_code=92 where alpha3code='PAK';
update system.country_tbl set country_calling_code=507 where alpha3code='PAN';
update system.country_tbl set country_calling_code=51 where alpha3code='PER';
update system.country_tbl set country_calling_code=63 where alpha3code='PHL';
update system.country_tbl set country_calling_code=680 where alpha3code='PLW';
update system.country_tbl set country_calling_code=48 where alpha3code='POL';
update system.country_tbl set country_calling_code=351 where alpha3code='PRT';
update system.country_tbl set country_calling_code=595 where alpha3code='PRY';
update system.country_tbl set country_calling_code=970 where alpha3code='PSE';
update system.country_tbl set country_calling_code=689 where alpha3code='PYF';
update system.country_tbl set country_calling_code=974 where alpha3code='QAT';
update system.country_tbl set country_calling_code=262 where alpha3code='REU';
update system.country_tbl set country_calling_code=40 where alpha3code='ROU';
update system.country_tbl set country_calling_code=7 where alpha3code='RUS';
update system.country_tbl set country_calling_code=250 where alpha3code='RWA';
update system.country_tbl set country_calling_code=966 where alpha3code='SAU';
update system.country_tbl set country_calling_code=249 where alpha3code='SDN';
update system.country_tbl set country_calling_code=221 where alpha3code='SEN';
update system.country_tbl set country_calling_code=65 where alpha3code='SGP';
update system.country_tbl set country_calling_code=290 where alpha3code='SHN';
update system.country_tbl set country_calling_code=290 where alpha3code='SHN';
update system.country_tbl set country_calling_code=677 where alpha3code='SLB';
update system.country_tbl set country_calling_code=232 where alpha3code='SLE';
update system.country_tbl set country_calling_code=503 where alpha3code='SLV';
update system.country_tbl set country_calling_code=378 where alpha3code='SMR';
update system.country_tbl set country_calling_code=252 where alpha3code='SOM';
update system.country_tbl set country_calling_code=508 where alpha3code='SPM';
update system.country_tbl set country_calling_code=381 where alpha3code='SRB';
update system.country_tbl set country_calling_code=239 where alpha3code='STP';
update system.country_tbl set country_calling_code=597 where alpha3code='SUR';
update system.country_tbl set country_calling_code=421 where alpha3code='SVK';
update system.country_tbl set country_calling_code=386 where alpha3code='SVN';
update system.country_tbl set country_calling_code=46 where alpha3code='SWE';
update system.country_tbl set country_calling_code=268 where alpha3code='SWZ';
update system.country_tbl set country_calling_code=248 where alpha3code='SYC';
update system.country_tbl set country_calling_code=963 where alpha3code='SYR';
update system.country_tbl set country_calling_code=235 where alpha3code='TCD';
update system.country_tbl set country_calling_code=228 where alpha3code='TGO';
update system.country_tbl set country_calling_code=66 where alpha3code='THA';
update system.country_tbl set country_calling_code=993 where alpha3code='TKM';
update system.country_tbl set country_calling_code=676 where alpha3code='TON';
update system.country_tbl set country_calling_code=216 where alpha3code='TUN';
update system.country_tbl set country_calling_code=90 where alpha3code='TUR';
update system.country_tbl set country_calling_code=688 where alpha3code='TUV';
update system.country_tbl set country_calling_code=255 where alpha3code='TZA';
update system.country_tbl set country_calling_code=256 where alpha3code='UGA';
update system.country_tbl set country_calling_code=380 where alpha3code='UKR';
update system.country_tbl set country_calling_code=598 where alpha3code='URY';
update system.country_tbl set country_calling_code=1 where alpha3code='USA';
update system.country_tbl set country_calling_code=998 where alpha3code='UZB';
update system.country_tbl set country_calling_code=379 where alpha3code='VAT';
update system.country_tbl set country_calling_code=58 where alpha3code='VEN';
update system.country_tbl set country_calling_code=678 where alpha3code='VUT';
update system.country_tbl set country_calling_code=681 where alpha3code='WLF';
update system.country_tbl set country_calling_code=685 where alpha3code='WSM';
update system.country_tbl set country_calling_code=967 where alpha3code='YEM';
update system.country_tbl set country_calling_code=27 where alpha3code='ZAF';
update system.country_tbl set country_calling_code=260 where alpha3code='ZMB';
update system.country_tbl set country_calling_code=263 where alpha3code='ZWE';

--Transaction under review for fraud, payment rejected sub state.
INSERT INTO log.state_tbl (id, name, module) VALUES (20104, 'Payment rejected. Transaction under review.', 'Payment');

--markup type 'b2b-app' introduced to redirect to merchant from paytabs redirect connector to client accept or decline url, this is applicable for API Integrations like for ET.
INSERT INTO Client.Account_Tbl (id,clientid, name, markup) VALUES ('1000077','10007', 'API Integration for Merchant app', 'b2b-app');

-- URL where the customer may be redirected if txn fails.
ALTER TABLE Log.Transaction_Tbl ADD declineurl VARCHAR(255);

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

-- For new merchant config
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 38, 'Paytabs-MADA', 'urmila.s@cellpointmobile.com', 'EZJZnZpSxpusvsKxtjFsTJedIyexXweBvXBSW1LuqgXWIHtb19DUSR7Bqm8oSn0okBhhC3M3jJZ1qtdsCRmvIt79QgxJDx7X9pfM');
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (100007, 38, '-1', true);

-- Update if merchant account is exists
UPDATE client.merchantaccount_tbl SET username = 'urmila.s@cellpointmobile.com', passwd = 'EZJZnZpSxpusvsKxtjFsTJedIyexXweBvXBSW1LuqgXWIHtb19DUSR7Bqm8oSn0okBhhC3M3jJZ1qtdsCRmvIt79QgxJDx7X9pfM' WHERE clientid = 10007  AND pspid = 38;

-- MADA Integration stop --

-- Paytabs SADAD v2 --
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (72, 'SADAD v2', 23, -1, -1, -1,4);

INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position, psp_type) VALUES (10007, 72, true, 38, 608, 1, null, 1);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (72, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 72, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 682;
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (72, 38, true);

-- For new merchant config
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 38, 'Paytabs', '10028311', 'wTMJj6gbalSZdnSWzdMF6m0Q1dkkFzlct7WMecXoyQoLegIOuutBRurTHMemlzyPHuCVHAkfqI1EZJtteX45rwZ8iduEM3tuy5qf');
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (100007, 38, '-1', true);

-- Update if merchant account is exists
UPDATE client.merchantaccount_tbl SET username = '10028311', passwd = 'wTMJj6gbalSZdnSWzdMF6m0Q1dkkFzlct7WMecXoyQoLegIOuutBRurTHMemlzyPHuCVHAkfqI1EZJtteX45rwZ8iduEM3tuy5qf' WHERE clientid = 10007  AND pspid = 38;;

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

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('HPP_HOST_URL', 'HPP_URL', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('debug', 'true', 10007, 'client');

-- FPX Integration  --
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (51, 'eGHL',1);
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (73, 'FPX', 23, -1, -1, -1,4);

INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position, psp_type) VALUES (10007, 73, true, 51, 638, 1, null, 4);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (73, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 73, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 458;
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (73, 51, true);
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (458,51,'MYR');


-- merchant config
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 51, 'MALINDO AIRWAYS SDN BHD', 'MLD', 'mld12345');
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (100007, 51, '-1', true);

-- FPX Integration End --

--== CONFIGURE Chase Payment Acquirer ==--

-- PSP tbl config --
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (52, 'Chase Payment',2);
-- END PSP tbl config --
-- Currencys for Chase Payment --
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (840,52,'USD');
-- END Currencys for Chase Payment --

INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (1, 52, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (5, 52, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (7, 52, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (8, 52, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (22, 52, true);

INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, stateid, enabled) VALUES (10007, 1, 52, 200, 1, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, stateid, enabled) VALUES (10007, 5, 52, 200, 1, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, stateid, enabled) VALUES (10007, 7, 52, 200, 1, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, stateid, enabled) VALUES (10007, 8, 52, 200, 1, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, stateid, enabled) VALUES (10007, 22, 52, 200, 1, true);

-- Merchant MID configuration --
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 52, 'nconline1', '', '');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 52, '-1');
-- End Merchant MID configuration --

--== END CONFIGURE Chase Payment Acquirer ==--