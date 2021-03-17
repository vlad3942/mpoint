--- mPoint Client Schema - for SR Migration

------------------------------------------------------------------------------
-- Enable client to use SR flow i.e non legacy flow:

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('IS_LEGACY', 'false',10077, 'client', 2);

-- Migration of existing merchant route details
INSERT into client.route_tbl (id, clientid, providerid, enabled)
SELECT id, clientid, pspid, enabled FROM client.merchantaccount_tbl WHERE clientid=10077;

-- 2C2P-alc - USD
INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, '2c2p-alc_Master_VISA_USD', 2, <MID>, <USERNAME>, <PASSWORD>, enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 40;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid = <MID>;
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = <MID>;


-- 2C2P-alc - MCC

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, '2c2p-alc_Master_VISA_PHP', 2, <MID>, <USERNAME>, <PASSWORD>, enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 40;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid = <MID>;
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on
        r.id = rc.routeid where r.clientid = 10077 and rc.mid = <MID>;


-- PAYPAL PHP : need to check currency for Paypal PHP / USD
INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'Paypal_PHP', 2, <MID>, <USERNAME>, <PASSWORD>, enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 24;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid = <MID>;
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = <MID>;


-- PAYPAL SGD
INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'Paypal_SGD', 2, <MID>, <USERNAME>, <PASSWORD>, enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 24;

INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid =<MID>;
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = <MID>;


-- PAYPAL HKD
INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'Paypal_HKD', 2, <MID>, <USERNAME>, <PASSWORD>, enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 24;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid =<MID>;
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = <MID>;


-- PAYPAL MYR
INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'Paypal_MYR', 2, <MID>, <USERNAME>, <PASSWORD>, enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 24;

INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid = <MID>;
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = <MID>;


-- PAYPAL USD

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'Paypal_USD', 2, <MID>, <USERNAME>, <PASSWORD>, enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 24;

INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid = <MID>;
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = <MID>;


-- MODIRUM API
INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MODIRUM-MPI', 2, <MID>, <USERNAME>, <PASSWORD>, enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 47;

INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid = <MID>;
INSERT INTO client.routecurrency_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid = <MID>;

-- FIRST DATA
INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'first-data', 2, <MID>, <USERNAME>, <PASSWORD>, enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 62;

INSERT INTO client.routefeature_tbl( clientid, routeconfigid, featureid) SELECT 10077, rc.id, 9 FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and  mid = <MID>;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid =<MID>;
INSERT INTO client.routecurrency_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid =<MID>;

-- WorldPay
INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'worldpay_JPY', 2, <MID>, <USERNAME>, <PASSWORD>,  enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 4;

INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid =<MID>;
INSERT INTO client.routecurrency_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid =<MID>;

-- GrabPay
INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'GrabPay', 2, <MID>, <USERNAME>, <PASSWORD>,  enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 67;

INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid =<MID>;
INSERT INTO client.routecurrency_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid =<MID>;


-- Stored Card:
INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'Stored Card', 2, <MID>, <USERNAME>, <PASSWORD>, enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 36;

INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid =<MID>;
INSERT INTO client.routecurrency_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid =<MID>;


-- CyberSourceAMEX
INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'CyberSourceAMEX', 2, <MID>, <USERNAME>, <PASSWORD>,  enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 63;

INSERT INTO client.routecountry_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = <MID>;
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = <MID>;

--- Client Country currency mapping

INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,630,36,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,601,48,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,501,96,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,202,124,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,634,144,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,609,156,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,100,208,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,614,344,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,603,356,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,505,360,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,616,392,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,632,410,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,604,414,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,636,446,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,638,458,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,502,554,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,416,578,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,640,608,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,606,634,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,608,682,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,642,702,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,649,704,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,101,752,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,136,756,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,644,764,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,602,784,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,422,826,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,200,840,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,646,901,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,409,978,true);