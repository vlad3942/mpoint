
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
    SELECT 'PAYPAL_BILLING_AGREEMENT', 'This is billing agreement with CellPoint Mobile', id, 'merchant', 2 FROM client.merchantaccount_tbl WHERE pspid=24 AND clientid = <client id>;

--CMP-3015 default value set to empty
alter table client.merchantaccount_tbl alter column username set default 'empty';
alter table client.merchantaccount_tbl alter column passwd set default 'empty';
