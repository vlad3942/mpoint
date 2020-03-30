
/* ========== batch-size for the chase connector:: CMP-3457 ========== */

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES ('MVAULT_BATCH_SIZE', '1', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = <client id> and pspid =  <pspid>), 'merchant',1);

/* ========== Below sql statements has to be updated to all the Apple Pay Live clients ========== */
/* ========== While preparing prod release not please consider updating below queries ========== */

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES ('APPLEPAY_MERCHANT_DOMAIN', <domainName>, 10069, 'client', true, 2);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES ('APPLEPAY_JS_URL', 'https://s3.ap-southeast-1.amazonaws.com/cpmassets/psp/applepay.js', 10069, 'client', true, 2);

/* ==================== */

/* ========== HPP Additional Property for Card Option Display : CPMEH-728 ========== */
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES ('hideCardPaymentOption', 'true', <clientid>, 'client', true, 2);


/*  CMP-3472 */
INSERT INTO CLIENT.ADDITIONALPROPERTY_TBL (KEY, VALUE, EXTERNALID, TYPE, SCOPE)
VALUES ('isnewcardconfig', 'true', <client id>  , 'client', 0);
