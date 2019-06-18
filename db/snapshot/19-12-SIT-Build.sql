
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
    SELECT 'PAYPAL_BILLING_AGREEMENT', 'This is billing agreement with CellPoint Mobile', id, 'merchant', 2 FROM client.merchantaccount_tbl WHERE pspid=24 AND clientid = <client id>;

--CMP-3015 default value set to empty
alter table client.merchantaccount_tbl alter column username set default 'empty';
alter table client.merchantaccount_tbl alter column passwd set default 'empty';

--SIT PR enable this endpoint for wallet CVV flow
INSERT INTO client.endpointaccess_tbl (groupid, endpointid) select 39, id from system.endpoint_tbl where path='mpoint/get-payment-summary';

--Prod SQL PR enable this endpoint for wallet CVV flow
INSERT INTO client.endpointaccess_tbl (groupid, endpointid) select <groupid>, id from system.endpoint_tbl where path='mpoint/get-payment-summary';
