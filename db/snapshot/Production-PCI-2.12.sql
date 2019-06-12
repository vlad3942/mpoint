--19-08-SIT-Build.sql ::
alter table client.cardaccess_tbl
	add capture_method integer default 0;

comment on column client.cardaccess_tbl.capture_method is '0 - manual
2 - bulk capture
3 - bulk refund
6 - bulk capture + bulk  refund';

UPDATE client.cardaccess_tbl ca SET capture_method = p.capture_method FROM system.psp_tbl p WHERE ca.pspid = p.id;

alter table system.psp_tbl drop column capture_method;


--/db/snapshot/19-10-GetStatus.sql ::not sign off
-- Get status Additional properties
-- Retry count value should be integer
insert into client.additionalproperty_tbl (key,value,enabled,externalid,type,scope) values ('status_retry_count',<value>,true,<clientid>,'client',2);
-- Retry count value should be integer and it will be seconds
insert into client.additionalproperty_tbl (key,value,enabled,externalid,type,scope) values ('status_retry_interval',<value>,true,<clientid>,'client',2);


--db/snapshot/19-10-SIT-Build.sql ::
alter table log.transaction_tbl
	add issuing_bank varchar(100);

ALTER TABLE client.client_tbl ADD COLUMN enable_cvv boolean DEFAULT true;
