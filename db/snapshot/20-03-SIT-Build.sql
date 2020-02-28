
/* ========== batch-size for the chase connector:: CMP-3457 ========== */

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES ('MVAULT_BATCH_SIZE', '1', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = <client id> and pspid =  <pspid>), 'merchant',1);


INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES ('APPLEPAY_MERCHANT_DOMAIN', 'cpm-pay-dev2.cellpointmobile.com', <clientid>, 'client', true, 2);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES ('APPLEPAY_JS_URL', 'https://s3.ap-southeast-1.amazonaws.com/cpmassets/psp/applepay.js', <clientid>, 'client', true, 2);
