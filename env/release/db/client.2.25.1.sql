-- mPoint DB Scripts :

-- Paymaya DB Scripts --
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, countryid,psp_type,capture_type) VALUES (10077, 95, 68, true, 640,4,2);
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10077,68, <name/mid>, <USERNAME> , <PASSWORD>);
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100770, 68, 'Paymaya');

-- Paymaya SR migration scripts --
INSERT into client.route_tbl ( clientid, providerid, enabled)
SELECT clientid, pspid, enabled FROM client.merchantaccount_tbl WHERE clientid=10077 and name='PayMaya';
INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'PayMaya', 2, <MID>, <USERNAME>, <PASSWORD>, enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 68;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid = <MID>;
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = <MID>;



--Payment Center DB Scripts --
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, countryid,psp_type,capture_type) VALUES (10077, 96, 69, true, NULL,4,2);
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10077,69, 'CEBU PaymentCenter', <USERNAME> , <PASSWORD>);
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100770, 69, 'CEBU PaymentCenter');

-- PaymentCenter SR migration scripts --
INSERT into client.route_tbl ( clientid, providerid, enabled)
SELECT clientid, pspid, enabled FROM client.merchantaccount_tbl WHERE clientid=10077 and name='PaymentCenter';
INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'CEBU PaymentCenter', 2, <MID>, <USERNAME>, <PASSWORD>, enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 69;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid = <MID>;
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = <MID>;