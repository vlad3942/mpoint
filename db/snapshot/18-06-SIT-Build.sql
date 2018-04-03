-- Alipay Chinese PIDs --

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) SELECT 'pid.html', '2088102135220161', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10007 AND pspid=43 ;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) SELECT 'pid.app', '2088102170185364', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10007 AND pspid=43 ;
