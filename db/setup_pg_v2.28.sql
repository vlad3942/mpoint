-- CMP-5595 & CMP-5596
INSERT INTO client.additionalproperty_tbl ( key, value, externalid, type, enabled, scope) VALUES ('COUPON_GEN', 'true',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10101 and pspid = 70) ,'merchant', true, 1);
INSERT INTO client.additionalproperty_tbl ( key, value, externalid, type, enabled, scope) VALUES ('COUPON_BUCKET_NAME', 'av-sp',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10101 and pspid = 70) ,'merchant', true, 1);

-- CMP-5546
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'post_fraud_rule', 'isPostFraudAttemp::=<isPostFraudAttemp1>OR<isPostFraudAttemp2>OR<isPostFraudAttemp3>OR<isPostFraudAttemp4>
isPostFraudAttemp1::=<eci>=="2"AND<isCryptogrm>!==""
isPostFraudAttemp2::=<eci>=="5"AND<isCryptogrm>!==""
isPostFraudAttemp3::=<eci>=="1"AND<isCryptogrm>!==""
isPostFraudAttemp4::=<eci>=="7"AND<isCryptogrm>!==""
eci::=(card.info-3d-secure.cryptogram.@eci)
isCryptogrm::={trim.(card,info-3d-secure,cryptogram)}', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10077 AND pspid=72;
