INSERT INTO client.client_tbl
(id, countryid, flowid, "name", username, passwd, logourl, cssurl, callbackurl, accepturl, cancelurl, maxamount, lang, smsrcpt, emailrcpt, "method", terms, enabled, "mode", send_pspid, store_card, iconurl, show_all_cards, max_cards, identification, transaction_ttl, num_masked_digits, declineurl, salt, secretkey, communicationchannels, installment, max_installments, installment_frequency, enable_cvv)
VALUES(10063, 640, 1, 'Payment Automation Client', 'autocebu', 'autocebuqa', 'https://hpp-uat-02.cellpointmobile.net/css/swag/img/cebu.png', 'https://devcpmassets.s3-ap-southeast-1.amazonaws.com', 'https://hpp2.sit-01.cellpoint.dev/views/redirect_response.php', 'https://hpp2.sit-01.cellpoint.dev/views/redirect_response.php', 'https://hpp2.sit-01.cellpoint.dev/views/redirect_response.php', 2147483600, 'gb', false, false, 'mPoint', NULL, true, 1, true, 0, NULL, false, -1, 1, 0, 4, 'https://cebu.dev:8989/booking-confirmation?decline', NULL, NULL, 0, 0, 0, 0, true);

INSERT INTO client.account_tbl (id, clientid, "name", mobile, enabled, markup, businesstype) VALUES(100630, 10063, 'Web Storefront', NULL, true, 'html5', 0);

INSERT INTO client.url_tbl (urltypeid, clientid, url, enabled) VALUES(2, 10063, 'http://mpoint.dev-01.cellpoint.dev/_test/simulators/login.php', true);
INSERT INTO client.url_tbl (urltypeid, clientid, url, enabled) VALUES(4, 10063, 'https://autocebu.mesb.dev.cpm.dev:443', true); 
INSERT INTO client.url_tbl (urltypeid, clientid, url, enabled) VALUES(14, 10063, 'https://cpd-hpp2-devassests.s3.eu-central-1.amazonaws.com/10063', true);

INSERT INTO client.keyword_tbl (clientid, "name", standard, enabled) VALUES(10063, 'CPD', true, true);

INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10063, 8, true, 4, 640, 1, NULL, false, 1, 0, 0, 3, NULL, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10063, 7, true, 4, 640, 1, NULL, false, 1, 0, 0, 3, NULL, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10063, 8, false, 40, 640, 1, NULL, false, 1, 0, 0, 3, NULL, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10063, 7, false, 40, 640, 1, NULL, false, 1, 0, 0, 3, NULL, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10063, 8, true, 62, 100, 1, NULL, false, 1, 0, 0, 3, NULL, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10063, 7, true, 62, 100, 1, NULL, false, 1, 0, 0, 3, NULL, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10063, 93, false, 40, 640, 1, NULL, false, 3, 0, 0, 2, NULL, false);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10063, 7, true, 65, NULL, 1, NULL, false, 9, 0, 0, 1, NULL, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10063, 8, true, 65, NULL, 1, NULL, false, 9, 0, 0, 1, NULL, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10063, 5, false, 64, NULL, 1, NULL, false, 10, 0, 0, 1, NULL, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10063, 7, true, 64, NULL, 1, NULL, false, 10, 0, 0, 1, NULL, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10063, 26, true, 71, NULL, 1, NULL, false, 1, 0, 0, 1, NULL, false);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10063, 5, false, 62, NULL, 1, NULL, false, 1, 0, 0, 2, NULL, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10063, 8, true, 64, NULL, 1, NULL, false, 10, 0, 0, 1, NULL, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10063, 5, true, 65, NULL, 1, NULL, false, 9, 0, 0, 1, NULL, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10063, 1, true, 65, NULL, 1, NULL, false, 9, 0, 0, 1, NULL, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10063, 94, false, 67, 640, 1, NULL, false, 4, 0, 0, 2, NULL, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10063, 95, false, 68, 640, 1, NULL, false, 4, 0, 0, 2, NULL, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10063, 96, false, 69, 640, 1, NULL, false, 4, 0, 0, 2, NULL, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10063, 28, true, 24, 640, 1, NULL, false, 4, 0, 0, 2, NULL, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10063, 7, true, 47, 640, 1, NULL, false, 6, 0, 0, 1, NULL, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10063, 8, true, 47, 640, 1, NULL, false, 6, 0, 0, 1, NULL, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10063, 1, true, 47, 640, 1, NULL, false, 6, 0, 0, 1, NULL, false);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10063, 5, false, 72, NULL, 1, NULL, false, 1, 0, 0, 1, NULL, false);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10063, 8, false, 72, NULL, 1, NULL, false, 1, 0, 0, 1, NULL, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10063, 7, false, 72, NULL, 1, NULL, false, 1, 0, 0, 1, NULL, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10063, 1, true, 63, NULL, 1, NULL, false, 1, 0, 0, 2, NULL, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10063, 1, true, 64, NULL, 1, NULL, false, 10, 0, 0, 1, NULL, true);



INSERT INTO client.merchantaccount_tbl (clientid, pspid, "name", enabled, username, passwd, stored_card, supportedpartialoperations) VALUES(10063, 4, 'CEBUAIRECJPY', true, 'CEBUAIRECJPY', 'live2020!', NULL, 0);
INSERT INTO client.merchantaccount_tbl (clientid, pspid, "name", enabled, username, passwd, stored_card, supportedpartialoperations) VALUES(10063, 24, 'ACCu12.jik2Wb3kzHJgFE1palQEsAHD7wsynoTYqRcOZAX7RzupgW4sQ', true, 'sb-sahh431638744_api1.business.example.com', '7W56K2VQBRYF8FLX', NULL, 0);
INSERT INTO client.merchantaccount_tbl (clientid, pspid, "name", enabled, username, passwd, stored_card, supportedpartialoperations) VALUES(10063, 36, 'mVault', true, 'Blank', 'Blank', NULL, 0);
INSERT INTO client.merchantaccount_tbl (clientid, pspid, "name", enabled, username, passwd, stored_card, supportedpartialoperations) VALUES(10063, 40, '2c2p-alc', true, 'CELLPM', 'HC1XBPV0O4WLKZMG', NULL, 0);
INSERT INTO client.merchantaccount_tbl (clientid, pspid, "name", enabled, username, passwd, stored_card, supportedpartialoperations) VALUES(10063, 47, 'MODIRUM MPI', true, '9449005362', '-----BEGIN PRIVATE KEY-----
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
j6a80YoNqFBPMFpDUeXRaCo=
-----END PRIVATE KEY-----', NULL, 0);
INSERT INTO client.merchantaccount_tbl (clientid, pspid, "name", enabled, username, passwd, stored_card, supportedpartialoperations) VALUES(10063, 62, '6160800000', true, 'WS6160800000._.1', 'tester01$', NULL, 0);
INSERT INTO client.merchantaccount_tbl (clientid, pspid, "name", enabled, username, passwd, stored_card, supportedpartialoperations) VALUES(10063, 63, 'CyberSourceAMEX', true, 'cebu_cellpoint_test', 'K/B7APZOVPoPCFvSIyqMpvUmeDCAyyd0aWXnIHFQqBnSBwc1PDXRVZCS8DazLnCSXZUuauffLNY0lxJpoR8/e94VJbzKVK+Dzxmhl3hkS0qnmk/ZJFcd2Huh80UK5qG2TwB2inqPacECAGBLk5steF6UlALDYuMOvJuVinUW84VEpxUJ1Dntmm4AhNpB2pUheytX4XjhoodDerjGZGg61Ps4xHxqNl29huaumNYIoCfGNchX5vkKi8uBoPwJCpbBO0ORUy9sgMQOk1w7DTNVSCvkpbF+LH3VdFV/3N8kU9z/ONKLF2zPq5aWjC861EjQo1mAqiZBjg8Afof3CsDQ0Q==', NULL, 0);
INSERT INTO client.merchantaccount_tbl (clientid, pspid, "name", enabled, username, passwd, stored_card, supportedpartialoperations) VALUES(10063, 64, 'CyberSourceFraud', true, 'cebupacific', 'VtjDJ88qyiETria6qKc7diTi5yuVmFiuBPPAICcn3IMiqxhRi4GXPYvXODnm1maxB8Ka8F9SE8g8TnbovNDMQvnTdSCKUMuUsA2bygN+80QezO+LLEQ7cYpmCTcrHf4dVTUHw0YFZrBIwhElmFIMvJ4VOG6YRtCmUi7iCVpJKTU1kvO9tvi4pea7EVgH2xgv7jd2YziB+ViTDET1dKwCjIklLS5QY+6u3/uzRHrW9FZqbyY6brtgyyKTL2aQhhqEB7wzzwzPn3w0C/w7jLPd2r5VngHgkq/Gjfnl+386D5ZjEiFm353+yS8FG2HqNBPIwrnFYJrPOlmLGYwjXBRm7A==', NULL, 0);
INSERT INTO client.merchantaccount_tbl (clientid, pspid, "name", enabled, username, passwd, stored_card, supportedpartialoperations) VALUES(10063, 65, 'CEBU-RMFSS', true, 'By9AjPV6j14jgb3DXRIpW0mInOfMEafS', 'E9NBawrSH6UAtw1v', NULL, 0);
INSERT INTO client.merchantaccount_tbl (clientid, pspid, "name", enabled, username, passwd, stored_card, supportedpartialoperations) VALUES(10063, 67, 'dbb00e18-83ee-49cf-b54d-2707a069b3e4', true, '0112218e-dda0-4ca8-8489-65a3d28abd69', 'apWSvBQj_evmVfzY', NULL, 0);
INSERT INTO client.merchantaccount_tbl (clientid, pspid, "name", enabled, username, passwd, stored_card, supportedpartialoperations) VALUES(10063, 68, 'paymaya', true, 'pk-cjighcbE1MzcMlDzs4WgncWT4CqYzN4w9FfIJVVclTt', 'sk-xsmleqi15WM6rsid1L8jCV9HHahZqFqAmX5jieFVIzE', NULL, 0);
INSERT INTO client.merchantaccount_tbl (clientid, pspid, "name", enabled, username, passwd, stored_card, supportedpartialoperations) VALUES(10063, 69, 'CEBU Payment Center', true, '', '', NULL, 0);
INSERT INTO client.merchantaccount_tbl (clientid, pspid, "name", enabled, username, passwd, stored_card, supportedpartialoperations) VALUES(10063, 71, '', true, '', 'empty', NULL, 0);
INSERT INTO client.merchantaccount_tbl (clientid, pspid, "name", enabled, username, passwd, stored_card, supportedpartialoperations) VALUES(10063, 72, 'TEST048583918507', true, 'merchant.TEST048583918507', '42b5c09392e50702e05f29c37c75841a', NULL, 0);

INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(100630, 4, '-1', true);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(100630, 40, '-1', true);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(100630, 62, '-1', true);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(100630, 63, '-1', true);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(100630, 24, '-1', true);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(100630, 65, '-1', true);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(100630, 64, '-1', true);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(100630, 36, '-1', true);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(100630, 47, '-1', true);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(100630, 67, '-1', true);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(100630, 69, '-1', true);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(100630, 68, 'paymaya', true);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(100630, 72, '-1', true);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(100630, 71, '-1', true);


INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('IssuerAddress1', 'CEBU PACIFIC BUILDING, DOMESTIC ROAD, BARANGAY 191, ZONE 20, PASAY CITY 1301 PHILIPPINES', true, 33, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('IssuerPostalCode', '1301', true, 33, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('IssuerCity', 'PASAY CITY', true, 33, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('IssuerCountryCode', 'PH', true, 33, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('FareBasisCode', 'BK', true, 33, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('RestrictedTicket', '1', true, 33, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('TravelAgencyCode', '5J', true, 33, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('TravelAgencyName', 'CebuPacificair', true, 33, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mpi_rule', 'isSkippAuth::=<status>!=="1"AND<status>!=="2"AND<status>!=="4"AND<status>!=="5"AND<status>!=="6"
status::=(card.info-3d-secure.additional-data.param[@name=''status''])', true, 33, 'merchant', 0);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('post_fraud_rule', 'isPostFraudAttemp::=<status>=="1"OR<status>=="4"

status::=(card.info-3d-secure.additional-data.param[@name=''status''])', true, 33, 'merchant', 0);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.USD', 'CebuPacific_USD', true, 35, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.PHP', 'CebuPacific_MCC', true, 35, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('post_fraud_rule', 'isPostFraudAttemp::=<pspid>=="40"
pspid::=(psp-config.@id)', true, 35, 'merchant', 0);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('3DVERIFICATION', 'true', true, 36, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('post_fraud_rule', 'isPostFraudAttemp::=<status>=="1"OR<status>=="2"OR<status>=="4"
status::=(card.info-3d-secure.additional-data.param[@name=''status''])', true, 36, 'merchant', 0);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('3DVERIFICATION', 'true', true, 33, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('3DSVERSION', '1.0', true, 10063, 'client', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('PAYPAL_ORDER_NUMBER_PREFIX', 'Cebu Pacific Air - ', true, 10063, 'client', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('invoiceidrule', 'invoiceid ::= (psp-config/@id)=="24"OR(psp-config/@id)=="40"=(transaction.@id)', true, 10063, 'client', 0);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('isnewcardconfig', 'true', true, 10063, 'client', 0);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('TIMEZONE', 'Asia/Manila', true, 10063, 'client', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('HPP_HOST_URL', 'cpm-pay-dev2.cellpointmobile.com', true, 10063, 'client', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('ISROLLBACK_ON_FRAUD_FAIL', 'true', true, 10063, 'client', 0);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('IS_STORE_BILLING_ADDRS', 'true', true, 10063, 'client', 0);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('enableHppAuthentication', 'false', false, 10063, 'client', 0);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mandateBillingDetails', 'true', true, 10063, 'client', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('isAutoRedirect', 'true', true, 10063, 'client', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('getTxnStatusPollingTimeOut', '10', true, 10063, 'client', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('webSessionTimeout', '13', true, 10063, 'client', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('showBillingDetails', 'true', true, 10063, 'client', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('3DVERIFICATION', 'true', true, 10063, 'client', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('hppFormRedirectMethod', 'GET', true, 10063, 'client', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('sessiontype', '2', true, 10063, 'client', 0);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('CLIENT_3DS_ENABLE', 'false', false, 10063, 'client', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('googleAnalyticsId', '%7B%22id%22%3A%22GTM-TJHF9HX%22%2C%22auth%22%3A%220bNRJejIX9RvP164Mor_Tw%22%2C%22preview%22%3A%22env-61%22%2C%22env%22%3A%22sit%22%7D', true, 10063, 'client', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('VeriTrans4G_JPO', '10', true, 10063, 'client', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('VeriTrans4G_TXN_VERSION', '2.0.0', true, 10063, 'client', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('VeriTrans4G_DUMMY_REQUEST', '1', true, 10063, 'client', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('VeriTrans4G_BROWSER_DEVICE_CATEGORY', '0', true, 10063, 'client', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('VeriTrans4G_SERVICE_OPTION_TYPE', 'mpi', true, 10063, 'client', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mvault', 'true', true, 10063, 'client', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('AIRLINE_CODE', '6S', true, 10063, 'client', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('CARRIER_NAME', 'SAUDI GULF AIRLINES', true, 10063, 'client', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('PROFILE_EXPIRY', '180', true, 10063, 'client', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('ENABLE_PROFILE_ANONYMIZATION', 'true', true, 10063, 'client', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('isVoucherPreferred', 'false', true, 10063, 'client', 0);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('SplitPaymentConfig', '{"split_count":2,"combinations":[{"combination":[{"index":1,"id":1,"is_clubbable":true},{"index":2,"id":2,"is_clubbable":true}]}]}', true, 10063, 'client', 0);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('isVoucherPreferred', 'false', false, 10063, 'client', 0);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('SplitPaymentFOPConfig', '{"1":-1,"2":-1}', true, 10063, 'client', 0);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('max_session_retry_count', '1000', true, 10063, 'client', 0);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('SSO_PREFERENCE', 'LOOSE', true, 10063, 'client', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('FOP_SELECTION', 'true', false, 10063, 'client', 0);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('DR_SERVICE', 'true', false, 10063, 'client', 0);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('IS_LEGACY', 'true', true, 10063, 'client', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('3DVERIFICATION', 'mpi', true, 151, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('PAYPAL_PASSWORD_SGD', 'B9WX2HPY9DPD6284', true, 153, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('PAYPAL_MID_SGD', 'ATG2YLmUZFFk6n5kHVGwCC2A2dsDAEAdhFEakpPDRN5lxmGq3zimTG6A', true, 153, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('PAYPAL_USERNAME_HKD', 'sb-ph1ko1832308_api1.business.example.com', true, 153, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('PAYPAL_MID_HKD', 'A78isDTCcuwhNZKyvzOsXXSzxdUPA25dTmhkEQxH7G1T4iQSksAr-SK0', true, 153, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('PAYPAL_USERNAME_MYR', 'sb-ivizq1858258_api1.business.example.com', true, 153, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('PAYPAL_PASSWORD_MYR', 'VMXEJAT9DCLCR7LQ', true, 153, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('PAYPAL_MID_MYR', 'AJXtiLishvWwk7Jm0EnHXPmvJv1xAqMsrFcJMX8Exsbl9aQ5NJRKPyem', true, 153, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('PAYPAL_USERNAME_USD', 'sb-43kvng1868465_api1.business.example.com', true, 153, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('PAYPAL_PASSWORD_USD', '37JT6WGJFFUJFRM3', true, 153, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('PAYPAL_MID_USD', 'Awzp6NMJcMOsM6SlpR13Cez-7vPFAMODxiW5ZT0qx6EbatLFSrMoBKtc', true, 153, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('PAYPAL_USERNAME_SGD', 'sb-mohn91867880_api1.business.example.com', true, 153, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('PAYPAL_PASSWORD_HKD', '5QBM4GMSFPV8AHNK', true, 153, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('PAYPAL_MID_PHP', 'ACCu12.jik2Wb3kzHJgFE1palQEsAHD7wsynoTYqRcOZAX7RzupgW4sQ', true, 153, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('PAYPAL_PASSWORD_PHP', '7W56K2VQBRYF8FLX', true, 153, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('PAYPAL_USERNAME_PHP', 'sb-sahh431638744_api1.business.example.com', true, 153, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('PAYPAL_PASSWORD_AUD', 'A5R2XGLF3JRBTUSV', true, 153, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('PAYPAL_USERNAME_AUD', 'sb-jeyzs914045_api1.business.example.com', true, 153, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('PAYPAL_MID_AUD', 'AIRfufj84zXiRCWyOpif2Up4pcJCAwICOSNjHMtgGxB7bHfzRvB4cRJs', true, 153, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('CLIENT_ID', '14c3e87ce4e04e82954fd78cea2b3a64', true, 154, 'merchant', 1);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('CLIENT_SECRET', 'dcyDLGEYkeLZA1YM', true, 154, 'merchant', 1);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('DEFAULT_EMAIL_ID', 'null@cybersource.com', true, 155, 'merchant', 1);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('HOST', 'gpmnl.gateway.mastercard.com', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.AED', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.AUD', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.BHD', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.BND', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.CAD', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.CHF', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.CNY', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.DKK', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.EUR', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.GBP', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.HKD', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.IDR', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.INR', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.JPY', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.KRW', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.KWD', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.LKR', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.MOP', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.MYR', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.NOK', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.NZD', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.PHP', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.QAR', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.SAR', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.SEK', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.SGD', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.THB', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.TWD', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.USD', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.USD', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('Notification-Secret.VND', '379001F6E4852A832F8138F70190585B', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.AED', 'TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.AUD', 'TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.BHD', 'TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.BND', 'TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.CAD', 'TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.CHF', 'TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.CNY', 'TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.DKK', 'TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.EUR', 'TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.GBP', 'TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.HKD', 'TEST088008881200', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.IDR', 'TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.INR', 'TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.JPY', 'TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.KRW', 'TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.KWD', 'TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.LKR', 'TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.MOP', 'TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.MYR', 'TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.NOK', 'TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.NZD', 'TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.PHP', 'TEST048583918507', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.QAR', 'TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.SAR', 'TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.SEK', 'TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.SGD', 'TEST065004485703', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.THB', 'TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.TWD', 'TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.USD', 'TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.USD', 'TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('mid.VND', 'TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.AED', '78ebecfdcd194aac7c1e5fde0b584f40', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.AUD', '78ebecfdcd194aac7c1e5fde0b584f40', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.BHD', '78ebecfdcd194aac7c1e5fde0b584f40', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.BND', '78ebecfdcd194aac7c1e5fde0b584f40', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.CAD', '78ebecfdcd194aac7c1e5fde0b584f40', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.CHF', '78ebecfdcd194aac7c1e5fde0b584f40', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.CNY', '78ebecfdcd194aac7c1e5fde0b584f40', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.DKK', '78ebecfdcd194aac7c1e5fde0b584f40', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.EUR', '78ebecfdcd194aac7c1e5fde0b584f40', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.GBP', '78ebecfdcd194aac7c1e5fde0b584f40', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.HKD', '7f8945cebdfd056530baca4e3e8d384a', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.IDR', '78ebecfdcd194aac7c1e5fde0b584f40', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.INR', '78ebecfdcd194aac7c1e5fde0b584f40', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.JPY', '78ebecfdcd194aac7c1e5fde0b584f40', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.KRW', '78ebecfdcd194aac7c1e5fde0b584f40', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.KWD', '78ebecfdcd194aac7c1e5fde0b584f40', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.LKR', '78ebecfdcd194aac7c1e5fde0b584f40', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.MOP', '78ebecfdcd194aac7c1e5fde0b584f40', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.MYR', '78ebecfdcd194aac7c1e5fde0b584f40', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.NOK', '78ebecfdcd194aac7c1e5fde0b584f40', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.NZD', '78ebecfdcd194aac7c1e5fde0b584f40', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.PHP', '42b5c09392e50702e05f29c37c75841a', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.QAR', '78ebecfdcd194aac7c1e5fde0b584f40', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.SAR', '78ebecfdcd194aac7c1e5fde0b584f40', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.SEK', '78ebecfdcd194aac7c1e5fde0b584f40', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.SGD', 'c1e2d0ffb6f52218be89ac0061af1f2a', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.THB', '78ebecfdcd194aac7c1e5fde0b584f40', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.TWD', '78ebecfdcd194aac7c1e5fde0b584f40', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.USD', '78ebecfdcd194aac7c1e5fde0b584f40', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.USD', '78ebecfdcd194aac7c1e5fde0b584f40', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('password.VND', '78ebecfdcd194aac7c1e5fde0b584f40', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.AED', 'merchant.TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.AUD', 'merchant.TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.BHD', 'merchant.TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.BND', 'merchant.TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.CAD', 'merchant.TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.CHF', 'merchant.TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.CNY', 'merchant.TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.DKK', 'merchant.TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.EUR', 'merchant.TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.GBP', 'merchant.TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.HKD', 'merchant.TEST088008881200', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.IDR', 'merchant.TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.INR', 'merchant.TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.JPY', 'merchant.TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.KRW', 'merchant.TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.KWD', 'merchant.TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.LKR', 'merchant.TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.MOP', 'merchant.TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.MYR', 'merchant.TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.NOK', 'merchant.TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.NZD', 'merchant.TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.PHP', 'merchant.TEST048583918507', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.QAR', 'merchant.TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.SAR', 'merchant.TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.SEK', 'merchant.TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.SGD', 'merchant.TEST065004485703', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.THB', 'merchant.TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.TWD', 'merchant.TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.USD', 'merchant.TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.USD', 'merchant.TEST048583918508', true, 159, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('username.VND', 'merchant.TEST048583918508', true, 159, 'merchant', 2);


INSERT INTO client.route_tbl (id, clientid, providerid, enabled) VALUES(487, 10063, 62, true);
INSERT INTO client.route_tbl (id, clientid, providerid, enabled) VALUES(486, 10063, 40, true);
INSERT INTO client.route_tbl (id, clientid, providerid, enabled) VALUES(488, 10063, 63, true);
INSERT INTO client.route_tbl (id, clientid, providerid, enabled) VALUES(489, 10063, 24, true);
INSERT INTO client.route_tbl (id, clientid, providerid, enabled) VALUES(485, 10063, 4, true);
INSERT INTO client.route_tbl (id, clientid, providerid, enabled) VALUES(610, 10063, 36, true);
INSERT INTO client.route_tbl (id, clientid, providerid, enabled) VALUES(653, 10063, 65, true);
INSERT INTO client.route_tbl (id, clientid, providerid, enabled) VALUES(655, 10063, 64, false);
INSERT INTO client.route_tbl (id, clientid, providerid, enabled) VALUES(569, 10063, 47, true);
INSERT INTO client.route_tbl (id, clientid, providerid, enabled) VALUES(882, 10063, 67, true);
INSERT INTO client.route_tbl (id, clientid, providerid, enabled) VALUES(793, 10063, 68, true);
INSERT INTO client.route_tbl (id, clientid, providerid, enabled) VALUES(883, 10063, 69, true);
INSERT INTO client.route_tbl (id, clientid, providerid, enabled) VALUES(794, 10063, 71, true);
INSERT INTO client.route_tbl (id, clientid, providerid, enabled) VALUES(884, 10063, 72, true);


INSERT INTO client.routeconfig_tbl (routeid, "name", capturetype, mid, username, "password", enabled, isdeleted) VALUES(489, 'Paypal_USD', 2, 'Awzp6NMJcMOsM6SlpR13Cez-7vPFAMODxiW5ZT0qx6EbatLFSrMoBKtc', 'sb-43kvng1868465_api1.business.example.com', '37JT6WGJFFUJFRM3', true, false);
INSERT INTO client.routeconfig_tbl (routeid, "name", capturetype, mid, username, "password", enabled, isdeleted) VALUES(489, 'Paypal_MYR', 2, 'AJXtiLishvWwk7Jm0EnHXPmvJv1xAqMsrFcJMX8Exsbl9aQ5NJRKPyem', 'sb-ivizq1858258_api1.business.example.com', 'VMXEJAT9DCLCR7LQ', true, false);
INSERT INTO client.routeconfig_tbl (routeid, "name", capturetype, mid, username, "password", enabled, isdeleted) VALUES(489, 'Paypal_HKD', 2, 'A78isDTCcuwhNZKyvzOsXXSzxdUPA25dTmhkEQxH7G1T4iQSksAr-SK0', 'sb-ph1ko1832308_api1.business.example.com', '5QBM4GMSFPV8AHNK', true, false);
INSERT INTO client.routeconfig_tbl (routeid, "name", capturetype, mid, username, "password", enabled, isdeleted) VALUES(489, 'Paypal_SGD', 2, 'ATG2YLmUZFFk6n5kHVGwCC2A2dsDAEAdhFEakpPDRN5lxmGq3zimTG6A', 'sb-mohn91867880_api1.business.example.com', 'B9WX2HPY9DPD6284', true, false);
INSERT INTO client.routeconfig_tbl (routeid, "name", capturetype, mid, username, "password", enabled, isdeleted) VALUES(489, 'Paypal_AUD', 2, 'AIRfufj84zXiRCWyOpif2Up4pcJCAwICOSNjHMtgGxB7bHfzRvB4cRJs', 'sb-jeyzs914045_api1.business.example.com', 'A5R2XGLF3JRBTUSV', true, false);
INSERT INTO client.routeconfig_tbl (routeid, "name", capturetype, mid, username, "password", enabled, isdeleted) VALUES(882, 'GrabPay', 2, 'dbb00e18-83ee-49cf-b54d-2707a069b3e4', '0112218e-dda0-4ca8-8489-65a3d28abd69', 'apWSvBQj_evmVfzY', true, false);
INSERT INTO client.routeconfig_tbl (routeid, "name", capturetype, mid, username, "password", enabled, isdeleted) VALUES(793, 'payMaya', 2, '102446547', 'pk-cjighcbE1MzcMlDzs4WgncWT4CqYzN4w9FfIJVVclTt', 'sk-xsmleqi15WM6rsid1L8jCV9HHahZqFqAmX5jieFVIzE', true, false);
INSERT INTO client.routeconfig_tbl (routeid, "name", capturetype, mid, username, "password", enabled, isdeleted) VALUES(794, 'travelfund', 2, 'travelfund', 'travelfund', 'travelfund', true, false);
INSERT INTO client.routeconfig_tbl (routeid, "name", capturetype, mid, username, "password", enabled, isdeleted) VALUES(485, 'TestWorldPay', 3, 'CELLPOINT', 'CELLPOINT', 'Mesb@1234', true, false);
INSERT INTO client.routeconfig_tbl (routeid, "name", capturetype, mid, username, "password", enabled, isdeleted) VALUES(487, 'Test_first-data', 2, '6160800000XX', 'WS6160800000._.1', 'tester01$', true, false);
INSERT INTO client.routeconfig_tbl (routeid, "name", capturetype, mid, username, "password", enabled, isdeleted) VALUES(489, 'Paypal_PHP', 2, 'ACCu12.jik2Wb3kzHJgFE1palQEsAHD7wsynoTYqRcOZAX7RzupgW4sQ', 'sb-sahh431638744_api1.business.example.com', '7W56K2VQBRYF8FLX', true, false);
INSERT INTO client.routeconfig_tbl (routeid, "name", capturetype, mid, username, "password", enabled, isdeleted) VALUES(884, 'MPGS_PHP', 2, 'TEST048583918507', 'merchant.TEST048583918507', '42b5c09392e50702e05f29c37c75841a', true, false);
INSERT INTO client.routeconfig_tbl (routeid, "name", capturetype, mid, username, "password", enabled, isdeleted) VALUES(486, '2c2p-alc', 2, '2c2p-alc', 'CELLPM', 'HC1XBPV0O4WLKZMG', true, false);
INSERT INTO client.routeconfig_tbl (routeid, "name", capturetype, mid, username, "password", enabled, isdeleted) VALUES(488, 'CyberSourceAMEX', 2, 'CyberSourceAMEX', 'cebu_cellpoint_test', 'K/B7APZOVPoPCFvSIyqMpvUmeDCAyyd0aWXnIHFQqBnSBwc1PDXRVZCS8DazLnCSXZUuauffLNY0lxJpoR8/e94VJbzKVK+Dzxmhl3hkS0qnmk/ZJFcd2Huh80UK5qG2TwB2inqPacECAGBLk5steF6UlALDYuMOvJuVinUW84VEpxUJ1Dntmm4AhNpB2pUheytX4XjhoodDerjGZGg61Ps4xHxqNl29huaumNYIoCfGNchX5vkKi8uBoPwJCpbBO0ORUy9sgMQOk1w7DTNVSCvkpbF+LH3VdFV/3N8kU9z/ONKLF2zPq5aWjC861EjQo1mAqiZBjg8Afof3CsDQ0Q==', true, false);
INSERT INTO client.routeconfig_tbl (routeid, "name", capturetype, mid, username, "password", enabled, isdeleted) VALUES(655, 'Cyber Source FSP', 2, 'Cyber Source FSP', 'cebupacific', 'VtjDJ88qyiETria6qKc7diTi5yuVmFiuBPPAICcn3IMiqxhRi4GXPYvXODnm1maxB8Ka8F9SE8g8TnbovNDMQvnTdSCKUMuUsA2bygN+80QezO+LLEQ7cYpmCTcrHf4dVTUHw0YFZrBIwhElmFIMvJ4VOG6YRtCmUi7iCVpJKTU1kvO9tvi4pea7EVgH2xgv7jd2YziB+ViTDET1dKwCjIklLS5QY+6u3/uzRHrW9FZqbyY6brtgyyKTL2aQhhqEB7wzzwzPn3w0C/w7jLPd2r5VngHgkq/Gjfnl+386D5ZjEiFm353+yS8FG2HqNBPIwrnFYJrPOlmLGYwjXBRm7A==', true, false);
INSERT INTO client.routeconfig_tbl (routeid, "name", capturetype, mid, username, "password", enabled, isdeleted) VALUES(610, 'mVault', 2, 'mVault', 'blank', 'blank', true, false);
INSERT INTO client.routeconfig_tbl (routeid, "name", capturetype, mid, username, "password", enabled, isdeleted) VALUES(653, 'CEBU-RMFSS', 2, 'CEBU-RMFSS', 'By9AjPV6j14jgb3DXRIpW0mInOfMEafS', 'E9NBawrSH6UAtw1v', true, false);
INSERT INTO client.routeconfig_tbl (routeid, "name", capturetype, mid, username, "password", enabled, isdeleted) VALUES(569, 'MODIRUM MPI', 2, 'MODIRUM MPI', '9449005362', '-----BEGIN PRIVATE KEY-----
MIIG/QIBADANBgkqhkiG9w0BAQEFAASCBucwggbjAgEAAoIBgQDhdJ9ei+suxKSs
uTxRVDzFrWVhsv4B+KvJ7HPnt6WSBDxPGcW+vCBtoJIuudq1H7zraLINDzXKWTt3
EJZ2iEDvxnovOKS4or9Yu9DxdBxasJld+VRIfIGaU8IvL/QXB5b6rJbo/8kFqE5v
l+6LWFHbbIKaWYt2hAptteNJ2NkbkQiudMEpyW8zVffO9pJxa2MNox9u5aFNCyhm
qQaPnfaDDDfFEPeXhWZ7fZGIceaVl1pyLgZvqD24ADHYhCD8+wX5Z/W12upJt750
N3cgO4hsheDGOHkMvRxmU3+FtpNo/9TJQ6NFK9tJXlJ681Y4bmOf+TjHYCj9a1eO
o8JtHl480ItcsFqzuqJv2Y4uuT4qdFdEEvnbj8/4k+QkuSFIG9b0U1Wsi5jfHUJx
x4tx8631TUQJWiJf3tXhwi89UhUQ7x5HTPijX4WqMDEGS9wElVNE25g3GimK7+cR
KuyD0XGAYVZ92SR+SlRlJXUYLQpzCAEfsVbUBlu9kmCqQQNRhgUCAwEAAQKCAYBa
T2j3anzOwx5jwd+ThHbDiG2v8Q8mowHNZNYY38uG7gNqfBxmBt3GjCeCaBhfrVLz
yYBpEsqtP4k+bHqZCINaiNqwx7PT8f1MAg/0aFpjSZJlvlYwow8XTq3eUQspnnSv
/SqGMs/HYF/q+6UmVD6V8qVuRqhC1SmIQO/GyQJOtI4Rs9scbgAJREp7sPGAVU/c
SpdwyLMQNYP9e3+RT+lLHT3Jxr4nB9zBYs9FJLsE+RfV3PHfEDmI1ysgW+0K6VH+
mTYZyDy0nUSLwIhotRUDsZztE864gH4RzYVUPsMCjIaFX81LpFMHqy8skvrkoN0O
OyeMmObglSH2xtYi9sXBmGP8tv/QD5tfaLrt0HMCE2CrF3TsL3gl0pwpxABVDTOr
0sC+XvHdajCRWwQRfeDKjdX3qzFoOkjYakwmZuF/FT8L4mY2k3C2pkto3di0cJoX
av691PW1Vbxf/SAjg/F/0wMt3BPyVqh9kjFhRVHlrNJHklzOrHiwEzvowP4Pz4EC
gcEA/NWXLK/312VLYJyXeGx640x450vvtf03JWJEqTtdLOq3eCBkz20m+z8Ugxkj
zLqOJRJJjEmf+BCuq7OtlzkRdPFDwIouFvMufkHGntiXvMAmzvgpwiT2k4RLaX0R
wyxBFhOchzemUj9P6OuwO7UMA6C5dVA90Utgm/Y3eY9Qw3t98fVT8Ta1FFH4DZvl
7LHOJ/KLQCpbORFY1xqQsu9nH5HOndc2YKjSKdOQzHuBChIRySaDmb3/AficjRAu
3yN5AoHBAORHRlyPe0/wJEX9rPlRZV8IVFdeegGbIFODb1jLVqzk2lz5F0OHGAuE
Kc3cOwGHbZNRcavQBDpnJ+6D1E0XQqKtkega0XaY7HHOcaUQbNSKdusPMFT5//+n
79RQcMqWLOQ7d0qzAQ6kKMwSN7EEr4OcYzS7/Hk+D/JAyXUEGpDeA/Hwh1CCiWrG
hoZO0dV8opAMDrY6UBx5jQowL89wXZTXJZHsdXviY1YXWmmpyFZd90CnQdcTl7SU
ihKIfThn7QKBwGr2ueQv9fxsYD1ZQkOyMTMkUSoZWCp4G/J25nFqKfssub3ahouy
tAErKLRl2F1ut2A7ol31b/X0qND/TPtjK118DJvSWblf3FWR4kOlglxaNpDtME1w
cdjq6TSRswgNGuQKG/igtH6rRlVWENLRv2lxf8R/1AdNimuw+Ls2xIZPInNQxNiR
un4ER5sKT3WZq4v+8qaMxNcZ3anzFTB+U/RpLS5dtCWkhnUnimTYAzmNd3TQFHDg
jpf449JR/GJukQKBwFtrh+wtsg6zd3NoQkueo2BJr61FssrN20ZWztLarB2VkTXG
s+BhS+ngxfUhi6fzGpjy9vj915OkGEPXG8C73f3UUSiJBPXgDGEPckfOqib8RN+i
N5Lwg+tY0B1REbqwJc5JWl7aDURVzdjcOt1zqO6mRLyrxFKx3iUAeS2ZtSHrJ91O
nIJLpMjUNK+5BSPgRCI4EfQ8qjSdITTBU4RZ1cBz4SHtdqdkZK/nrle+nPKpswl7
cky2Ff1Ft33wl2VtiQKBwQD49Oih+XnaL8iS6pkmoTKgWN7/tQM1dGKlIgfHDunj
EzBAERBre1x/ynHdZtwo2hvSw2Qvc0ugNEY3tOeFmqr7W6BX63PDkuwasUlXMSrX
lE7oQhftxEElY0LC7r92jO9YSIpGLQK4Ytk8f4lJUT3ajTRtoP6c/m9KCeC3kHKs
1pTQ/5qt2b9EVW1ETipoOyVpST8IJZB/nSLRl6JklR+Y6YPfOTeBPaC6vjwyXGep
j6a80YoNqFBPMFpDUeXRaCo=
-----END PRIVATE KEY-----', true, false);
INSERT INTO client.routeconfig_tbl (routeid, "name", capturetype, mid, username, "password", enabled, isdeleted) VALUES(884, 'MPGS_HKD', 2, 'TEST088008881200', 'merchant.TEST088008881200', '7f8945cebdfd056530baca4e3e8d384a', true, false);
INSERT INTO client.routeconfig_tbl (routeid, "name", capturetype, mid, username, "password", enabled, isdeleted) VALUES(884, 'MPGS_SGD', 2, 'TEST065004485703', 'merchant.TEST065004485703', 'c1e2d0ffb6f52218be89ac0061af1f2a', true, false);
INSERT INTO client.routeconfig_tbl (routeid, "name", capturetype, mid, username, "password", enabled, isdeleted) VALUES(884, 'MPGS_OTHER_COUNTRY', 2, 'TEST048583918508', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', true, false);


INSERT INTO client.routecurrency_tbl (routeconfigid, currencyid, enabled) VALUES(377, NULL, true);
INSERT INTO client.routecurrency_tbl (routeconfigid, currencyid, enabled) VALUES(402, NULL, true);
INSERT INTO client.routecurrency_tbl (routeconfigid, currencyid, enabled) VALUES(403, NULL, true);
INSERT INTO client.routecurrency_tbl (routeconfigid, currencyid, enabled) VALUES(404, NULL, true);
INSERT INTO client.routecurrency_tbl (routeconfigid, currencyid, enabled) VALUES(406, NULL, true);
INSERT INTO client.routecurrency_tbl (routeconfigid, currencyid, enabled) VALUES(407, NULL, true);
INSERT INTO client.routecurrency_tbl (routeconfigid, currencyid, enabled) VALUES(408, NULL, true);
INSERT INTO client.routecurrency_tbl (routeconfigid, currencyid, enabled) VALUES(410, NULL, true);
INSERT INTO client.routecurrency_tbl (routeconfigid, currencyid, enabled) VALUES(411, NULL, true);
INSERT INTO client.routecurrency_tbl (routeconfigid, currencyid, enabled) VALUES(412, NULL, true);
INSERT INTO client.routecurrency_tbl (routeconfigid, currencyid, enabled) VALUES(413, NULL, true);
INSERT INTO client.routecurrency_tbl (routeconfigid, currencyid, enabled) VALUES(414, NULL, true);
INSERT INTO client.routecurrency_tbl (routeconfigid, currencyid, enabled) VALUES(415, NULL, true);
INSERT INTO client.routecurrency_tbl (routeconfigid, currencyid, enabled) VALUES(416, NULL, true);
INSERT INTO client.routecurrency_tbl (routeconfigid, currencyid, enabled) VALUES(417, NULL, true);
INSERT INTO client.routecurrency_tbl (routeconfigid, currencyid, enabled) VALUES(418, NULL, true);
INSERT INTO client.routecurrency_tbl (routeconfigid, currencyid, enabled) VALUES(419, NULL, true);


INSERT INTO client.routecountry_tbl (routeconfigid, countryid, enabled) VALUES(377, NULL, true);
INSERT INTO client.routecountry_tbl (routeconfigid, countryid, enabled) VALUES(402, NULL, true);
INSERT INTO client.routecountry_tbl (routeconfigid, countryid, enabled) VALUES(403, NULL, true);
INSERT INTO client.routecountry_tbl (routeconfigid, countryid, enabled) VALUES(404, NULL, true);
INSERT INTO client.routecountry_tbl (routeconfigid, countryid, enabled) VALUES(406, NULL, true);
INSERT INTO client.routecountry_tbl (routeconfigid, countryid, enabled) VALUES(407, NULL, true);
INSERT INTO client.routecountry_tbl (routeconfigid, countryid, enabled) VALUES(408, NULL, true);
INSERT INTO client.routecountry_tbl (routeconfigid, countryid, enabled) VALUES(410, NULL, true);
INSERT INTO client.routecountry_tbl (routeconfigid, countryid, enabled) VALUES(411, NULL, true);
INSERT INTO client.routecountry_tbl (routeconfigid, countryid, enabled) VALUES(412, NULL, true);
INSERT INTO client.routecountry_tbl (routeconfigid, countryid, enabled) VALUES(413, NULL, true);
INSERT INTO client.routecountry_tbl (routeconfigid, countryid, enabled) VALUES(414, NULL, true);
INSERT INTO client.routecountry_tbl (routeconfigid, countryid, enabled) VALUES(415, NULL, true);
INSERT INTO client.routecountry_tbl (routeconfigid, countryid, enabled) VALUES(416, NULL, true);
INSERT INTO client.routecountry_tbl (routeconfigid, countryid, enabled) VALUES(417, NULL, true);
INSERT INTO client.routecountry_tbl (routeconfigid, countryid, enabled) VALUES(418, NULL, true);
INSERT INTO client.routecountry_tbl (routeconfigid, countryid, enabled) VALUES(419, NULL, true);