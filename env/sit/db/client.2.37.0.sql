UPDATE client.split_configuration_tbl SET type = 'hybrid' WHERE trim(name) IN ('Card+Voucher','APM+Voucher','Wallet+Voucher');
UPDATE client.split_configuration_tbl SET type = 'conventional' WHERE trim(name) IN ('Card+Card');

SELECT setval('client.route_tbl_id_seq', COALESCE((SELECT MAX(id)+1 FROM client.route_tbl), 1), false);
SELECT setval('client.routeconfig_tbl_id_seq', COALESCE((SELECT MAX(id)+1 FROM client.routeconfig_tbl), 1), false);
SELECT setval('client.routecountry_tbl_id_seq', COALESCE((SELECT MAX(id)+1 FROM client.routecountry_tbl), 1), false);
SELECT setval('client.routecurrency_tbl_id_seq', COALESCE((SELECT MAX(id)+1 FROM client.routecurrency_tbl), 1), false);
SELECT setval('client.routefeature_tbl_id_seq', COALESCE((SELECT MAX(id)+1 FROM client.routefeature_tbl), 1), false);
SELECT setval('client.routepm_tbl_id_seq', COALESCE((SELECT MAX(id)+1 FROM client.routepm_tbl), 1), false);
