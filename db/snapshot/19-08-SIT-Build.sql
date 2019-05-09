-- eGHL phase 2 - additional property
-- cachetimeout default value is 5
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('cachetimeout', <frequency of cache refresh>, true, <id of merchantaccount_tbl>, 'merchant', 0);
-- logourl default value is https://securepay.e-ghl.com/IPG/assets3/img/
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('logourl', <Base URL of logo>, true, <id of merchantaccount_tbl>, 'merchant', 0);