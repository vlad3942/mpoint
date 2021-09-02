--Create Client
INSERT INTO client.client_tbl
(id, countryid, flowid, "name", username, passwd, logourl, cssurl, callbackurl, accepturl, cancelurl, maxamount, lang, smsrcpt, emailrcpt, "method", terms, enabled, "mode", send_pspid, store_card, iconurl, show_all_cards, max_cards, identification, transaction_ttl, num_masked_digits, declineurl, salt, secretkey, communicationchannels, installment, max_installments, installment_frequency, enable_cvv)
VALUES(10101, 405, 1, 'Avianca', 'avianca', 'zf4Bc6$TyF', 'https://cpd-hpp2-prodassests.s3.eu-central-1.amazonaws.com/10101/logo.png', 'https://cpd-hpp2-prodassests.s3.eu-central-1.amazonaws.com/10101', 'https://av.mretail.prod-02.cellpoint.cloud/mOrder/sys/mpoint.php', '', '', 999999999, 'gb', false, false, 'mPoint', NULL, true, 1, true, 0, NULL, false, -1, 7, 0, 4, '', 'c1mig48m9n7', NULL, 0, 0, 0, 0, true);

--Create Storefronts
INSERT INTO client.account_tbl
(id, clientid, "name", mobile, enabled, markup, businesstype)
VALUES(101011, 10101, 'Avianca Colombia Web', NULL, true, 'spa', 0);

INSERT INTO client.account_tbl
(id, clientid, "name", mobile, enabled, markup, businesstype)
VALUES(101012, 10101, 'Avianca Brazil Web', NULL, true, 'spa', 0);

INSERT INTO client.account_tbl
(id, clientid, "name", mobile, enabled, markup, businesstype)
VALUES(101013, 10101, 'Avianca Brazil App', NULL, true, 'spa', 0);

INSERT INTO client.account_tbl
(id, clientid, "name", mobile, enabled, markup, businesstype)
VALUES(101014, 10101, 'Avianca Colombia App', NULL, true, 'spa', 0);

--Create Keyword
INSERT INTO client.keyword_tbl
(clientid, "name", standard, enabled)
VALUES(10101, 'CPM', true, true);

--Country & Currency
INSERT INTO client.countrycurrency_tbl (clientid, countryid, currencyid, enabled) VALUES(10101, 405, 170, true); -- Colombia-COL
INSERT INTO client.countrycurrency_tbl (clientid, countryid, currencyid, enabled) VALUES(10101, 403, 986, true); -- Brazil-BRL
--INSERT INTO client.countrycurrency_tbl (clientid, countryid, currencyid, enabled) VALUES(10101, 405, 840, true); -- Colombia-USD
--INSERT INTO client.countrycurrency_tbl (clientid, countryid, currencyid, enabled) VALUES(10101, 403, 840, true); -- Brazil-USD

--URL
INSERT INTO client.url_tbl
(urltypeid, clientid, url, enabled)
VALUES(14, 10101, 'https://cpd-hpp2-prodassests.s3.eu-central-1.amazonaws.com/10101', true);

INSERT INTO client.url_tbl (urltypeid, clientid, url, enabled)
VALUES(4, 10101, 'https://av.velocity.cellpointmobile.net:443', true);

--Merchant Account
INSERT INTO client.merchantaccount_tbl --CYBS Fraud
(clientid, pspid, "name", enabled, username, passwd, stored_card, supportedpartialoperations)
VALUES(10101, 64, 'CYBS Fraud', true, 'avianca_master', '', NULL, 0);

/*INSERT INTO client.merchantaccount_tbl --uatp_cellpoint
(clientid, pspid, "name", enabled, username, passwd, stored_card, supportedpartialoperations)
VALUES(10101, 50, '', true, '', '', NULL, 0); */

--Avianca Colombia Web
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(101011, 21, '-1', true);--Ingenico
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(101011, 4, '-1', true); --Worldpay
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(101011, 70, '-1', true);--SafetyPay
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(101011, 50, '-1', true);--UATP CardAccount
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(101011, 63, '-1', true);--CyberSource
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(101011, 64, '-1', true);--CyberSource Fraud Gateway

--Avianca Brazil Web
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(101012, 21, '-1', true);--Ingenico
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(101012, 4, '-1', true); --Worldpay
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(101012, 70, '-1', true);--SafetyPay
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(101012, 50, '-1', true);--UATP CardAccount
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(101012, 63, '-1', true);--CyberSource
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(101012, 64, '-1', true);--CyberSource Fraud Gateway

--Avianca Brazil App
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(101013, 21, '-1', true);--Ingenico
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(101013, 4, '-1', true); --Worldpay
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(101013, 70, '-1', true);--SafetyPay
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(101013, 50, '-1', true);--UATP CardAccount
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(101013, 63, '-1', true);--CyberSource
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(101013, 64, '-1', true);--CyberSource Fraud Gateway

--Avianca Colombia App
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(101014, 21, '-1', true);--Ingenico
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(101014, 4, '-1', true); --Worldpay
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(101014, 70, '-1', true);--SafetyPay
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(101014, 50, '-1', true);--UATP CardAccount
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(101014, 63, '-1', true);--CyberSource
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(101014, 64, '-1', true);--CyberSource Fraud Gateway

--Create Static Routes
--CyberSource Fraud Gateway mapping with Cards types
INSERT INTO client.cardaccess_tbl
(clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled)
VALUES(10101, 1, true, 64, NULL, 1, NULL, false, 9, 0, 0, 1, NULL, false); --American Express

INSERT INTO client.cardaccess_tbl
(clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled)
VALUES(10101, 7, true, 64, NULL, 1, NULL, false, 9, 0, 0, 1, NULL, false); --Master Card

INSERT INTO client.cardaccess_tbl
(clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled)
VALUES(10101, 8, true, 64, NULL, 1, NULL, false, 9, 0, 0, 1, NULL, false); --VISA

INSERT INTO client.cardaccess_tbl
(clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled)
VALUES(10101, 21, true, 64, NULL, 1, NULL, false, 9, 0, 0, 1, NULL, false); --UATP

INSERT INTO client.cardaccess_tbl
(clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled)
VALUES(10101, 3, true, 64, NULL, 1, NULL, false, 9, 0, 0, 1, NULL, false); --Diners Club

--Create Routes
--Ingenico
INSERT INTO client.route_tbl
(clientid, providerid, enabled)
VALUES(10101, 21, true); 

--Worldpay
INSERT INTO client.route_tbl
(clientid, providerid, enabled)
VALUES(10101, 4, true); 

--Safety Pay
INSERT into client.route_tbl (clientid, providerid, enabled)
VALUES(10101, 70,true);

--Cybersource 
INSERT into client.route_tbl (clientid, providerid, enabled) 
values(10101, 63,true);

--surepay_tbl
INSERT INTO client.surepay_tbl
(clientid, resend, "notify", email, enabled, max)
VALUES(10101, 60, NULL, NULL, true, 5);

--Client Additional Properties
---Additional properties for external id=Clientid
INSERT INTO client.additionalproperty_tbl
("key", value, modified, created, enabled, externalid, "type", "scope")
VALUES('HPP_HOST_URL', 'pop.cellpointdigital.net', now(), now(), true, 10101, 'client', 2);

INSERT INTO client.additionalproperty_tbl
("key", value, modified, created, enabled, externalid, "type", "scope")
VALUES('googleAnalyticsId', 'UA-2170765-12', now(), now(), true, 10101, 'client', 2);

INSERT INTO client.additionalproperty_tbl
("key", value, modified, created, enabled, externalid, "type", "scope")
VALUES('webSessionTimeout', '45', now(), now(), true, 10101, 'client', 2);

INSERT INTO client.additionalproperty_tbl
("key", value, modified, created, enabled, externalid, "type", "scope")
VALUES('enableHppAuthentication', 'true', now(), now(), true, 10101, 'client', 0);

INSERT INTO client.additionalproperty_tbl
("key", value, modified, created, enabled, externalid, "type", "scope")
VALUES('IS_LEGACY', 'false', now(), now(), true, 10101, 'client', 2);

INSERT INTO client.additionalproperty_tbl
("key", value, modified, created, enabled, externalid, "type", "scope")
VALUES('isAutoRedirect', 'true', now(), now(), true, 10101, 'client', 2);

INSERT INTO client.additionalproperty_tbl
("key", value, modified, created, enabled, externalid, "type", "scope")
VALUES('showBillingDetails', 'true', now(),now(), true, 10101, 'client', 2);

INSERT INTO client.additionalproperty_tbl
("key", value, modified, created, enabled, externalid, "type", "scope")
VALUES('mandateBillingDetails', 'true', now(), now(), true, 10101, 'client', 2);

INSERT INTO client.additionalproperty_tbl
("key", value, modified, created, enabled, externalid, "type", "scope")
VALUES('AIRLINE_NUMRIC_CODE', '134', now(), now(), true, 10101, 'client', 2);

INSERT INTO client.additionalproperty_tbl
("key", value, modified, created, enabled, externalid, "type", "scope")
VALUES('CARRIER_NAME', 'Avianca', now(), now(), true, 10101, 'client', 2);

INSERT INTO client.additionalproperty_tbl
("key", value, modified, created, enabled, externalid, "type", "scope")
VALUES('IS_LEGACY_CALLBACK_FLOW', 'true', now(), now(), true, 10101, 'client', 2);

INSERT INTO client.additionalproperty_tbl
("key", value, modified, created, enabled, externalid, "type", "scope")
VALUES('IS_STORE_BILLING_ADDRS', 'true', now(), now(), true, 10101, 'client', 0);

INSERT INTO client.additionalproperty_tbl
("key", value, modified, created, enabled, externalid, "type", "scope")
VALUES('DFP_GEN', 'true', now(), now(), true, 10101, 'client', 2);

/*INSERT INTO client.additionalproperty_tbl
("key", value, modified, created, enabled, externalid, "type", "scope")
VALUES('CYBS_DM_ORGID', '1snn5n9w', now(), now(), true, 10101, 'client', 2);*/

INSERT INTO client.additionalproperty_tbl
("key", value, modified, created, enabled, externalid, "type", "scope")
VALUES('CYBS_DM_MID', 'avianca_master', now(), now(), true, 10101, 'client', 2);

INSERT INTO client.additionalproperty_tbl
("key", value, modified, created, enabled, externalid, "type", "scope")
VALUES('ADOBE_TARGET_SCRIPT', 'true', now(), now(), true, 10101, 'client', 2);

/*INSERT INTO client.additionalproperty_tbl
("key", value, modified, created, enabled, externalid, "type", "scope")
VALUES('ADOBE_TARGET_SCRIPT_PATH', '6ac3e976c146/92ff2d2716e2/launch-9a08edba7641-staging.min.js', now(), now(), true, 10101, 'client', 2); */

INSERT INTO client.additionalproperty_tbl
("key", value, modified, created, enabled, externalid, "type", "scope")
VALUES('getTxnStatusPollingTimeOut', '2700', now(), now(), true, 10101, 'client', 2);

INSERT INTO client.additionalproperty_tbl
("key", value, modified, created, enabled, externalid, "type", "scope")
VALUES('minPollingInterval', '5', now(), now(), true, 10101, 'client', 2);

INSERT INTO client.additionalproperty_tbl
("key", value, modified, created, enabled, externalid, "type", "scope")
VALUES('maxPollingInterval', '30', now(), now(), true, 10101, 'client', 2);


INSERT INTO client.product_tbl (clientid, producttypeid, code, description)
values  (10101, 200, 'ASST', 'TRAVEL ASSISTANCE'),
        (10101, 200, 'AVIH', 'PET IN HOLD'),
        (10101, 200, 'BBAG', '2ND CHECKED BAG 23KG'),
        (10101, 200, 'BLND', 'BLIND PASSENGER INFORMATION'),
        (10101, 200, 'BSCT', 'BASSINET / BABY COT REQUEST'),
        (10101, 200, 'CBAG', '3RD CHECKED BAG OR MORE 23KG'),
        (10101, 200, 'DEAF', 'DEAF PASSENGER INFORMATION'),
        (10101, 200, 'DEPA', 'ACCOMPANIED DEPORTEE INFORMATION'),
        (10101, 200, 'DEPU', 'DEPORTEE - ACCOMPANIED BY AN ESCORT'),
        (10101, 200, 'DOCA', 'PASSENGER CREW ADDRESS INFORMATION'),
        (10101, 200, 'DOCO', 'PASSENGER/CREW OTHER TRAVEL RELATED INFO'),
        (10101, 200, 'DOCS', 'PASSENGER/CREW PRIMARY TRAVEL DOCUMENT INFO'),
        (10101, 200, 'DPNA', 'DISABLED PASSENGER NEEDING ASSISTANCE'),
        (10101, 200, 'ESAN', 'PASSENGER WITH EMOTIONAL SUPPORT/PSYCHIATRIC ASSIS...'),
        (10101, 200, 'FOID', 'FORM OF IDENTIFICATION FOR ETICKET'),
        (10101, 200, 'HBAG', 'PREPAID OVERWEIGHT'),
        (10101, 200, 'MEDA', 'MEDICAL ASSISTANCE INFORMATION'),
        (10101, 200, 'MEQT', 'MEDICAL EQUIPMENT'),
        (10101, 200, 'PETC', 'PET IN CABIN'),
        (10101, 200, 'SPEQ', 'GOLF EQUIPMENT'),
        (10101, 200, 'SPEQ', 'SKI EQUIPMENT'),
        (10101, 200, 'SPEQ', 'BICYCLE'),
        (10101, 200, 'SPEQ', 'SCUBA EQUIPMENT'),
        (10101, 200, 'SPEQ', 'SURFBOARD UPTO70LB 32KG'),
        (10101, 200, 'SPEQ', 'WINDSURF EQUIP UPTO70LB 32KG'),
        (10101, 200, 'SPEQ', 'KITE SURFBOARD UP TO 22LB 10KG'),
        (10101, 200, 'SVAN', 'PASSENGER WITH SERVICE ANIMAL IN CABIN'),
        (10101, 200, 'TIME', 'TIME TO THINK'),
        (10101, 200, 'UMNR', 'UNACCOMPANIED MINOR'),
        (10101, 200, 'WCBD', 'WHEELCHAIR DRY-CELL BATTERY REQUEST'),
        (10101, 200, 'WCBW', 'WHEELCHAIR WET-CELL BATTERY REQUEST'),
        (10101, 200, 'WCHC', 'WHEELCHAIR TO SEAT REQUEST'),
        (10101, 200, 'WCHR', 'WHEELCHAIR TO AIRCRAFT DOOR REQUEST'),
        (10101, 200, 'WCHS', 'WHEELCHAIR UP/DOWN STAIRS REQUEST'),
        (10101, 200, 'WCLB', 'WHEELCHAIR LITHIUM ION BATTERY REQUEST'),
        (10101, 200, 'WCMP', 'WHEELCHAIR MANUAL POWERED REQUEST'),
        (10101, 200, 'WCOB', 'WHEELCHAIR ON BOARD REQUEST');