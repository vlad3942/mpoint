-- mPoint DB Scripts For Travel Fund:

INSERT INTO client.route_tbl (clientid, providerid) VALUES(10077, 71);

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'travelfund', 2, 'travelfund', 'travelfund', 'travelfund', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 71;
 
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='travelfund';
 
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'travelfund';