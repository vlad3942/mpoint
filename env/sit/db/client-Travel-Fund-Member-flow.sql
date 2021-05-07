-- mPoint DB Scripts For Travel Fund:  CMP-5450
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('autoFetchBalance', 'true', true, 10077, 'client', 0);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('fetchBalanceUserType', '{"1":2}', true, 10077, 'client', 0);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('fetchBalancePaymentMethods', '{"1":26}', true, 10077, 'client', 0);