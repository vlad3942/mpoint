--mPoint
--Client configuration

INSERT INTO client.client_tbl (id, countryid, flowid, name, username, passwd, logourl, cssurl, callbackurl, accepturl, cancelurl, maxamount, lang, smsrcpt, emailrcpt, method, terms, enabled, mode, auto_capture, send_pspid, store_card, iconurl, show_all_cards, max_cards, identification, transaction_ttl, num_masked_digits, salt, declineurl, secretkey, communicationchannels) VALUES (10021, 608, 1, 'SGA', 'SGADemo', 'DEMOisNO_2', null, null, 'http://sga.mretail.uat-01.cellpointmobile.net/mOrder/sys/mpoint.php', 'https://cpm-pay-dev2.cellpointmobile.com/views/index.html', 'https://cpm-pay-dev2.cellpointmobile.com/views/index.html', 100000000, 'gb', true, true, 'mPoint', null, true, 0, true, true, 0, null, false, -1, 7, 0, 4, null, null, null, 0);
INSERT INTO client.account_tbl (id, clientid, name, mobile, enabled, markup) VALUES (100210, 10021, 'SGA App', null, true, 'app');
INSERT INTO client.keyword_tbl (id, clientid, name, standard, enabled) VALUES (51, 10021, 'CPM', true, true);
INSERT INTO client.url_tbl (clientid, urltypeid, url, enabled) VALUES (10021, 14, 'https://s3-ap-southeast-1.amazonaws.com/cpmassets/payment/icons', true);
INSERT INTO client.url_tbl (clientid, urltypeid, url, enabled) VALUES (10021, 4, 'https://sga.uat-01.cellpointmobile.net', true);
INSERT INTO client.url_tbl (clientid, urltypeid, url, enabled) VALUES (10021, 2, 'http://sga.uat-01.cellpointmobile.net/mprofile/login', true);
AMEX PSP Setup

INSERT INTO system.psp_tbl (id, name, system_type, enabled) VALUES (45, 'Amex', 2, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (1, 45, true);
INSERT INTO system.pspcurrency_tbl (pspid, name, currencyid, enabled) VALUES (45, 'SAR', 682, true);
INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, username, passwd, enabled, stored_card) VALUES (10021, 45, '', '', '', true, null);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (100210, 45, '-1', true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10021, 1, true, 45, 608, 1, null, false, 2);


--externalid is id of merchantaccount_tbl for pspid 45 and clientid 10021
INSERT INTO client.additionalproperty_tbl (key, value, enabled, type, externalid) SELECT 'AMEX_ORIGIN', 'Cellpoint Mobile', true, 'merchant', id FROM client.merchantaccount_tbl WHERE pspid=45 AND clientid=10021;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, type, externalid) SELECT 'AMEX_COUNTRY_CODE', '682', true, 'merchant', id FROM client.merchantaccount_tbl WHERE pspid=45 AND clientid=10021;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, type, externalid) SELECT 'AMEX_REGION', 'EMEA', true, 'merchant', id FROM client.merchantaccount_tbl WHERE pspid=45 AND clientid=10021;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, type, externalid) SELECT 'AMEX_MESSAGE_TYPE', 'ISO GCAG', true, 'merchant', id FROM client.merchantaccount_tbl WHERE pspid=45 AND clientid=10021;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, type, externalid) SELECT 'AMEX_MERCHANT_NUMBER', '4417414679', true, 'merchant', id FROM client.merchantaccount_tbl WHERE pspid=45 AND clientid=10021;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, type, externalid) SELECT 'AMEX_ROUTING_INDICATOR', '050', true, 'merchant', id FROM client.merchantaccount_tbl WHERE pspid=45 AND clientid=10021;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, type, externalid) SELECT 'AMEX_CARD_ACCEPTOR_BUSINESS_CODE', '4511', true, 'merchant', id FROM client.merchantaccount_tbl WHERE pspid=45 AND clientid=10021;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, type, externalid) SELECT 'AMEX_CARD_ACCEPTOR_CITY', 'Broby', true, 'merchant', id FROM client.merchantaccount_tbl WHERE pspid=45 AND clientid=10021;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, type, externalid) SELECT 'AMEX_CARD_ACCEPTOR_ADDRESS', 'Boulevard 4', true, 'merchant', id FROM client.merchantaccount_tbl WHERE pspid=45 AND clientid=10021;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, type, externalid) SELECT 'AMEX_CARD_ACCEPTOR_NAME', 'AMEX Tester', true, 'merchant', id FROM client.merchantaccount_tbl WHERE pspid=45 AND clientid=10021;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, type, externalid) SELECT 'AMEX_CARD_ACCEPTOR_TERMINAL_ID', '208752', true, 'merchant', id FROM client.merchantaccount_tbl WHERE pspid=45 AND clientid=10021;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, type, externalid) SELECT 'AMEX_CARD_ACCEPTOR_COUNTRY', 'DKK', true, 'merchant', id FROM client.merchantaccount_tbl WHERE pspid=45 AND clientid=10021;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, type, externalid) SELECT 'AMEX_CARD_ACCEPTOR_REGION', 'DK', true, 'merchant', id FROM client.merchantaccount_tbl WHERE pspid=45 AND clientid=10021;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, type, externalid) SELECT 'AMEX_CARD_ACCEPTOR_ZIP', '85054 ', true, 'merchant', id FROM client.merchantaccount_tbl WHERE pspid=45 AND clientid=10021;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, type, externalid) SELECT 'AMEX_MESSAGE_REASON_CODE', '1100', true, 'merchant', id FROM client.merchantaccount_tbl WHERE pspid=45 AND clientid=10021;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, type, externalid) SELECT 'AMEX_CARD_ACCEPTOR_IDENTIFICATION_CODE', '4417414679', true, 'merchant', id FROM client.merchantaccount_tbl WHERE pspid=45 AND clientid=10021;
-- Only For AMEX 3DS
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('3DVERIFICATION', 'true', 10021, 'client', true);


--Datacash

INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (17, 'Data Cash',1);
INSERT INTO system.pspcurrency_tbl (pspid, name, currencyid, enabled) VALUES (17, 'SAR', 682, true);
INSERT INTO system.pspcurrency_tbl (pspid, name, currencyid, enabled) VALUES (17, 'AED', 784, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (1, 17, true); --AMEX--
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (7, 17, true); --MASTERCARD--
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (8, 17, true); --VISA --
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10021, 17, 'SGBSABB01', 'merchant.SGBSABB01', 'bebd68b2fa491f807e40462a6f85617e');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100210, 17, '-1');
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10021, 1, true, 17, 608, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10021, 8, true, 17, 608, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10021, 7, true, 17, 608, 1, null, false, 1);


--DATACASH SETUP FOR AED CURRENCY:

INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10021, 1, true, 17, 647, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10021, 8, true, 17, 647, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10021, 7, true, 17, 647, 1, null, false, 1);





--Paytabs - SADAD

INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength) VALUES (31, 'SADAD', 23, -1, -1, -1);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (31, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 31, id FROM System.PricePoint_Tbl WHERE amount = -1 AND countryid = 608;
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (38, 'Paytabs',1);
INSERT INTO system.pspcurrency_tbl (pspid, name, currencyid, enabled) VALUES (38, 'SAR', 682, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (31, 38, true); --SADAD--
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10021, 38, 'Paytabs', 'test_sadad@paytabs.com', 'zoVCrg1wOzCN22cXIZt5YM3TnAKoA5paulNWBOtqo6eq8roRqSWoEZh1A2qb7PlCa9yMX2cm8qMgSb7i34HH3ZID19P9YaL9jkVh');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100210, 38, '-1');
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10021, 31, true, 38, 608, 1, null, false, 1);


--html supports MD5 or RSA - Alipay
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'signtype.html', 'RSA', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10007 AND pspid=43 ;
--app supports RSA and RSA2
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'signtype.app', 'RSA2', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10007 AND pspid=43 ;




--- Datacash notification secret key for setting up https callback url
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'Notification-Secret', '379001F6E4852A832F8138F70190585A', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10007 AND pspid=17 ;
