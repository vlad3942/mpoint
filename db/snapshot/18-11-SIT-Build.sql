ALTER TABLE log.transaction_tbl
ADD approval_action_code varchar(40) NULL;
COMMENT ON COLUMN log.transaction_tbl.approval_action_code
IS 'This field contains an action code and approval code
"approval code":"action code"'

--NETS acquirer additional property

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('MERCHANT_COUNTRY', '208', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('MERCHANT_NAME', 'Cellpoint Mobile', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('MERCHANT_GMTOFFSET', '+1', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('MERCHANT_URL', 'http://www.cpm.com', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('FORCED_AUTH', 'true', 10007, 'client');

--END NETS acquirer additional property