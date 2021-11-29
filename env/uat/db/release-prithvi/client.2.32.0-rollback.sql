-- HPP Host URL
DELETE FROM client.url_tbl WHERE urltypeid = 17  and clientid = 10101;

-- Fraud Rules
UPDATE client.additionalproperty_tbl SET value = 'isPostFraudAttemp::=<status>=="1"OR<status>=="4"
status::=(card.info-3d-secure.additional-data.param[@name=''status''])' WHERE key = 'post_fraud_rule' and externalid=(SELECT id from client.merchantaccount_tbl where clientid=10077 and pspid=4 and enabled=true);

UPDATE client.additionalproperty_tbl SET value = 'isPostFraudAttemp::=<status>=="1"OR<status>=="2"OR<status>=="4"
status::=(card.info-3d-secure.additional-data.param[@name=''status''])' WHERE key = 'post_fraud_rule' and externalid=(SELECT id from client.merchantaccount_tbl where clientid=10077 and pspid=62 and enabled=true);