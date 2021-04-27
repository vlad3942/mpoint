-- mPoint DB Scripts For Travel Fund:

INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10077, 26, 71);
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username) VALUES (10077, 71, '', '');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) SELECT A.id, 71, '-1' FROM Client.Account_Tbl A, System.PSP_Tbl P WHERE clientid = 10077 GROUP BY A.id;
INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid) VALUES (10077,640,208);

INSERT INTO client.route_tbl (clientid, providerid) VALUES(10077, 71);

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'travelfund', 2, 'travelfund', 'travelfund', 'travelfund', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 71;
 
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='travelfund';
 
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'travelfund';


-- Split payment configs
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('SplitPaymentFOPConfig', '{"1": -1, "2": -1}', false, 10077, 'client', 0);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('SplitPaymentConfig', '{"split_count":2,"combinations":[{"combination":[{"index":1,"id":1,"is_clubbable":true},{"index":2,"id":2,"is_clubbable":true}]}]}', false, 10077, 'client', 0);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('sessiontype', '2', false, 10077, 'client', 0);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('isVoucherPreferred', 'false', false, 10077, 'client', 0);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('PROXY_CALLBACK', 'https://mpoint.prod-01.cellpoint.cloud/cebu/callback.php', true, 10077, 'client', 0);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES ('max_session_retry_count', 1000, 10077, 'client', true, 0);