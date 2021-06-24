
-- mPoint DB Scripts :

-- CMP-5664------
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES('IS_STORE_BILLING_ADDRS', 'true', 10101, 'client', true, 0);

-- Insert the Property for CYBS
INSERT INTO client.additionalproperty_tbl ( key, value, externalid, type, enabled, scope) VALUES ('CYBS_MerchantDescriptor', 'Avianca S.A.',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10101 and pspid = 63) ,'merchant', true, 1);
