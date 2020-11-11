INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) select 'MAX_DOWNLOAD_FILE_LIMIT', '2', id, 'merchant', from client.merchantaccount_tbl WHERE clientid=10069 AND pspid=52;

DELETE FROM client.additionalproperty_tbl where key = 'mpi_rule';

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'mpi_rule', 'isProceedAuth::=<status>=="2"OR<status>=="5"OR<status>=="6"
status::=(additional-data.param[@name=''status''])', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10077 AND pspid=4;