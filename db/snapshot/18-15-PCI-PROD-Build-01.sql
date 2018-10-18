INSERT INTO log.state_tbl (id, name, module) VALUES (20101, 'Payment rejected due to incorrect card data', 'Callback');
INSERT INTO log.state_tbl (id, name, module) VALUES (20102, 'Payment rejected as PSP unavailable', 'Callback');
INSERT INTO log.state_tbl (id, name, module) VALUES (20103, 'Payment rejected due to 3D authorization failure', 'Callback');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'mid.USD', '80000715', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10021 AND pspid=17 ;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'mid.AED', '80000717', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10021 AND pspid=17 ;