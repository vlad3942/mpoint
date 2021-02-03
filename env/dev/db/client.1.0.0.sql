-- mPoint DB Scripts :

--Table Name : Client.Client_Tbl

INSERT INTO client.client_tbl
(id, countryid, flowid, "name", username, passwd, logourl, cssurl, callbackurl, accepturl, cancelurl, maxamount, lang, smsrcpt, emailrcpt, "method", terms, enabled, "mode", send_pspid, store_card, iconurl, show_all_cards, max_cards, identification, transaction_ttl, num_masked_digits, declineurl, salt, secretkey, communicationchannels, installment, max_installments, installment_frequency, enable_cvv)
VALUES(10077, 640, 1, 'CEBU Pacific Air Automation', 'cebuair', 'C3B753w5', 'https://hpp-uat-02.cellpointmobile.net/css/swag/img/cebu.png', 'https://devcpmassets.s3-ap-southeast-1.amazonaws.com', 'https://hpp2.sit-01.cellpoint.dev/views/redirect_response.php', 'https://hpp2.sit-01.cellpoint.dev/views/redirect_response.php', 'https://hpp2.sit-01.cellpoint.dev/views/redirect_response.php', 2147483600, 'gb', false, false, 'mPoint', NULL, true, 1, true, 0, NULL, false, -1, 1, 0, 4, 'https://cebu.dev:8989/booking-confirmation?decline', 'az1sx2dc3fv', NULL, 0, 0, 0, 0, true);


--Table Name : Client.Account_Tbl

INSERT INTO Client.Account_Tbl (id, clientid, name, enabled, markup, businesstype) values (100770, 10077, 'CEBU Pacific Air Web Storefront', true, 'spa', 2);


--Table Name : Client.CardAccess_Tbl

--FirstData

INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_type, walletid, capture_method, dccenabled) VALUES(10077, 5, false, 62, NULL, 1, NULL, false, 1, 0, 2, NULL, 0, true);

--2c2p-alc

INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_type, walletid, capture_method, dccenabled) VALUES(10077, 8, false, 40, NULL, 1, NULL, false, 1, 0, 2, NULL, 0, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_type, walletid, capture_method, dccenabled) VALUES(10077, 7, false, 40, NULL, 1, NULL, false, 1, 0, 2, NULL, 0, true);

--PayPal

INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_type, walletid, capture_method, dccenabled) VALUES(10077, 28, true, 24, NULL, 1, NULL, false, 4, 0, 2, NULL, 0, true);

--CyberSourceAMEX

INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_type, walletid, capture_method, dccenabled) VALUES(10077, 1, false, 63, NULL, 1, NULL, false, 1, 0, 2, NULL, 0, true);

--For WorldPay(Visa/Mastercard)

INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_type, walletid, capture_method, dccenabled) VALUES(10077, 8, false, 4, 640, 1, NULL, false, 1, 0, 3, NULL, 0, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_type, walletid, capture_method, dccenabled) VALUES(10077, 7, false, 4, 640, 1, NULL, false, 1, 0, 3, NULL, 0, true);

--Modirum

INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_type, walletid, capture_method, dccenabled) VALUES(10077, 7, true, 47, NULL, 1, NULL, false, 6, 0, 1, NULL, 0, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_type, walletid, capture_method, dccenabled) VALUES(10077, 8, true, 47, NULL, 1, NULL, false, 6, 0, 1, NULL, 0, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_type, walletid, capture_method, dccenabled) VALUES(10077, 1, true, 47, NULL, 1, NULL, false, 6, 0, 1, NULL, 0, true);

--Pre Fraud - CEBU-RMFSS

--MasterCard

INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_type, walletid, capture_method, dccenabled) VALUES(10077, 7, true, 65, NULL, 1, NULL, false, 9, 0, 1, NULL, 0, true);

--Visa

INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_type, walletid, capture_method, dccenabled) VALUES(10077, 8, true, 65, NULL, 1, NULL, false, 9, 0, 1, NULL, 0, true);

--GrabPay

INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_type, walletid, capture_method, dccenabled) VALUES(10077, 94, true, 67, 640, 1, NULL, false, 4, 0, 2, NULL, 0, true);

--GCash

INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_type, walletid, capture_method, dccenabled) VALUES(10077, 93, true, 40, 640, 1, NULL, false, 1, 0, 1, NULL, 0, true);


--Table Name : Client.Keyword_Tbl

INSERT INTO Client.Keyword_Tbl(clientid, name, standard, enabled) VALUES (10077, 'CEBUauto', true, true);


--Table Name : Client.Url_Tbl

INSERT INTO client.url_tbl(clientid, urltypeid, url, enabled) Values (10077, 2, 'http://mpoint.dev-01.cellpoint.dev/_test/simulators/login.php', true );
INSERT INTO client.url_tbl(clientid, urltypeid, url, enabled) Values (10077, 14, 'https://cpd-hpp2-devassests.s3.eu-central-1.amazonaws.com/10077/', true);
INSERT INTO client.url_tbl(clientid, urltypeid, url, enabled) Values (10077, 4, 'http://5j.mesb.dev.cpm.dev', true );


--Table Name : Client.MerchantAccount_Tbl

--2c2p-alc

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd, enabled, stored_card) VALUES (10077, 40, '2c2p-alc', 'CELLPM', 'HC1XBPV0O4WLKZMG', true, null);

--FirstData

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd, enabled, stored_card) VALUES (10077, 62, '6160800000', 'WS6160800000._.1', 'tester01$', true, null);

--PayPal

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd, enabled, stored_card) VALUES (10077, 24,'ACCu12.jik2Wb3kzHJgFE1palQEsAHD7wsynoTYqRcOZAX7RzupgW4sQ', 'sb-sahh431638744_api1.business.example.com', '7W56K2VQBRYF8FLX', true, null);

--Pre Fruad - CEBU-RMFSS

INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, username, passwd, enabled, stored_card) VALUES (10077, 65, 'CEBU-RMFSS', 'By9AjPV6j14jgb3DXRIpW0mInOfMEafS', 'E9NBawrSH6UAtw1v', true, null);

--WorldPay

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd, enabled, stored_card) VALUES (10077, 4, 'CELLPOINT', 'CELLPOINT', 'Mesb@1234', true, null);

--CyberSourceAMEX

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd, enabled, stored_card) VALUES (10077, 63, 'CyberSourceAMEX', 'cebu_cellpoint_test', 'K/B7APZOVPoPCFvSIyqMpvUmeDCAyyd0aWXnIHFQqBnSBwc1PDXRVZCS8DazLnCSXZUuauffLNY0lxJpoR8/e94VJbzKVK+Dzxmhl3hkS0qnmk/ZJFcd2Huh80UK5qG2TwB2inqPacECAGBLk5steF6UlALDYuMOvJuVinUW84VEpxUJ1Dntmm4AhNpB2pUheytX4XjhoodDerjGZGg61Ps4xHxqNl29huaumNYIoCfGNchX5vkKi8uBoPwJCpbBO0ORUy9sgMQOk1w7DTNVSCvkpbF+LH3VdFV/3N8kU9z/ONKLF2zPq5aWjC861EjQo1mAqiZBjg8Afof3CsDQ0Q==', true, null);

--GrabPay

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd, enabled, stored_card) VALUES (10077, 67, 'dbb00e18-83ee-49cf-b54d-2707a069b3e4', '0112218e-dda0-4ca8-8489-65a3d28abd69', 'apWSvBQj_evmVfzY', true, null);

--mVault

INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, username, passwd, enabled, stored_card) VALUES(10077, 36, 'mVault', 'Blank', 'Blank', true, NULL);
INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, username, passwd, enabled, stored_card) VALUES(10077, 1, 'mVault', 'Blank', 'Blank', true, NULL);

--Modirum

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd, enabled, stored_card) VALUES (10077, 47, 'MODIRUM MPI', '9449005362', '-----BEGIN PRIVATE KEY-----
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
k4ZJ11Zt3Vi+LfrvtgZm0h8=
-----END PRIVATE KEY----', true, null);


--Table Name : Client.MerchantSubAccount_Tbl

--2c2p-alc

INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name, enabled) VALUES (100770, 40, '-1', true);

--FirstData

INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name, enabled) VALUES (100770, 62, '-1', true);

--PayPal

INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name, enabled) VALUES (100770, 24, '-1', true);

--mVault

INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name, enabled) VALUES (100770, 1, '-1', true);
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name, enabled) VALUES (100770, 36, '-1', true);

--WorldPay

INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name, enabled) VALUES (100770, 4, '-1', true);

--CyberSourceAMEX

INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name, enabled) VALUES (100770, 63, '-1', true);

--Pre Faurd - CEBU-RMFSS

INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name, enabled) VALUES (100770, 65, '-1', true);

--Modirum

INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name, enabled) VALUES (100770, 47, '-1', true);

--GrabPay

INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name, enabled) VALUES (100770, 67, '-1', true);


--Table Name : Client.Additionalproperty_Tbl

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES('3DVERIFICATION', 'true', 10077, 'client', true, 2);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES('enableHppAuthentication', 'true', 10077, 'client', true, 0);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES('3DSVERSION', '1.0', 10077, 'client', true, 2);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES('PAYPAL_ORDER_NUMBER_PREFIX', 'Cebu Pacific Air - ', 10077, 'client', true, 2);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES('invoiceidrule', 'invoiceid ::= (psp-config/@id)=="24"OR(psp-config/@id)=="40"=(transaction.@id)', 10077, 'client', true, 0);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES('webSessionTimeout', '5', 10077, 'client', true, 2);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES('isAutoRedirect', 'true', 10077, 'client', true, 2);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES('isnewcardconfig', 'true', 10077, 'client', true, 0);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES('TIMEZONE', 'Asia/Kuala_Lumpur', 10077, 'client', true, 2);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES('HPP_HOST_URL', 'cpm-pay-dev2.cellpointmobile.com', 10077, 'client', true, 2);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES('hppFormRedirectMethod', 'GET', 10077, 'client', true, 2);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES('googleAnalyticsId', '%7B%22id%22%3A%22GTM-TJHF9HX%22%2C%22auth%22%3A%220bNRJejIX9RvP164Mor_Tw%22%2C%22preview%22%3A%22env-61%22%2C%22env%22%3A%22sit%22%7D', 10077, 'client', true, 2);

--2c2p-alc Additional Properties

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.USD', 'CebuPacific_USD', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 40), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.PHP', 'CebuPacific_MCC', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 40), 'merchant', 2);


--FirstData Additional Properties

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('3DVERIFICATION', 'true', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 62), 'merchant', 2);


--PayPal Additional Properties

--PAYPAL_HKD

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('PAYPAL_MID_HKD', 'A78isDTCcuwhNZKyvzOsXXSzxdUPA25dTmhkEQxH7G1T4iQSksAr-SK0', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 24), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('PAYPAL_PASSWORD_HKD', '5QBM4GMSFPV8AHNK', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 24), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('PAYPAL_USERNAME_HKD', 'sb-ph1ko1832308_api1.business.example.com', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 24), 'merchant', 2);

--PAYPAL_SGD

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('PAYPAL_MID_SGD', 'ATG2YLmUZFFk6n5kHVGwCC2A2dsDAEAdhFEakpPDRN5lxmGq3zimTG6A', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 24), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('PAYPAL_PASSWORD_SGD', 'B9WX2HPY9DPD6284', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 24), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('PAYPAL_USERNAME_SGD', 'sb-mohn91867880_api1.business.example.com', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 24), 'merchant', 2);

--PAYPAL_USD

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('PAYPAL_MID_USD', 'Awzp6NMJcMOsM6SlpR13Cez-7vPFAMODxiW5ZT0qx6EbatLFSrMoBKtc', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 24), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('PAYPAL_PASSWORD_USD', '37JT6WGJFFUJFRM3', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 24), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('PAYPAL_USERNAME_USD', 'sb-43kvng1868465_api1.business.example.com', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 24), 'merchant', 2);

--PAYPAL_MYR

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('PAYPAL_PASSWORD_MYR', 'VMXEJAT9DCLCR7LQ', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 24), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('PAYPAL_USERNAME_MYR', 'sb-ivizq1858258_api1.business.example.com', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 24), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('PAYPAL_MID_MYR', 'AJXtiLishvWwk7Jm0EnHXPmvJv1xAqMsrFcJMX8Exsbl9aQ5NJRKPyem', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 24), 'merchant', 2);

--WorldPay Additional Properties

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('3DVERIFICATION', 'mpi', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 4), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('FareBasisCode', 'BK', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 4), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('IssuerAddress1', 'CEBU PACIFIC BUILDING, DOMESTIC ROAD, BARANGAY 191, ZONE 20, PASAY CITY 1301 PHILIPPINES', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 4), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('IssuerCity', 'PASAY CITY', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 4), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('IssuerCountryCode', 'PH', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 4), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('IssuerPostalCode', '1301', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 4), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('RestrictedTicket', '1', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 4), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('TravelAgencyCode', '5J', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 4), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('TravelAgencyName', 'CebuPacificair', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 4), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mpi_rule', 'isSkippAuth::=<status>!=="1"AND<status>!=="2"AND<status>!=="4"AND<status>!=="5"AND<status>!=="6"

status::=(card.info-3d-secure.additional-data.param[@name=''status''])', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 4), 'merchant', 0);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('post_fraud_rule', 'isPostFraudAttemp::=<status>=="1"OR<status>=="4"

status::=(card.info-3d-secure.additional-data.param[@name=''status''])', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 4), 'merchant', 0);

--2c2p-alc Additional Properties

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('post_fraud_rule', 'isPostFraudAttemp::=<pspid>=="40"
pspid::=(psp-config.@id)', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 40), 'merchant', 0);

--FirstData Additional Properties

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('post_fraud_rule', 'isPostFraudAttemp::=<status>=="1"OR<status>=="2"OR<status>=="4"
status::=(card.info-3d-secure.additional-data.param[@name=''status''])', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 62), 'merchant', 0);

-- CYBS Additional Properties

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('3DVERIFICATION', 'mpi', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 63), 'merchant', 2);


--Additional properties to control billing address visibility and mandatory feature.

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES ('showBillingDetails', true, 10077, 'client', true, 2);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES ('mandateBillingDetails', true, 10077, 'client', true, 2);


--CRS Additional Properties

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES('FOP_SELECTION', 'true', 10077, 'client', true, 0);  --FOP Selection
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES('DR_SERVICE', 'true', 10077, 'client', true, 0);  --Dynamic Routing


--Fraud Integration Additional Properties

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES('getTxnStatusPollingTimeOut', '60', 10077, 'client', true, 2);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES('ISROLLBACK_ON_FRAUD_FAIL', 'true', 10077, 'client', true, 0);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES('IS_STORE_BILLING_ADDRS', 'true', 10077, 'client', true, 0);

--GrabPay

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('CLIENT_ID', '14c3e87ce4e04e82954fd78cea2b3a64', true, (SELECT ID FROM client.merchantaccount_tbl WHERE pspid = 67 and clientid = 10077), 'merchant', 1);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('CLIENT_SECRET', 'dcyDLGEYkeLZA1YM', true, (SELECT ID FROM client.merchantaccount_tbl WHERE pspid = 67 and clientid = 10077), 'merchant', 1);


--This will support all currency to all country

INSERT INTO client.countrycurrency_tbl (clientid, countryid, currencyid) VALUES(10077, 0, 1);

--CEBU Payment Center
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_type, walletid, capture_method, dccenabled) VALUES(10077, 96, true, 69, NULL, 1, NULL, false, 4, 0, 2, NULL, 0, false);
INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, username, passwd, enabled, stored_card) VALUES (10077, 69, 'CEBU Payment Center', '', '', true, null);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (100770, 69, '-1', true);

--CEBU CIAM additional property 
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES( 'SSO_PREFERENCE', 'LOOSE',true,  10077,'client',2)

-- Cebu Icer : Set dcc enabled to true for PayPal
UPDATE client.cardaccess_tbl set dccenabled = '1' where clientid = 10077 and cardid = 28 ;

-- Cebu Icer : Add sale currency and presentment currency configuration for PayPal
INSERT into client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (28,10077,156,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (28,10077,360,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (28,10077,901,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (28,10077,36,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (28,10077,96,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (28,10077,446,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (28,10077,784,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (28,10077,410,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (28,10077,764,608,'true','true');


-- Cebu Icer : Set dcc enabled to true for Gcash
UPDATE client.cardaccess_tbl set dccenabled = '1' where clientid = 10077 and cardid = 93 ;

-- Cebu Icer : Add sale currency and presentment currency configuration for Gcash

INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (93,10077,156,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (93,10077,360,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (93,10077,901,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (93,10077,36,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (93,10077,96,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (93,10077,446,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (93,10077,784,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (93,10077,410,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (93,10077,764,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (93,10077,344,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (93,10077,458,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (93,10077,702,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (93,10077,392,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (93,10077,840,608,'true','true');



-- Cebu Icer : Set dcc enabled to true for GrabPay
UPDATE client.cardaccess_tbl set dccenabled = '1' where clientid = 10077 and cardid = 94 ;
-- Cebu Icer : Add sale currency and presentment currency configuration for GrabPay

INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (94,10077,156,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (94,10077,360,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (94,10077,901,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (94,10077,36,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (94,10077,96,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (94,10077,446,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (94,10077,784,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (94,10077,410,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (94,10077,764,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (94,10077,344,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (94,10077,458,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (94,10077,702,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (94,10077,392,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (94,10077,840,608,'true','true');



-- Cebu Icer : Set dcc enabled to true for paymaya
UPDATE client.cardaccess_tbl set dccenabled = '1' where clientid = 10077 and cardid = 95 ;

-- Cebu Icer : Add sale currency and presentment currency configuration for paymaya

INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (95,10077,156,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (95,10077,360,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (95,10077,901,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (95,10077,36,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (95,10077,96,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (95,10077,446,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (95,10077,784,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (95,10077,410,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (95,10077,764,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (95,10077,344,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (95,10077,458,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (95,10077,702,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (95,10077,392,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (95,10077,840,608,'true','true');


-- Cebu Icer : Set dcc enabled to true for payment center
UPDATE client.cardaccess_tbl set dccenabled = '1' where clientid = 10077 and cardid = 96 ;

-- Cebu Icer : Add sale currency and presentment currency configuration for payment center
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,36,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,36,784,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,96,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,96,784,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,156,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,156,784,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,410,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,410,784,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,446,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,446,784,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,764,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,764,784,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,901,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,901,784,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,344,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,344,784,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,458,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,458,784,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,702,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,702,784,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,392,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,392,784,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,840,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,840,784,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,784,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,608,784,'true','true');
