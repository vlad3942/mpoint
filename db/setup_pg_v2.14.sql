--CMP-3015 default value set to empty
alter table client.merchantaccount_tbl alter column username set default 'empty';
alter table client.merchantaccount_tbl alter column passwd set default 'empty';

--SIT PR enable this endpoint for wallet CVV flow
INSERT INTO client.endpointaccess_tbl (groupid, endpointid) select 39, id from system.endpoint_tbl where path='mpoint/get-payment-summary';

--Prod SQL PR enable this endpoint for wallet CVV flow
INSERT INTO client.endpointaccess_tbl (groupid, endpointid) select <groupid>, id from system.endpoint_tbl where path='mpoint/get-payment-summary';