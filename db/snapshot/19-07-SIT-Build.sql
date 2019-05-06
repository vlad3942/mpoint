-- MID selection based on card id -
--sample scripts are for 2c2p JCB card issue
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('MID.5', <MID>, true, <id of merchantaccount_tbl>, 'merchant', 0);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('mechantaccountrule', 'merchantaccount ::= (property[@name=''<midpath>''])   
 midpath ::= "MID."(@card-id)', true, <id of merchantaccount_tbl>, 'merchant', 0);