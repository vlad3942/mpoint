/*----------------------------------------------------------------------------------------------
		Table : log.txnpassbook_tbl_part
		Version	  : v1.0
		Date		  : 2020-03-01
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

--create partition at defination time
CREATE TABLE log.txnpassbook_tbl_10018 PARTITION OF log.txnpassbook_tbl_part FOR VALUES IN (10018) 
PARTITION BY RANGE  (transactionid);
CREATE TABLE log.txnpassbook_tbl_10020 PARTITION OF log.txnpassbook_tbl_part FOR VALUES IN (10020) 
PARTITION BY RANGE  (transactionid);
CREATE TABLE log.txnpassbook_tbl_10021 PARTITION OF log.txnpassbook_tbl_part FOR VALUES IN (10021) 
PARTITION BY RANGE  (transactionid);
CREATE TABLE log.txnpassbook_tbl_10069 PARTITION OF log.txnpassbook_tbl_part FOR VALUES IN (10069) 
PARTITION BY RANGE  (transactionid);
CREATE TABLE log.txnpassbook_tbl_10022 PARTITION OF log.txnpassbook_tbl_part FOR VALUES IN (10022); 
CREATE TABLE log.txnpassbook_tbl_10044 PARTITION OF log.txnpassbook_tbl_part FOR VALUES IN (10044); 
CREATE TABLE log.txnpassbook_tbl_10047 PARTITION OF log.txnpassbook_tbl_part FOR VALUES IN (10047); 
CREATE TABLE log.txnpassbook_tbl_10048 PARTITION OF log.txnpassbook_tbl_part FOR VALUES IN (10048); 
CREATE TABLE log.txnpassbook_tbl_10055 PARTITION OF log.txnpassbook_tbl_part FOR VALUES IN (10055); 
CREATE TABLE log.txnpassbook_tbl_10060 PARTITION OF log.txnpassbook_tbl_part FOR VALUES IN (10060);
CREATE TABLE log.txnpassbook_tbl_10061 PARTITION OF log.txnpassbook_tbl_part FOR VALUES IN (10061);
CREATE TABLE log.txnpassbook_tbl_10062 PARTITION OF log.txnpassbook_tbl_part FOR VALUES IN (10062); 
CREATE TABLE log.txnpassbook_tbl_10065 PARTITION OF log.txnpassbook_tbl_part FOR VALUES IN (10065); 
CREATE TABLE log.txnpassbook_tbl_10067 PARTITION OF log.txnpassbook_tbl_part FOR VALUES IN (10067); 

CREATE TABLE log.txnpassbook_tbl_10066 PARTITION OF log.txnpassbook_tbl_part FOR VALUES IN (10066); 
CREATE TABLE log.txnpassbook_tbl_10070 PARTITION OF log.txnpassbook_tbl_part FOR VALUES IN (10070);
CREATE TABLE log.txnpassbook_tbl_10071 PARTITION OF log.txnpassbook_tbl_part FOR VALUES IN (10071);
CREATE TABLE log.txnpassbook_tbl_10075 PARTITION OF log.txnpassbook_tbl_part FOR VALUES IN (10075);
CREATE TABLE log.txnpassbook_tbl_10077 PARTITION OF log.txnpassbook_tbl_part FOR VALUES IN (10077);
CREATE TABLE log.txnpassbook_tbl_10080 PARTITION OF log.txnpassbook_tbl_part FOR VALUES IN (10080);
CREATE TABLE log.txnpassbook_tbl_10098 PARTITION OF log.txnpassbook_tbl_part FOR VALUES IN (10098);

CREATE TABLE log.txnpassbook_tbl_10074 PARTITION OF log.txnpassbook_tbl_part FOR VALUES IN (10074);
CREATE TABLE log.txnpassbook_tbl_10076 PARTITION OF log.txnpassbook_tbl_part FOR VALUES IN (10076);

CREATE TABLE log.txnpassbook_tbl_10078 PARTITION OF log.txnpassbook_tbl_part FOR VALUES IN (10078);
CREATE TABLE log.txnpassbook_tbl_10072 PARTITION OF log.txnpassbook_tbl_part FOR VALUES IN (10072);
CREATE TABLE log.txnpassbook_tbl_10073 PARTITION OF log.txnpassbook_tbl_part FOR VALUES IN (10073);

CREATE TABLE log.txnpassbook_tbl_10079 PARTITION OF log.txnpassbook_tbl_part FOR VALUES IN (10079); 
CREATE TABLE log.txnpassbook_tbl_10099 PARTITION OF log.txnpassbook_tbl_part FOR VALUES IN (10099);



