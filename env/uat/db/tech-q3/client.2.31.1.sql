------CMP-6091----------
-------World pay-------
UPDATE client.additionalproperty_tbl SET value = 'isPostFraudAttemp::=<status>=="1"OR<status>=="4"AND<tempRule>
status::=(card.info-3d-secure.additional-data.param[@name=''status''])
tempRule::=(transaction.@type)=="5"OR(transaction.@type)=="3"' WHERE key = 'post_fraud_rule' and externalid=(SELECT id from client.merchantaccount_tbl where clientid=10077 and pspid=4 and enabled=true);

------------First Data-------------

UPDATE client.additionalproperty_tbl SET value = 'isPostFraudAttemp::=<status>=="1"OR<status>=="2"OR<status>=="4"AND<tempRule>
status::=(card.info-3d-secure.additional-data.param[@name='status'])
tempRule::=(transaction.@type)=="5"OR(transaction.@type)=="3"' WHERE key = 'post_fraud_rule' and externalid=(SELECT id from client.merchantaccount_tbl where clientid=10077 and pspid=62 and enabled=true);


-------------------ROLLBACK sql--------------------

-- UPDATE client.additionalproperty_tbl SET value = 'isPostFraudAttemp::=<status>=="1"OR<status>=="4" status::=(card.info-3d-secure.additional-data.param[@name=''status''])' WHERE key = 'post_fraud_rule' and externalid=(SELECT id from client.merchantaccount_tbl where clientid=10077 and pspid=4 and enabled=true);


-- UPDATE client.additionalproperty_tbl SET value = 'isPostFraudAttemp::=<status>=="1"OR<status>=="2"OR<status>=="4" status::=(card.info-3d-secure.additional-data.param[@name='status'])' WHERE key = 'post_fraud_rule' and externalid=(SELECT id from client.merchantaccount_tbl where clientid=10077 and pspid=62 and enabled=true);
