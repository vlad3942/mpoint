ALTER TABLE log.transaction_tbl ALTER COLUMN attempt SET DEFAULT 0;
/*======= ADD NEW PROCESSOR TYPE FOR TOKENIZATION SYSTEM ======== */
INSERT INTO system.processortype_tbl (id, name) VALUES (8, 'Tokenize');
/*======= END NEW PROCESSOR TYPE FOR TOKENIZATION SYSTEM ======== */

/* =========== Creating a new Client for SouthWest : START =============*/
INSERT INTO client.client_tbl (id, countryid, flowid, name, username, passwd, logourl, cssurl, callbackurl, accepturl, cancelurl, maxamount, lang, smsrcpt, emailrcpt, method, terms, enabled, mode, auto_capture, send_pspid, store_card, iconurl, show_all_cards, max_cards, identification, transaction_ttl, num_masked_digits, salt, declineurl, secretkey, communicationchannels)
VALUES (10025, 200, 1, 'Southwest', 'SWDemo', 'DEMOisNO_2', null, null, null, null, null, 100000000, 'en-us', true, true, 'mPoint', null, true, 0, false, true, 0, null, false, -1, 7, 0, 4, null, null, null, 0);
INSERT INTO client.account_tbl (id, clientid, name, mobile, enabled, markup) VALUES (100250, 10025, 'Southwest App', null, true, 'app');
INSERT INTO client.keyword_tbl (id, clientid, name, standard, enabled) VALUES (52, 10021, 'CPM', true, true);
INSERT INTO client.url_tbl (clientid, urltypeid, url, enabled) VALUES (10021, 4, 'http://localhost:10080', true);
INSERT INTO client.url_tbl (clientid, urltypeid, url, enabled) VALUES (10021, 2, 'http://localhost:10080', true);
/* =========== Creating a new Client for SouthWest : END =============*/


/* ========== CONFIGURE UATP START FOR SOUTHWEST========== */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (50, 'UATP CardAccount',8);
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (15, 50); /*With Apple-Pay*/

INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (208,50,'DKK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (840,50,'USD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (826,50,'GBP');

INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, psp_type) VALUES (10007, 15, 50, 200, 8);/*With Apple-Pay*/
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 50, 'uatp_cellpoint', 'uatp_cellpointcrdacc', 'hUprlx_6');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 50, '-1');

INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (49, 'UATP',2);
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (15, 49); /*With Apple-Pay*/
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, psp_type) VALUES (10007, 15, 49, 200, 8);/*With Apple-Pay*/
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 49, 'uatp_cellpoint', 'uatp_cellpointcrdacc', 'hUprlx_6');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 49, '-1');