
-- mPoint DB Scripts :

-- CMP-5546
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'post_fraud_rule', 'isPostFraudAttemp::=<isPostFraudAttemp1>OR<isPostFraudAttemp2>OR<isPostFraudAttemp3>OR<isPostFraudAttemp4>
isPostFraudAttemp1::=<eci>=="02"AND<isCryptogrm>!==""
isPostFraudAttemp2::=<eci>=="05"AND<isCryptogrm>!==""
isPostFraudAttemp3::=<eci>=="01"AND<isCryptogrm>!==""
isPostFraudAttemp4::=<eci>=="07"AND<isCryptogrm>!==""
eci::=(card.info-3d-secure.cryptogram.@eci)
isCryptogrm::={trim.(card,info-3d-secure,cryptogram)}', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10077 AND pspid=72;