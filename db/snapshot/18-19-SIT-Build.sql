-- FPX Integration End --
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

-- FPX Integration End

-- --INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('HPP_HOST_URL', 'HPP_URL', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('debug', 'true', 10007, 'client');