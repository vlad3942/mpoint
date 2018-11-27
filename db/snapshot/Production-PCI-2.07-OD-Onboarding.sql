--System Schema

--PSP
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (48, 'CHUBB', 1);

--psp card
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (8, 48, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (7, 48, true);

--psp currency
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (702,48,'SGD');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (840,48,'USD');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (764,48,'THB');



--Client Schema

--Client
INSERT INTO client.client_tbl (id, countryid, flowid, name, username, passwd, callbackurl, maxamount, lang, emailrcpt, method, terms, enabled, auto_capture, send_pspid, store_card, show_all_cards, max_cards, num_masked_digits, communicationchannels) VALUES (10018, 100, 1, 'Malindo Air', 'odMBE', 'nB4y8BFGfJe5jHXQ', 'http://od.mretail.cellpointmobile.net/mOrder/sys/mpoint.php', 947483647, 'gb', false, 'mPoint', null, true, false, true, 3, false, 20, 2, 5);
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


--countrycurrency

INSERT INTO client.countrycurrency_tbl (clientid, countryid, currencyid, enabled) VALUES (10018, 302, 840, true);
INSERT INTO client.countrycurrency_tbl (clientid, countryid, currencyid, enabled) VALUES (10018, 610, 840, true);
INSERT INTO client.countrycurrency_tbl (clientid, countryid, currencyid, enabled) VALUES (10018, 639, 840, true);
INSERT INTO client.countrycurrency_tbl (clientid, countryid, currencyid, enabled) VALUES (10018, 649, 840, true);


--merchant account

INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, enabled, username, passwd, stored_card) VALUES (10018, 1, '<name>', true, '<username>', '<password>', null);
INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, enabled, username, passwd, stored_card) VALUES (10018, 25, '<name>', true, '<username>', '<password>', null);
INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, enabled, username, passwd, stored_card) VALUES (10018, 26, '<name>', true, '<username>', '<password>', null);
INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, enabled, username, passwd, stored_card) VALUES (10018, 27, '<name>', true, '<username>', '<password>', null);
INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, enabled, username, passwd, stored_card) VALUES (10018, 28, '<name>', true, '<username>', '<password>', null);
INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, enabled, username, passwd, stored_card) VALUES (10018, 48, '<name>', true, '<username>', '<password>', null);


--merchant sub account

INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (100181, 1, '-1', true);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (100181, 25, '-1', true);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (100181, 26, '-1', true);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (100181, 27, '-1', true);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (100181, 28, '-1', true);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (100181, 48, '-1', true);


--URL

INSERT INTO client.url_tbl (urltypeid, clientid, url, enabled) VALUES (2, 10018, 'https://od.voyage.cellpointmobile.net/mpoint/mprofile/authenticate-user', true);
INSERT INTO client.url_tbl (urltypeid, clientid, url, enabled) VALUES (4, 10018, 'https://od.velocity.cellpointmobile.net', true);