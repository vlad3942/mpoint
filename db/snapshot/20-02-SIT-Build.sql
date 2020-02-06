/* PASSBOOK IMPROVEMENT - START*/

alter table log.txnpassbook_tbl
	add clientid int;

alter table log.txnpassbook_tbl
	add constraint txnpassbook_tbl_client_tbl_id_fk
		foreign key (clientid) references client.client_tbl;

/* If required add range check to avoid high peak in RDS CPU */
UPDATE LOG.TXNPASSBOOK_TBL PASSBOOK
SET CLIENTID = TRANSACTION.CLIENTID
FROM LOG.TRANSACTION_TBL TRANSACTION
WHERE PASSBOOK.TRANSACTIONID = TRANSACTION.ID;

/* Run migrate script before adding not null constraint */
alter table log.txnpassbook_tbl alter column clientid set not null;

/* PASSBOOK IMPROVEMENT - END */