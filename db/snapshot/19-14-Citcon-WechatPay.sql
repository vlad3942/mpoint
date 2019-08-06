/* ========== Global Configuration for Citcon - WeChat Pay - Payment Method : START========== */
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (39, 'WeChat Pay', 23, -1, -1, -1,6);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (39, 0, 0);
INSERT INTO system.cardpricing_tbl ( pricepointid, cardid) VALUES ( -840, 39);
INSERT INTO system.cardpricing_tbl ( pricepointid, cardid) VALUES ( -156, 39);


INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (41, 'Citcon',5);
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (840,41,'USD');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (156,56,'CNY');

INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (39, 41);
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name) VALUES (<clientid>, 41, 'Citcon');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<accountid>, 41, <MERCHANTTOKEN>); -- For Android and iOS merchant accounts.
INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position,psp_type) VALUES (<clientid>, 39, true, 41, <CountryId>, 1, null,5);

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('MERCHANT_VENDOR', 'wechatpay', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = <clientid> and pspid = 41), 'merchant');

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('ALLOW_DUPLICATES', 'no', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = <clientid> and pspid = 41), 'merchant');

--QR Code timeout value in seconds
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('QR_CODE_TIMEOUT', '180', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = <clientid> and pspid = 41), 'merchant');

--Virtual payment page timer value in mm:ss, this should be less than or equal to the QR code timeout property
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('VIRTUAL_PAYMENT_TIMER', '02:00', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = <clientid> and pspid = 41), 'merchant');

--url to link wechat icon
INSERT INTO client.url_tbl(urltypeid, clientid, url)
VALUES (14, <clientid>, "https://s3-ap-southeast-1.amazonaws.com/cpmassets/payment/icons");

--Redirect URL for HPP integration - can be blank or accept url or as required for client configuration
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('WECHAT_CALLBACK_URL', '', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = <clientid> and pspid = 41), 'merchant');


/*=========================End===================================== */
