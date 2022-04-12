SELECT setval('client.split_combination_tbl_id_seq', COALESCE((SELECT MAX(id)+1 FROM client.split_combination_tbl), 1), false);

SELECT setval('client.split_configuration_tbl_id_seq', COALESCE((SELECT MAX(id)+1 FROM client.split_configuration_tbl), 1), false);
