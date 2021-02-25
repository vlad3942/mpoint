--- mPoint Client Schema - for SR Migration

------------------------------------------------------------------------------
-- Enable client to use SR flow i.e non legacy flow:

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('IS_LEGACY', 'false',10077, 'client', 2);


-- Migration of existing merchant route details
INSERT into client.route_tbl (id, clientid, providerid, enabled)
SELECT id, clientid, pspid, enabled FROM client.merchantaccount_tbl WHERE clientid=10077;


-- 2c2p-alc

	INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
    SELECT id, '2c2p-alc_Master_VISA_USD', 2, 'CebuPacific_USD', 'CELLPM', 'HC1XBPV0O4WLKZMG', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 40;
    
    INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
    SELECT id, '2c2p-alc_Master_VISA_PHP', 2, 'CebuPacific_MCC', 'CELLPM', 'HC1XBPV0O4WLKZMG', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 40;
    
    INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='CebuPacific_USD';
    
    INSERT INTO client.routecurrency_tbl(routeconfigid, currencyid) SELECT rc.id, 840 FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and  mid ='CebuPacific_USD';
    INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'CebuPacific_USD';
    
    INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='CebuPacific_MCC';
    
    INSERT INTO client.routecurrency_tbl(routeconfigid, currencyid) SELECT rc.id, 608 FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and  mid ='CebuPacific_MCC';

    INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'CebuPacific_MCC';



-- FirstData

    INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
    SELECT id, 'first-data_JCB_master_VISA', 2, '6160800000', 'WS6160800000._.1', 'tester01$', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 62;
    
    INSERT INTO client.routefeature_tbl( clientid, routeconfigid, featureid) SELECT 10077, rc.id, 9 FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and  mid ='6160800000';
    
    INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='6160800000';
    
    INSERT INTO client.routecurrency_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='6160800000';


  -- Paypal

  INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
    SELECT id, 'Paypal_USD', 2, 'Awzp6NMJcMOsM6SlpR13Cez-7vPFAMODxiW5ZT0qx6EbatLFSrMoBKtc', 'sb-43kvng1868465_api1.business.example.com', '37JT6WGJFFUJFRM3', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 24;
    
    INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='Awzp6NMJcMOsM6SlpR13Cez-7vPFAMODxiW5ZT0qx6EbatLFSrMoBKtc';
    
    INSERT INTO client.routecurrency_tbl(routeconfigid, currencyid) SELECT rc.id, 840 FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and  mid ='Awzp6NMJcMOsM6SlpR13Cez-7vPFAMODxiW5ZT0qx6EbatLFSrMoBKtc';
    INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'Awzp6NMJcMOsM6SlpR13Cez-7vPFAMODxiW5ZT0qx6EbatLFSrMoBKtc';
    
    INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
    SELECT id, 'Paypal_MYR', 2, 'AJXtiLishvWwk7Jm0EnHXPmvJv1xAqMsrFcJMX8Exsbl9aQ5NJRKPyem', 'sb-ivizq1858258_api1.business.example.com', 'VMXEJAT9DCLCR7LQ', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 24;
    
    INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='AJXtiLishvWwk7Jm0EnHXPmvJv1xAqMsrFcJMX8Exsbl9aQ5NJRKPyem';
    
    INSERT INTO client.routecurrency_tbl(routeconfigid, currencyid) SELECT rc.id, 458 FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and  mid ='AJXtiLishvWwk7Jm0EnHXPmvJv1xAqMsrFcJMX8Exsbl9aQ5NJRKPyem';

    INSERT INTO client.routecurrency_tbl(routeconfigid, currencyid) SELECT rc.id, 608 FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='AJXtiLishvWwk7Jm0EnHXPmvJv1xAqMsrFcJMX8Exsbl9aQ5NJRKPyem';

    INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'AJXtiLishvWwk7Jm0EnHXPmvJv1xAqMsrFcJMX8Exsbl9aQ5NJRKPyem';

    
    INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
    SELECT id, 'Paypal_HKD', 2, 'A78isDTCcuwhNZKyvzOsXXSzxdUPA25dTmhkEQxH7G1T4iQSksAr-SK0', 'sb-ph1ko1832308_api1.business.example.com', '5QBM4GMSFPV8AHNK', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 24;
    
    INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='A78isDTCcuwhNZKyvzOsXXSzxdUPA25dTmhkEQxH7G1T4iQSksAr-SK0';
    
    INSERT INTO client.routecurrency_tbl(routeconfigid, currencyid) SELECT rc.id, 344 FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and  mid ='A78isDTCcuwhNZKyvzOsXXSzxdUPA25dTmhkEQxH7G1T4iQSksAr-SK0';
    INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'A78isDTCcuwhNZKyvzOsXXSzxdUPA25dTmhkEQxH7G1T4iQSksAr-SK0';
    
    INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
    SELECT id, 'Paypal_SGD', 2, 'ATG2YLmUZFFk6n5kHVGwCC2A2dsDAEAdhFEakpPDRN5lxmGq3zimTG6A', 'sb-mohn91867880_api1.business.example.com', 'B9WX2HPY9DPD6284', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 24;
    
    INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='ATG2YLmUZFFk6n5kHVGwCC2A2dsDAEAdhFEakpPDRN5lxmGq3zimTG6A';
    
    INSERT INTO client.routecurrency_tbl(routeconfigid, currencyid) SELECT rc.id, 702 FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and  mid ='ATG2YLmUZFFk6n5kHVGwCC2A2dsDAEAdhFEakpPDRN5lxmGq3zimTG6A';

    INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'ATG2YLmUZFFk6n5kHVGwCC2A2dsDAEAdhFEakpPDRN5lxmGq3zimTG6A';
    
        
    INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
    SELECT id, 'Paypal_USD', 2, 'ACCu12.jik2Wb3kzHJgFE1palQEsAHD7wsynoTYqRcOZAX7RzupgW4sQ', 'sb-sahh431638744_api1.business.example.com', '7W56K2VQBRYF8FLX', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 24;
    
    INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='ACCu12.jik2Wb3kzHJgFE1palQEsAHD7wsynoTYqRcOZAX7RzupgW4sQ';
    
    INSERT INTO client.routecurrency_tbl(routeconfigid, currencyid) SELECT rc.id, 36 FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='ACCu12.jik2Wb3kzHJgFE1palQEsAHD7wsynoTYqRcOZAX7RzupgW4sQ';
    
    INSERT INTO client.routecurrency_tbl(routeconfigid, currencyid) SELECT rc.id, 96 FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='ACCu12.jik2Wb3kzHJgFE1palQEsAHD7wsynoTYqRcOZAX7RzupgW4sQ';
    
    INSERT INTO client.routecurrency_tbl(routeconfigid, currencyid) SELECT rc.id, 156 FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='ACCu12.jik2Wb3kzHJgFE1palQEsAHD7wsynoTYqRcOZAX7RzupgW4sQ';
    
    INSERT INTO client.routecurrency_tbl(routeconfigid, currencyid) SELECT rc.id, 360 FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='ACCu12.jik2Wb3kzHJgFE1palQEsAHD7wsynoTYqRcOZAX7RzupgW4sQ';
    
    INSERT INTO client.routecurrency_tbl(routeconfigid, currencyid) SELECT rc.id, 410 FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='ACCu12.jik2Wb3kzHJgFE1palQEsAHD7wsynoTYqRcOZAX7RzupgW4sQ';
    
    INSERT INTO client.routecurrency_tbl(routeconfigid, currencyid) SELECT rc.id, 446 FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='ACCu12.jik2Wb3kzHJgFE1palQEsAHD7wsynoTYqRcOZAX7RzupgW4sQ';
    
    INSERT INTO client.routecurrency_tbl(routeconfigid, currencyid) SELECT rc.id, 608 FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='ACCu12.jik2Wb3kzHJgFE1palQEsAHD7wsynoTYqRcOZAX7RzupgW4sQ';
    
    INSERT INTO client.routecurrency_tbl(routeconfigid, currencyid) SELECT rc.id, 764 FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='ACCu12.jik2Wb3kzHJgFE1palQEsAHD7wsynoTYqRcOZAX7RzupgW4sQ';
    
    INSERT INTO client.routecurrency_tbl(routeconfigid, currencyid) SELECT rc.id, 784 FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='ACCu12.jik2Wb3kzHJgFE1palQEsAHD7wsynoTYqRcOZAX7RzupgW4sQ';
    
    INSERT INTO client.routecurrency_tbl(routeconfigid, currencyid) SELECT rc.id, 901 FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='ACCu12.jik2Wb3kzHJgFE1palQEsAHD7wsynoTYqRcOZAX7RzupgW4sQ'; 


-- Worldpay

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'worldpay_Master_VISA_PHP', 2, 'CELLPOINT', 'CELLPOINT', 'Mesb@1234', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 4;
    
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='CELLPOINT';
    
INSERT INTO client.routecurrency_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='CELLPOINT';


-- Grab Pay

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'GrabPay', 2, 'dbb00e18-83ee-49cf-b54d-2707a069b3e4', '0112218e-dda0-4ca8-8489-65a3d28abd69', 'apWSvBQj_evmVfzY', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 67;
    
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='dbb00e18-83ee-49cf-b54d-2707a069b3e4';
    
INSERT INTO client.routecurrency_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='dbb00e18-83ee-49cf-b54d-2707a069b3e4';


-- Stored Card:

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'Stored Card', 2, 'mVault', 'Blank', 'Blank', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 36;
    
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mVault';
    
INSERT INTO client.routecurrency_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mVault';



-- Pay MAYA :


INSERT into client.route_tbl (id, clientid, providerid) values (23, 10077, 68);

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled) SELECT id, 'payMaya', 2, 'paymaya', 'pk-MOfNKu3FmHMVHtjyjG7vhr7vFevRkWxmxYL1Yq6iFk5', 'sk-NMda607FeZNGRt9xCdsIRiZ4Lqu6LT898ItHbN4qPSe', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 68;

INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='paymaya';

INSERT INTO client.routecurrency_tbl(routeconfigid, currencyid) SELECT rc.id, 608 FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='paymaya';

INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'paymaya';

-- CyberSourceAMEX

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'CyberSourceAMEX', 2, 'CyberSourceAMEX', 'cebu_cellpoint_test', 'K/B7APZOVPoPCFvSIyqMpvUmeDCAyyd0aWXnIHFQqBnSBwc1PDXRVZCS8DazLnCSXZUuauffLNY0lxJpoR8/e94VJbzKVK+Dzxmhl3hkS0qnmk/ZJFcd2Huh80UK5qG2TwB2inqPacECAGBLk5steF6UlALDYuMOvJuVinUW84VEpxUJ1Dntmm4AhNpB2pUheytX4XjhoodDerjGZGg61Ps4xHxqNl29huaumNYIoCfGNchX5vkKi8uBoPwJCpbBO0ORUy9sgMQOk1w7DTNVSCvkpbF+LH3VdFV/3N8kU9z/ONKLF2zPq5aWjC861EjQo1mAqiZBjg8Afof3CsDQ0Q==', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 63;

INSERT INTO client.routecountry_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'CyberSourceAMEX';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'CyberSourceAMEX';


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