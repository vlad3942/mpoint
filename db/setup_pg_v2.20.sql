/* PASSBOOK IMPROVEMENT - START*/

/* If required add range check to avoid high peak in RDS CPU */
UPDATE LOG.TXNPASSBOOK_TBL PASSBOOK
SET CLIENTID = TRANSACTION.CLIENTID
FROM LOG.TRANSACTION_TBL TRANSACTION
WHERE PASSBOOK.TRANSACTIONID = TRANSACTION.ID;

/* PASSBOOK IMPROVEMENT - END */
/* ========== batch-size for the chase connector:: CMP-3457 ========== */


INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES ('MVAULT_BATCH_SIZE', '1', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = <client id> and pspid =  <pspid>), 'merchant',1);
