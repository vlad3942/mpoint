-------- TXN Time Out -------------

UPDATE log.state_tbl SET name = 'Issuer timed out' WHERE id = 20109;
INSERT INTO log.state_tbl (id, name, module) VALUES (20108, 'PSP timed out', 'Payment');

-------- TXN Time Out -------------

ALTER TABLE client.account_tbl  ALTER COLUMN markup type character varying(20); 
ALTER TABLE log.transaction_tbl  ALTER COLUMN markup type character varying(20); 
