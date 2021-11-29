INSERT INTO client.url_tbl (urltypeid, clientid, url, enabled) VALUES (17, 10101, 'payments.avtest.ink', true);


-- Fraud Rules
UPDATE client.additionalproperty_tbl SET value = 'isPostFraudAttemp::=<status>=="1"OR<status>=="4"OR<tempRule>
status::=(card.info-3d-secure.additional-data.param[@name=''status''])
tempRule::=(transaction.@type)=="5"OR(transaction.@type)=="3"])' WHERE key = 'post_fraud_rule' and externalid=(SELECT id from client.merchantaccount_tbl where clientid=10077 and pspid=4 and enabled=true);

UPDATE client.additionalproperty_tbl SET value = 'isPostFraudAttemp::=<status>=="1"OR<status>=="2"OR<status>=="4"OR<tempRule>
status::=(card.info-3d-secure.additional-data.param[@name=''status''])
tempRule::=(transaction.@type)=="5"OR(transaction.@type)=="3"])' WHERE key = 'post_fraud_rule' and externalid=(SELECT id from client.merchantaccount_tbl where clientid=10077 and pspid=62 and enabled=true);