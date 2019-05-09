-- eGHL phase 2 - additional property
-- cachetimeout default value is 5
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('cachetimeout', <frequency of cache refresh>, true, <id of merchantaccount_tbl>, 'merchant', 0);
-- logourl default value is https://securepay.e-ghl.com/IPG/assets3/img/
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('logourl', <Base URL of logo>, true, <id of merchantaccount_tbl>, 'merchant', 0);
alter table client.cardaccess_tbl
	add capture_method integer default 0;

comment on column client.cardaccess_tbl.capture_method is '0 - manual
2 - bulk capture
3 - bulk refund
6 - bulk capture + bulk  refund';


UPDATE client.cardaccess_tbl ca SET capture_method = p.capture_method FROM system.psp_tbl p WHERE ca.pspid = p.id

alter table system.psp_tbl drop column capture_method;