
/* ========== batch-size for the chase connector:: CMP-3457 ========== */

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES ('MVAULT_BATCH_SIZE', '1', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = <client id> and pspid =  <pspid>), 'merchant',1);

/*  CMP-3472 */
INSERT INTO CLIENT.ADDITIONALPROPERTY_TBL (KEY, VALUE, EXTERNALID, TYPE, SCOPE)
VALUES ('isnewcardconfig', 'true', <client id>  , 'client', 0);