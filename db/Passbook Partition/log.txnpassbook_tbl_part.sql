/*----------------------------------------------------------------------------------------------
		Table : log.txnpassbook_tbl_part
		Version	  : v1.1
		Date		  : 2020-03-03
		Purpose 	  : Creates a Basic intermediatory Partition Table for Transaction Passbook
		Author	      : CPM (SWE/Sarvesh)
------------------------------------------------------------------------------------------------*/
-- DROP TABLE log.txnpassbook_tbl_part
CREATE TABLE log.txnpassbook_tbl_part 
(
	id serial NOT NULL,
	clientid int4 NOT NULL,  --new column
	transactionid int4 NOT NULL,
	amount int4 NOT NULL,
	currencyid int4 NOT NULL,
	requestedopt int4 NULL,
	performedopt int4 NULL,
	status varchar(20) NOT NULL,
	extref varchar(1000) NULL,
	extrefidentifier varchar(100) NULL,
	enabled bool NULL DEFAULT true,
	created timestamp NULL DEFAULT now(),
	modified timestamp NULL DEFAULT now()
)
PARTITION BY LIST(clientid);

ALTER TABLE log.TXNPASSBOOK_TBL_PART OWNER TO mpoint;
