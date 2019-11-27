-- HPP form redirect method set to GET for all the client

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10000, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10014, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10062, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10019, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10070, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10071, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10066, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10060, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10072, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10067, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10075, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10061, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10069, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10074, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10020, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10021, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10073, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10065, 'client', true);


-- UATP CeptorAccessId and CeptorAccessKey
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES ('CeptorAccessId', 'cellpointuser', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10069 and pspid = 50), 'merchant',1);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES ('CeptorAccessKey', 'PhkdD7IB', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10069 and pspid = 50), 'merchant',1);

