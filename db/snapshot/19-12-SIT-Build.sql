
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
    SELECT 'PAYPAL_BILLING_AGREEMENT', 'This is billing agreement with CellPoint Mobile', id, 'merchant', 2 FROM client.merchantaccount_tbl WHERE pspid=24
