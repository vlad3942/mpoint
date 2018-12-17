--System Schema

--PSP
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (48, 'CHUBB', 1);
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (51, 'eGHL',1);

--Card
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (73, 'FPX', 23, -1, -1, -1,4);

--psp card
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (8, 48, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (7, 48, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (73, 51, true);

--Card prefix
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (73, 0, 0);

--Card Pricing
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 73, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 458;

--psp currency
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (702,48,'SGD');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (840,48,'USD');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (764,48,'THB');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (458,51,'MYR');

--Currency
UPDATE system.country_tbl SET alpha2code = 'MM', alpha3code = 'MMR', code = 104, currencyid = 104 WHERE id = 652;

--Client Schema

--Client
INSERT INTO client.client_tbl (id, countryid, flowid, name, username, passwd, callbackurl, maxamount, lang, emailrcpt, method, terms, enabled, auto_capture, send_pspid, store_card, show_all_cards, max_cards, num_masked_digits, communicationchannels) VALUES (10018, 100, 1, 'Malindo Air', 'odMBE', '852pvRLCZthLgBNB', 'http://od.mretail.cellpointmobile.net/mOrder/sys/mpoint.php', 947483647, 'gb', false, 'mPoint', null, true, false, true, 3, false, 20, 2, 5);
INSERT INTO client.account_tbl (id, clientid, name, mobile, enabled, markup) VALUES (100181, 10018, 'Malindo - App', null, true, 'app');

--Static Route
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 11, true, 1, null, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 1, true, 25, 603, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 7, true, 25, 603, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 8, true, 25, 603, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 7, true, 26, 644, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 8, true, 26, 644, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 1, true, 27, 638, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 7, true, 27, 638, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 8, true, 27, 638, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 7, true, 28, 649, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 7, true, 28, 610, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 7, true, 28, 642, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 7, true, 28, 609, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 7, true, 28, 614, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 7, true, 28, 646, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 7, true, 28, 634, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 7, true, 28, 500, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 7, true, 28, 200, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 7, true, 28, 505, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 7, true, 28, 302, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 7, true, 28, 639, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 7, true, 28, 613, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 7, true, 28, 608, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 7, true, 28, 652, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 8, true, 28, 639, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 8, true, 28, 634, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 8, true, 28, 642, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 8, true, 28, 614, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 8, true, 28, 500, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 8, true, 28, 302, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 8, true, 28, 610, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 8, true, 28, 649, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 8, true, 28, 505, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 8, true, 28, 609, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 8, true, 28, 646, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 8, true, 28, 200, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 8, true, 28, 613, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 8, true, 28, 608, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 8, true, 28, 652, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10018, 73,true, 51, 638, 1, null, false, 4);

--countrycurrency

INSERT INTO client.countrycurrency_tbl (clientid, countryid, currencyid, enabled) VALUES (10018, 302, 840, true);
INSERT INTO client.countrycurrency_tbl (clientid, countryid, currencyid, enabled) VALUES (10018, 610, 840, true);
INSERT INTO client.countrycurrency_tbl (clientid, countryid, currencyid, enabled) VALUES (10018, 639, 840, true);
INSERT INTO client.countrycurrency_tbl (clientid, countryid, currencyid, enabled) VALUES (10018, 649, 840, true);
INSERT INTO client.countrycurrency_tbl (clientid, countryid, currencyid, enabled) VALUES (10018, 613, 840, true);
INSERT INTO client.countrycurrency_tbl (clientid, countryid, currencyid, enabled) VALUES (10018, 652, 840, true);


--merchant account
INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, enabled, username, passwd, stored_card) VALUES (10018, 1, '<name>', true, '<username>', '<password>', null);
INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, enabled, username, passwd, stored_card) VALUES (10018, 25, '<name>', true, '<username>', '<password>', null);
INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, enabled, username, passwd, stored_card) VALUES (10018, 26, '<name>', true, '<username>', '<password>', null);
INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, enabled, username, passwd, stored_card) VALUES (10018, 27, '<name>', true, '<username>', '<password>', null);
INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, enabled, username, passwd, stored_card) VALUES (10018, 28, '<name>', true, '<username>', '<password>', null);
INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, enabled, username, passwd, stored_card) VALUES (10018, 48, '<name>', true, '<username>', '<password>', null);
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10018, 36, 'mvault', '', '');
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10018, 51, '<name>','<username>', '<password>');

--merchant sub account
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (100181, 1, '-1', true);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (100181, 25, '-1', true);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (100181, 26, '-1', true);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (100181, 27, '-1', true);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (100181, 28, '-1', true);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (100181, 48, '-1', true);
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name, enabled) VALUES (100181, 36, '-1', true);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (100181, 51, '-1', true);

--URL
INSERT INTO client.url_tbl (urltypeid, clientid, url, enabled) VALUES (2, 10018, 'http://internal-mesb:10080/mpoint/mprofile/authenticate-user', true);
INSERT INTO client.url_tbl (urltypeid, clientid, url, enabled) VALUES (4, 10018, 'https://od.velocity.cellpointmobile.net', true);


--additional property
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('mvault', 'true', 10018, 'client');
INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'mid.7.MYR', '<MID>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=28;
INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'mid.7.HKD', '<MID>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=28;
INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'mid.7.SGD', '<MID>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=28;
INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'mid.7.AUD', '<MID>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=28;
INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'mid.7.LKR', '<MID>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=28;
INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'mid.7.CNY', '<MID>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=28;
INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'mid.7.THB', '<MID>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=28;
INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'mid.7.TWD', '<MID>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=28;
INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'mid.7.SAR', '<MID>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=28;
INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'mid.7.USD', '<MID>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=28;
INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'mid.7.IDR', '<MID>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=28;
INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'mid.8.MYR', '<MID>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=28;
INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'mid.8.HKD', '<MID>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=28;
INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'mid.8.SGD', '<MID>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=28;
INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'mid.8.AUD', '<MID>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=28;
INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'mid.8.LKR', '<MID>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=28;
INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'mid.8.CNY', '<MID>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=28;
INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'mid.8.THB', '<MID>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=28;
INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'mid.8.TWD', '<MID>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=28;
INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'mid.8.SAR', '<MID>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=28;
INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'mid.8.USD', '<MID>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=28;
INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'mid.8.IDR', '<MID>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=28;

INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'ccavenue.access.key', '<access key>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=25;
INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'ccavenue.working.key', '<working key>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=25;

INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'mid.8', '<MID>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=27;
INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'mid.7', '<MID>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=27;
INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'mid.1', '<MID>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=27;
INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'pwd.8', '<PWD>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=27;
INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'pwd.7', '<PWD>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=27;
INSERT INTO client.additionalproperty_tbl (externalid, key, value, enabled, type) SELECT id, 'pwd.1', '<PWD>', true, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10018 and pspid=27;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('DR_SERVICE', 'true', true, 10018, 'client');

INSERT INTO client.keyword_tbl (clientid, name, standard, enabled) VALUES (10018, 'OD', true, true);