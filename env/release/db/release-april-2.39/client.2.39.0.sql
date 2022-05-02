INSERT INTO client.fraud_config_tbl (clientid, pmid, providerid, countryid, currencyid, typeoffraud) VALUES(10101, 22, 64, 0, 0, 1);

SELECT setval('client.split_combination_tbl_id_seq', COALESCE((SELECT MAX(id)+1 FROM client.split_combination_tbl), 1), false);
SELECT setval('client.split_configuration_tbl_id_seq', COALESCE((SELECT MAX(id)+1 FROM client.split_configuration_tbl), 1), false);