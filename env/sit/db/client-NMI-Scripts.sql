--Legacy
INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, enabled, username, passwd, supportedpartialoperations)
VALUES (<client-id>, 74, 'Nmi Credomatic', true, '-1', 'xbSK528tPxujQfM7Cu25xjC4VKMS9864', 0);
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<client-account-id>, 74, '-1');

-- Non-Legacy
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<client-account-id>, 74, '-1');
INSERT INTO client.route_tbl (clientid, providerid) VALUES(<client-id>, 74);
INSERT INTO client.routeconfig_tbl(routeid, name, capturetype, mid, username, password, enabled) SELECT id, 'Nmi Credomatic', 2, 'xbSK528tPxujQfM7Cu25xjC4VKMS9864', '', '', enabled FROM client.route_tbl WHERE clientid = <client-id> AND providerid = 74;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10101 and mid ='xbSK528tPxujQfM7Cu25xjC4VKMS9864' and capturetype = 2;
INSERT INTO client.routecurrency_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10101 and mid ='xbSK528tPxujQfM7Cu25xjC4VKMS9864' and capturetype = 2;

-- country-currency mapping
INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid) VALUES (<client-id>,<country-id>, <currency-id>);
