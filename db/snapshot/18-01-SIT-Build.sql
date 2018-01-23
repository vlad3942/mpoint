/* ========== Global Configuration for Citcon - WeChat Pay - Payment Method : START========== */
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (39, 'WeChat Pay', 23, -1, -1, -1,4);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (39, 0, 0);
INSERT INTO system.cardpricing_tbl ( pricepointid, cardid) VALUES ( -840, 39);

INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (41, 'Citcon',1);
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (840,41,'USD');
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (39, 41);
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 41, 'Citcon', '', '');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 41, '-1');
INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position) VALUES (10007, 39, true, 41, 200, 1, null);

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('MERCHANT_API_TOKEN', '71D149972DDC436694922B912104C5A5', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10007 and pspid = 41), 'merchant');

/*=========================End===================================== */

