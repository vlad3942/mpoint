--Datacash

INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (17, 'Data Cash',1);
INSERT INTO system.pspcurrency_tbl (pspid, name, currencyid, enabled) VALUES (17, 'SAR', 682, true);
INSERT INTO system.pspcurrency_tbl (pspid, name, currencyid, enabled) VALUES (17, 'AED', 784, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (1, 17, true); --AMEX--
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (7, 17, true); --MASTERCARD--
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (8, 17, true); --VISA --

--mPoint Client Set up for DataCash
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (<ClientID>, 17, '<MID>', 'merchant.<MID>', '<PASSWORD STRING>');	
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<AccountID>, 17, '-1');
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (<ClientID>, 1, true, 17, 608, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (<ClientID>, 8, true, 17, 608, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (<ClientID>, 7, true, 17, 608, 1, null, false, 1);


--DATACASH SETUP FOR AED CURRENCY:

INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (<ClientID>, 1, true, 17, 647, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (<ClientID>, 8, true, 17, 647, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (<ClientID>, 7, true, 17, 647, 1, null, false, 1);


--Paytabs - SADAD

INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength) VALUES (31, 'SADAD', 23, -1, -1, -1);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (31, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 31, id FROM System.PricePoint_Tbl WHERE amount = -1 AND countryid = 608;
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (38, 'Paytabs',1);
INSERT INTO system.pspcurrency_tbl (pspid, name, currencyid, enabled) VALUES (38, 'SAR', 682, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (31, 38, true); --SADAD--


INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (<ClientID>, 38, 'Paytabs', '<USERNAME>', '<PASSWORD>');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<AccountID>, 38, '-1');
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (<ClientID>, 31, true, 38, 608, 1, null, false, 1);


--- Datacash notification secret key for setting up https callback url
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'Notification-Secret', '<SECRET>', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<ClientID> AND pspid=17 ;