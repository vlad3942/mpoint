--Database:MPOINT (As per JIRA:https://cellpointdigital.atlassian.net/browse/AVPOP-754)

--Entry in route tables for NMI
INSERT INTO client.route_tbl (clientid, providerid) VALUES(10101, 74);

INSERT INTO client.routeconfig_tbl(routeid, name, capturetype, mid, username, password, enabled) SELECT id, 'Nmi Credomatic', 2, 'xbSK528tPxujQfM7Cu25xjC4VKMS9864', '', '', enabled FROM client.route_tbl WHERE clientid = 10101 AND providerid = 74;

INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10101 and mid ='xbSK528tPxujQfM7Cu25xjC4VKMS9864' and capturetype = 2;

INSERT INTO client.routecurrency_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10101 and mid ='xbSK528tPxujQfM7Cu25xjC4VKMS9864' and capturetype = 2;

-----------------------------------------------------------------------------------------------------------
--Mapping in Merchant Subaccount
--POS->US
--Web
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (101117, 74, '-1');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (101117, 4, '-1');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (101117, 21, '-1');
--APP
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (101120, 74, '-1');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (101120, 4, '-1');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (101120, 21, '-1');
-----------------------------------------------------------------------------------------------------------

--POS->NAM
--Web
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (101118, 4, '-1');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (101118, 21, '-1');
--APP
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (101121, 4, '-1');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (101121, 21, '-1');
------------------------------------------------------------------------------------------------------------
--POS-CostaRica
--WEB
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (101119, 4, '-1');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (101119, 21, '-1');
--APP
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (101122, 4, '-1');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (101122, 21, '-1');
------------------------------------------------------------------------------------------------------------

--Country-currency mapping (As per https://cellpointdigital.atlassian.net/browse/AVPOP-734)
INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid) VALUES (10101,200, 840);--USA
INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid) VALUES (10101,428, 840);--Panama
INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid) VALUES (10101,408, 840);--El Salvador
INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid) VALUES (10101,410, 840);--Guatemala
INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid) VALUES (10101,412, 840);--Honduras
INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid) VALUES (10101,427, 840);--Nicaragua
INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid) VALUES (10101,209, 840);--Dominican Republic
INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid) VALUES (10101,429, 840);--Peru
INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid) VALUES (10101,404, 840);--Chile
INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid) VALUES (10101,439, 840);--Uruguay
INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid) VALUES (10101,432, 840);--Paraguay
INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid) VALUES (10101,304, 840);--Bolivia
INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid) VALUES (10101,407, 840);--Ecuador
INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid) VALUES (10101,201, 840);--Mexico
INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid) VALUES (10101,202, 840);--Canada
INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid) VALUES (10101,406, 840);--Costa Rica
--------------------------------------------------------------------------------------------------------------
--Entry in route tables for Worldpay USA
INSERT INTO client.routeconfig_tbl(routeid, name, capturetype, mid, username, password, enabled) SELECT id, 'WorldPay_USA ', 2, 'AVIANCAECOMUSD', 'AVIANCAECOMUSD', 'Avianca.1CPDUS', enabled FROM client.route_tbl WHERE clientid = 10101 AND providerid = 4 and enabled=true;

INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10101 and mid ='AVIANCAECOMUSD' and capturetype = 2 and rc.enabled=true;

INSERT INTO client.routecurrency_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10101 and mid ='AVIANCAECOMUSD' and capturetype = 2 and rc.enabled=true;

--Entry in route tables for Worldpay Others
INSERT INTO client.routeconfig_tbl(routeid, name, capturetype, mid, username, password, enabled) SELECT id, 'WorldPay_others ', 2, 'AVIANCAECOMOTHERSUSD', 'AVIANCAECOMOTHERSUSD', 'Avianca.1CPDOT', enabled FROM client.route_tbl WHERE clientid = 10101 AND providerid = 4 and enabled=true;

INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10101 and mid ='AVIANCAECOMOTHERSUSD' and capturetype = 2 and rc.enabled=true;

INSERT INTO client.routecurrency_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10101 and mid ='AVIANCAECOMOTHERSUSD' and capturetype = 2 and rc.enabled=true;

--Entry in route tables for Worldpay Costa Rica
INSERT INTO client.routeconfig_tbl(routeid, name, capturetype, mid, username, password, enabled) SELECT id, 'WorldPay_CR ', 2, 'AVIANCAECOMCRUSD', 'AVIANCAECOMCRUSD', 'Avianca.1CPDCR', enabled FROM client.route_tbl WHERE clientid = 10101 AND providerid = 4 and enabled=true;

INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10101 and mid ='AVIANCAECOMCRUSD' and capturetype = 2 and rc.enabled=true;

INSERT INTO client.routecurrency_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10101 and mid ='AVIANCAECOMCRUSD' and capturetype = 2 and rc.enabled=true;