INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (53, 'PayU',1);
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (53, 1);	-- American Express
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (53, 3);	-- Diners Club
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (53, 7);	-- MasterCard
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (53, 8);	-- VISA

--Add currency support as required for client
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (840,53,'USD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (986,53,'BRL');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (32,53,'ARS');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (152,53,'CLP');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (170,53,'COP');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (604,53,'PEN');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (484,53,'MXN');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (604,53,'PEN');

--TODO Place holder for additional local card types to support for LATAM
--INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (74, 'Elo', 23, -1, -1, -1,1);
--TODO INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (74, 0, 0);
--INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 74, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 986;
--INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 74, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 152;
--INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (74, 53, true);

--INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (75, 'Hipercard', 23, -1, -1, -1,1);
--TODO INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (75, 0, 0);
--INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 75, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 986;
--INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 75, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 152;
--INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (75, 53, true);


--INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (76, 'Argencard', 23, -1, -1, -1,1);
--TODO INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (76, 0, 0);
--INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 76, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 32;
--INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (76, 53, true);

--INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (77, 'Cabal', 23, -1, -1, -1,1);
--TODO INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (77, 0, 0);
--INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 77, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 32;
--INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (77, 53, true);

--INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (78, 'Cencosud', 23, -1, -1, -1,1);
--TODO INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (78, 0, 0);
--INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 78, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 32;
--INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (78, 53, true);

--INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (79, 'Naranja', 23, -1, -1, -1,1);
--TODO INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (79, 0, 0);
--INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 79, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 32;
--INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (79, 53, true);

--INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (80, 'Shopping', 23, -1, -1, -1,1);
--TODO INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (80, 0, 0);
--INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 80, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 32;
--INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (80, 53, true);
--end todo --


-- Merchant MID configuration --
--Sandbox env details.
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 53, 'PayU LATAM', '', '');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 53, '-1');
--enable Offline Installment option for a PSP - payu if applicable for the client and route.
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment) VALUES (10007, 8, true, 53, 403, 1, null, false, 1,1);

-- Additional properties for API credentials per currency
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) select 'app_id.BRL', 'com.cellpointmobile.cellpointdev', id, 'merchant', 1 from client.merchantaccount_tbl WHERE clientid=10007 AND pspid=53;

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) select 'private_key.BRL', '2c96253a-14e8-4e2f-817e-4ca7775ed08e', id, 'merchant', 1 from client.merchantaccount_tbl WHERE clientid=10007 AND pspid=53;

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) select 'public_key.BRL', '4fff7dd4-3ee1-4295-8c2e-cc35deaacec6', id, 'merchant', 1 from client.merchantaccount_tbl WHERE clientid=10007 AND pspid=53;


--production sql
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (<clientid>, 53, <merchant name>, '', '');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<accountid>, 53, '-1');
--edit if installment is to be enabled for specific SR, 0 means no installment option. 1 means installment is enabled.
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment) VALUES (<clientid>, <cardid>, true, 53, <countryid>, 1, null, false, 1,1);
-- Additional properties for API credentials per currency
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) select app_id.<CUR>, <app-id from payu portal>, id, 'merchant', 1 from client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=53;

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) select private_key.<CUR>, <privatekey from pay portal>, id, 'merchant', 1 from client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=53;

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) select public_key.<CUR>, <public key from payu portal>, id, 'merchant', 1 from client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=53;


