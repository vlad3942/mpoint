alter table log.transaction_tbl
	add issuing_bank varchar(100);

ALTER TABLE client.client_tbl ADD COLUMN enable_cvv boolean DEFAULT true;