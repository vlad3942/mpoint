ALTER TABLE log.additional_data_tbl ALTER COLUMN value TYPE text;

/* ========== Grab Pay Integration = STARTS ========== */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES ( 67, 'GrabPay',4);
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (608,67,'PHP');
INSERT INTO system.Card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (94, 'GrabPay', 23, -1, -1, -1, 4);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 94, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 608;
INSERT INTO system.cardprefix_tbl (cardid, min, max) VALUES (94, 0, 0);
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (94, 67);

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10077, 67, 'dbb00e18-83ee-49cf-b54d-2707a069b3e4', '0112218e-dda0-4ca8-8489-65a3d28abd69', 'apWSvBQj_evmVfzY');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100770, 67, '-1');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) select 'CLIENT_ID', '14c3e87ce4e04e82954fd78cea2b3a64', id, 'merchant',1 from client.merchantaccount_tbl WHERE clientid=10077 AND pspid=67;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) select 'CLIENT_SECRET', 'dcyDLGEYkeLZA1YM', id, 'merchant',1 from client.merchantaccount_tbl WHERE clientid=10077 AND pspid=67;
INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, stateid, enabled,capture_type,psp_type) VALUES (10077, 94, 67, 640, 1, true,2,4);
/* ========== Grab Pay Integration = STARTS ========== */

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) select 'MAX_DOWNLOAD_FILE_LIMIT', '2', id, 'merchant', from client.merchantaccount_tbl WHERE clientid=10069 AND pspid=52;