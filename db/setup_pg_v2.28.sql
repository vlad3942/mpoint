-- CMP-5595 & CMP-5596
INSERT INTO client.additionalproperty_tbl ( key, value, externalid, type, enabled, scope) VALUES ('COUPON_GEN', 'true',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10101 and pspid = 70) ,'merchant', true, 1);
INSERT INTO client.additionalproperty_tbl ( key, value, externalid, type, enabled, scope) VALUES ('COUPON_BUCKET_NAME', 'av-sp',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10101 and pspid = 70) ,'merchant', true, 1);

