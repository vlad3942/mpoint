-- Citcon WeChat --
--QR Code timeout value in seconds
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('QR_CODE_TIMEOUT', '180', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10007 and pspid = 41), 'merchant');


--Virtual payment page timer value in mm:ss, this should be less than or equal to the QR code timeout property
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('VIRTUAL_PAYMENT_TIMER', '02:00', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10007 and pspid = 41), 'merchant');


--url to link wechat icon
INSERT INTO client.url_tbl(urltypeid, clientid, url)
VALUES (14, 10007, "https://s3-ap-southeast-1.amazonaws.com/cpmassets/payment/icons");
--------------
