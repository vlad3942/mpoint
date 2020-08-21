---2c2p-alc Rule---
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'post_fraud_rule', 'isPostFraudAttemp::=<isPostFraudAttemp1>OR<isPostFraudAttemp2>OR<isPostFraudAttemp3>OR<isPostFraudAttemp4>
isPostFraudAttemp1::=<cardid>=="7"AND<eci>=="2"AND<isCryptogrm>!==""
isPostFraudAttemp2::=<cardid>=="7"AND<eci>=="1"AND<isCryptogrm>!==""
isPostFraudAttemp3::=<cardid>=="8"AND<eci>=="5"AND<isCryptogrm>!==""
isPostFraudAttemp4::=<cardid>=="8"AND<eci>=="6"AND<isCryptogrm>!==""
eci::=(card.info-3d-secure.cryptogram.@eci)
cardid::=(card.@type-id)
isCryptogrm::={trim.(card,info-3d-secure,cryptogram)}', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=40;

---First Data Rule---
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'post_fraud_rule', 'isPostFraudAttemp::=<eci>=="1"OR<eci>=="2"OR<eci>=="4"
eci::=(card.info-3d-secure.additional-data.param[@name=''Secure3DResponse''])', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=62;

INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2017, 'Authorization not attempted due to rule matched', 'Payment', '');

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) select 'RestrictedTicket', '1', id, 'merchant',2 from client.merchantaccount_tbl WHERE clientid=<> AND pspid=4;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) select 'FareBasisCode', 'BK', id, 'merchant',2 from client.merchantaccount_tbl WHERE clientid=<> AND pspid=4;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) select 'TravelAgencyName', 'CebuPacificair', id, 'merchant',2 from client.merchantaccount_tbl WHERE clientid=<> AND pspid=4;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) select 'TravelAgencyCode', '5J', id, 'merchant',2 from client.merchantaccount_tbl WHERE clientid=<> AND pspid=4;
