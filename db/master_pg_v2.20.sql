/* PASSBOOK IMPROVEMENT - START*/

alter table log.txnpassbook_tbl
	add clientid int;

alter table log.txnpassbook_tbl
	add constraint txnpassbook_tbl_client_tbl_id_fk
		foreign key (clientid) references client.client_tbl;

/* Run migrate script before adding not null constraint */
alter table log.txnpassbook_tbl alter column clientid set not null;

/* PASSBOOK IMPROVEMENT - END */