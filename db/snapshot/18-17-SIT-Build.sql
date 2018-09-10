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