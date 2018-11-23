INSERT INTO log.state_tbl (id, name, module) VALUES (20101, 'Payment rejected due to incorrect card data', 'Callback');
INSERT INTO log.state_tbl (id, name, module) VALUES (20102, 'Payment rejected as PSP unavailable', 'Callback');
INSERT INTO log.state_tbl (id, name, module) VALUES (20103, 'Payment rejected due to 3D authorization failure', 'Callback');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'mid.USD', '80000715', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10021 AND pspid=17 ;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'mid.AED', '80000717', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10021 AND pspid=17 ;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'mid.SAR', '80562', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10021 AND pspid=17 ;

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'username.USD', 'merchant.80000715', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10021 AND pspid=17 ;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'password.USD', 'b08d9a8a4dc6a76df5a1a9b3726fbe87', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10021 AND pspid=17 ;

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'username.AED', 'merchant.80000717', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10021 AND pspid=17 ;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'password.AED', '9692f374496b22633a1e73a6a38320fc', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10021 AND pspid=17 ;

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'username.SAR', 'merchant.80562', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10021 AND pspid=17 ;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'password.SAR', '332cdb0b67cdeee66e2e6f515adc4fc4', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10021 AND pspid=17 ;

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'HOST', 'ap-gateway.mastercard.com', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10021 AND pspid=17 ;