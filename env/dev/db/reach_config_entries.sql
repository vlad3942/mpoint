Insert into client.countrycurrency_tbl (clientid, countryid, currencyid) values (<client-id>, <country-id>, <currency-id>);

INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, enabled, username, passwd, supportedpartialoperations)
VALUES (<client-id>, 78, 'Reach', true, '<username>', '<password>', 0);

INSERT INTO client.route_tbl (clientid, providerid) VALUES(<client-id>, 78);

/*
By default below entries will be done by MO (vision portal) only and but will be useful if you want to do the entries manually

INSERT INTO client.routeconfig_tbl(routeid, name, capturetype, mid, username, password, enabled) SELECT id, 'Reach', 2, '<MID>', '<username>', '<password>', enabled FROM client.route_tbl WHERE clientid = <client-id> AND providerid = 78;

INSERT INTO client.routecountry_tbl(routeconfigid, countryid) SELECT rc.id, <country-id> FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = <client-id> and rc.username ='<username>' and rc.mid = '<mid>';

INSERT INTO client.routecurrency_tbl(routeconfigid, currencyid) SELECT rc.id, <currency-id> FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = <client-id> and rc.username ='<username>' and rc.mid = '<mid>';

INSERT INTO client.routefeature_tbl (clientid,routeconfigid,featureid,enabled) VALUES
(<client-id>,(SELECT id FROM client.routeconfig_tbl WHERE name = '<name>' and mid = '<mid>' ),<feature-id>,true);

INSERT INTO client.routepm_tbl (routeconfigid,pmid,enabled) VALUES
((SELECT id FROM client.routeconfig_tbl WHERE name = '<name>' and mid = '<mid>' ),<card-id>,true);

INSERT INTO client.route_property_tbl
(clientid, propertyid, value, enabled)
VALUES(<client-id>, (SELECT id FROM system.route_property_tbl WHERE pspid = 78 and name = 'HMAC_SECRET'), '<HMAC-SECRET>', true);
*/