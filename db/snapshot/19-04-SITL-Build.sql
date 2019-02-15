--SGAMBE-4207
ALTER TABLE client.client_tbl ALTER COLUMN smsrcpt SET DEFAULT FALSE ;
UPDATE client.client_tbl set smsrcpt=false where id=10021;