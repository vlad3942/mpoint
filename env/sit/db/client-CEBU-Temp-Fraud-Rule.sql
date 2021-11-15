------CMP-6091----------
-------World pay-------
UPDATE client.additionalproperty_tbl SET value = 'isPostFraudAttemp::=<status>=="1"OR<status>=="4"AND<tempRule>
status::=(card.info-3d-secure.additional-data.param[@name=''status''])
tempRule::=(transaction.@type)=="5"OR(transaction.@type)=="3"' WHERE key = 'post_fraud_rule' and externalid=(SELECT id from client.merchantaccount_tbl where clientid=10077 and pspid=4 and enabled=true);

------------First Data-------------

UPDATE client.additionalproperty_tbl SET value = 'isPostFraudAttemp::=<status>=="1"OR<status>=="2"OR<status>=="4"AND<tempRule>
status::=(card.info-3d-secure.additional-data.param[@name='status'])
tempRule::=(transaction.@type)=="5"OR(transaction.@type)=="3"' WHERE key = 'post_fraud_rule' and externalid=(SELECT id from client.merchantaccount_tbl where clientid=10077 and pspid=62 and enabled=true);

-------------MPGS-------------

UPDATE client.additionalproperty_tbl SET value = 'isPostFraudAttemp::=<isPostFraudAttemp1>OR<isPostFraudAttemp2>OR<isPostFraudAttemp3>OR<isPostFraudAttemp4>AND<tempRule>
isPostFraudAttemp1::=<eci>=="02"AND<isCryptogrm>!==""
isPostFraudAttemp2::=<eci>=="05"AND<isCryptogrm>!==""
isPostFraudAttemp3::=<eci>=="01"AND<isCryptogrm>!==""
isPostFraudAttemp4::=<eci>=="07"AND<isCryptogrm>!==""
eci::=(card.info-3d-secure.cryptogram.@eci)
isCryptogrm::={trim.(card,info-3d-secure,cryptogram)}
tempRule::=(transaction.@type)=="5"OR(transaction.@type)=="3"' WHERE key = 'post_fraud_rule' and externalid=(SELECT id from client.merchantaccount_tbl where clientid=10077 and pspid=72 and enabled=true);


-------------------ROLLBACK sql--------------------

UPDATE client.additionalproperty_tbl SET value = 'isPostFraudAttemp::=<status>=="1"OR<status>=="4"
status::=(card.info-3d-secure.additional-data.param[@name=''status''])' WHERE key = 'post_fraud_rule' and externalid=(SELECT id from client.merchantaccount_tbl where clientid=10077 and pspid=4 and enabled=true);


UPDATE client.additionalproperty_tbl SET value = 'isPostFraudAttemp::=<status>=="1"OR<status>=="2"OR<status>=="4"
status::=(card.info-3d-secure.additional-data.param[@name='status'])' WHERE key = 'post_fraud_rule' and externalid=(SELECT id from client.merchantaccount_tbl where clientid=10077 and pspid=62 and enabled=true);


UPDATE client.additionalproperty_tbl SET value = 'isPostFraudAttemp::=<isPostFraudAttemp1>OR<isPostFraudAttemp2>OR<isPostFraudAttemp3>OR<isPostFraudAttemp4>
isPostFraudAttemp1::=<eci>=="02"AND<isCryptogrm>!==""
isPostFraudAttemp2::=<eci>=="05"AND<isCryptogrm>!==""
isPostFraudAttemp3::=<eci>=="01"AND<isCryptogrm>!==""
isPostFraudAttemp4::=<eci>=="07"AND<isCryptogrm>!==""
eci::=(card.info-3d-secure.cryptogram.@eci)
isCryptogrm::={trim.(card,info-3d-secure,cryptogram)}' WHERE key = 'post_fraud_rule' and externalid=(SELECT id from client.merchantaccount_tbl where clientid=10077 and pspid=72 and enabled=true);
