alter table client.cardaccess_tbl
	add capture_method integer default 0;

comment on column client.cardaccess_tbl.capture_method is '0 - manual
2 - bulk capture
3 - bulk refund
6 - bulk capture + bulk  refund';


UPDATE client.cardaccess_tbl ca SET capture_method = p.capture_method FROM system.psp_tbl p WHERE ca.pspid = p.id

alter table system.psp_tbl drop column capture_method;