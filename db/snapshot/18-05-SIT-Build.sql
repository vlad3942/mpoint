-- Citcon WeChat --
--QR Code timeout value in seconds
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('QR_CODE_TIMEOUT', '180', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10007 and pspid = 41), 'merchant');


--Virtual payment page timer value in minutes, this should be less than or equal to the QR code timeout property
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('VIRTUAL_PAYMENT_TIMER', '3', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10007 and pspid = 41), 'merchant');

--------------
