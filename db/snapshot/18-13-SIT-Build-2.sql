ALTER TABLE log.transaction_tbl ALTER COLUMN attempt SET DEFAULT 0;
/*======= ADD NEW PROCESSOR TYPE FOR TOKENIZATION SYSTEM ======== */
INSERT INTO system.processortype_tbl (id, name) VALUES (8, 'Tokenize');
/*======= END NEW PROCESSOR TYPE FOR TOKENIZATION SYSTEM ======== */

/* =========== Creating a new Client for SouthWest : START =============*/
INSERT INTO client.client_tbl (id, countryid, flowid, name, username, passwd, logourl, cssurl, callbackurl, accepturl, cancelurl, maxamount, lang, smsrcpt, emailrcpt, method, terms, enabled, mode, auto_capture, send_pspid, store_card, iconurl, show_all_cards, max_cards, identification, transaction_ttl, num_masked_digits, salt, declineurl, secretkey, communicationchannels)
VALUES (10068, 200, 1, 'Southwest', 'SWDemo', 'DEMOisNO_2', null, null, null, null, null, 100000000, 'gb', true, true, 'mPoint', null, true, 0, false, true, 0, null, false, -1, 7, 0, 4, null, null, null, 0);
INSERT INTO client.account_tbl (id, clientid, name, mobile, enabled, markup) VALUES (100680, 10068, 'Southwest App', null, true, 'app');
INSERT INTO client.keyword_tbl (clientid, name, standard, enabled) VALUES (10068, 'SWT', true, true);
INSERT INTO client.url_tbl (clientid, urltypeid, url, enabled) VALUES (10068, 4, 'http://localhost:10080', true);
INSERT INTO client.url_tbl (clientid, urltypeid, url, enabled) VALUES (10068, 2, 'http://localhost:10080', true);
/* =========== Creating a new Client for SouthWest : END =============*/


/* ========== CONFIGURE UATP START FOR SOUTHWEST========== */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (50, 'UATP CardAccount',8);
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (15, 50); /*With Apple-Pay*/

INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (208,50,'DKK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (840,50,'USD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (826,50,'GBP');

INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, psp_type) VALUES (10068, 15, 50, 200, 8);/*With Apple-Pay*/
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid) VALUES (10068, 15, 18, 200);/*With Apple-Pay*/
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid) VALUES (10068, 7, 18, 200);
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid) VALUES (10068, 8, 18, 200);
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10068, 50, 'uatp_cellpoint', 'uatp_cellpointcrdacc', 'hUprlx_6');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100680, 50, '-1');
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10068, 18, '9105bb4f-ae68-4768-9c3b-3eda968f57ea', '70000-APILUHN-CARD', '8mhwavKVb91T');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100680, 18, '-1');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'check.enrollment.mid', '33f6d473-3036-4ca5-acb5-8c64dac862d1',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10068 and pspid = 18),'merchant');
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name) VALUES (10001, 14, 'merchant.cpm.apple.pay');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 14, '-1');