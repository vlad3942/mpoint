ALTER TABLE log.address_tbl add last_name varchar(200) null;
ALTER TABLE log.address_tbl RENAME COLUMN name TO first_name;
--Granular status codes
alter table log.state_tbl alter column name type character varying(120);
