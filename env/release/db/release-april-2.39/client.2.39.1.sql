--Entry in route tables for Ingenico USA
INSERT INTO client.routeconfig_tbl(routeid, name, capturetype, mid, username, password, enabled) SELECT id, 'Ingenico_USA ', 2, '12931', '8fc3f1b12679026b', '6T9urEzMH+5Z/Ybxy+Ez2LNVyTDxZ5Xulk7ZBEXbf+8=', enabled FROM client.route_tbl WHERE clientid = 10101 AND providerid = 21 and enabled=true;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10101 and mid ='12931' and capturetype = 2 and rc.enabled=true;
INSERT INTO client.routecurrency_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10101 and mid ='12931' and capturetype = 2 and rc.enabled=true;

--Entry in route tables for Ingenico Others
INSERT INTO client.routeconfig_tbl(routeid, name, capturetype, mid, username, password, enabled) SELECT id, 'Ingenico_NAM_Others ', 2, '12933', '8fc3f1b12679026b', '6T9urEzMH+5Z/Ybxy+Ez2LNVyTDxZ5Xulk7ZBEXbf+8=', enabled FROM client.route_tbl WHERE clientid = 10101 AND providerid = 21 and enabled=true;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10101 and mid ='12933' and capturetype = 2 and rc.enabled=true;
INSERT INTO client.routecurrency_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10101 and mid ='12933' and capturetype = 2 and rc.enabled=true;

--Entry in route tables for Ingenico Costa Rica
INSERT INTO client.routeconfig_tbl(routeid, name, capturetype, mid, username, password, enabled) SELECT id, 'Ingenico_CR ', 2, '12932', '8fc3f1b12679026b', '6T9urEzMH+5Z/Ybxy+Ez2LNVyTDxZ5Xulk7ZBEXbf+8=', enabled FROM client.route_tbl WHERE clientid = 10101 AND providerid = 21 and enabled=true;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10101 and mid ='12932' and capturetype = 2 and rc.enabled=true;
INSERT INTO client.routecurrency_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10101 and mid ='12932' and capturetype = 2 and rc.enabled=true;