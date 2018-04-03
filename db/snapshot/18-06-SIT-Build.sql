--Citcon Wechat change for user experience 
--Redirect URL for HPP integration - can be blank or accept url or as required for client configuration
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('WECHAT_CALLBACK_URL', '', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10007 and pspid = 41), 'merchant');
