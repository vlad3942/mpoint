-- Paypal subject
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
    SELECT 'PAYPAL_SUBJECT', 'pal_paypal_sandbox@pal.com.ph', id, 'merchant', 2 FROM client.merchantaccount_tbl WHERE pspid=24 AND clientid = <clientid>;
