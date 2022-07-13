-- Need to execute these routeconfig queries on Test environment only. For Prod - Need to check MID, Username and Password with BA

---- routeconfig_tbl queries ----
INSERT INTO client.routeconfig_tbl(routeid, name, capturetype, mid, username, password, enabled) SELECT id, 'CYBS_PHP', 2, '200035681', '200035681', 'yXT1vWdfXw17XlrpXvQfKmW3aJtqVPUT6rF+o/VRDbFB9L4mDpMBd1jqYrDc/NWUwFoOAZ4SN+r1neNB8so9MpPEvrORRM9z/jZKyMpeY4NmC83vcrfF87n/a6BK9fJ6caTaBCGOrzJTIWy4m10ZGcmAjnDlIy1g89g48vwc2pxEd3Hwgiy022cKlme/m0K0USJYmH+HjEw4Dve15zqAQg7t7UbZupbOO/ipOAQ5AIROezz70pM1qK2ZcoY4ceJ1ihIqjctDfwaeXm5AinKLXt511dp9bxNdpc6j6rOgq1iVGHyTVOawNDFdHuGX3cs6X8j6Xla1nTT+Vg+EQkseYg==', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 63;
INSERT INTO client.routeconfig_tbl(routeid, name, capturetype, mid, username, password, enabled) SELECT id, 'CYBS_Others', 2, '200035682', '200035682', '5Yw4h+6cBrDzxI6gjz6UeGD+8vktZHQDnKLkC6vCv+374uXnLTLpbfBOk9sg+sUpW0dEw/35Wm2iRWi3+DGl2yZHuC4yQWDkvil6701HUWZIRD3TFZcHyEys4LINtPWP/cli63EbgmbtbM6HIFnBx7O3XuuFnQas6XrTKxvYEXvzEMhw8LGn26t6fkoYknyqfg14TSjDNE1LrkQHWcm7bK4XgpxflcQfPzEg3CBfH3QtJ2A+Hpc5wV4+jmBxPCv54qE5Nv3moIfcHsyjVEEda+2QJt9hJbHm4Zi5D8cdlejtTHh7d7yI0zEPhSlO9Cm6LRpY41OmPkHuStuPAga+Ng==', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 63;

---- routecountry_tbl queries ----
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='200035681' and capturetype = 2;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='200035682' and capturetype = 2;

---- routecurrency_tbl queries ----
INSERT INTO client.routecurrency_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='200035681' and capturetype = 2;
INSERT INTO client.routecurrency_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='200035682' and capturetype = 2;

-- Need to add JCB in payment mode ---
INSERT INTO client.mpi_config_tbl (clientid, pmid, providerid, enabled) VALUES(10077, 5, 47, true);
INSERT INTO client.pm_tbl (clientid, pmid, enabled) VALUES(10077, 5, true);

--Need to add MPI in routefeature_tbl--
INSERT INTO client.routefeature_tbl (routeconfigid,clientid,featureid) select id,10077,20 from client.routeconfig_tbl where routeid in ( select id from client.route_tbl where clientid=10077 and providerid in (63)) and mid='200035681';
INSERT INTO client.routefeature_tbl (routeconfigid,clientid,featureid) select id,10077,20 from client.routeconfig_tbl where routeid in ( select id from client.route_tbl where clientid=10077 and providerid in (63)) and mid='200035682';

----MPI Rule----
INSERT INTO client.psp_property_tbl(clientid, propertyid, value, enabled)
VALUES(10077,(select id from "system".psp_property_tbl ppt where pspid = 63 and "name"='mpi_rule') , 'isProceedAuth::=<status>=="2"OR<status>=="5"OR<status>=="6"
status::=(additional-data.param[@name=''status''])', true);

----Post Fraud Rule----
INSERT INTO client.psp_property_tbl
(clientid, propertyid, value, enabled)
VALUES(10077,(select id from "system".psp_property_tbl ppt where pspid = 63 and "name"='post_fraud_rule'), 'isPostFraudAttemp::=<status>=="1"OR<status>=="4"OR<tempRule>
status::=(card.info-3d-secure.additional-data.param[@name=''status''])
tempRule::=(transaction.@type)=="5"OR(transaction.@type)=="3"])', true);

---- providerpm queries ----
INSERT INTO client.providerpm_tbl (pmid, routeid, enabled) VALUES(7,(select id from client.route_tbl rt where providerid = 63 and clientid = 10077), true);
INSERT INTO client.providerpm_tbl (pmid, routeid, enabled) VALUES(8,(select id from client.route_tbl rt where providerid = 63 and clientid = 10077), true);
INSERT INTO client.providerpm_tbl (pmid, routeid, enabled) VALUES(5,(select id from client.route_tbl rt where providerid = 63 and clientid = 10077), true);

---- routepm queries ----
INSERT INTO client.routepm_tbl (routeconfigid, pmid, enabled) VALUES((select id from client.routeconfig_tbl rt where name='CYBS_PHP'), 5, true);
INSERT INTO client.routepm_tbl (routeconfigid, pmid, enabled) VALUES((select id from client.routeconfig_tbl rt where name='CYBS_Others'), 5, true);
INSERT INTO client.routepm_tbl (routeconfigid, pmid, enabled) VALUES((select id from client.routeconfig_tbl rt where name='CYBS_PHP'), 7, true);
INSERT INTO client.routepm_tbl (routeconfigid, pmid, enabled) VALUES((select id from client.routeconfig_tbl rt where name='CYBS_Others'), 7, true);
INSERT INTO client.routepm_tbl (routeconfigid, pmid, enabled) VALUES((select id from client.routeconfig_tbl rt where name='CYBS_PHP'), 8, true);
INSERT INTO client.routepm_tbl (routeconfigid, pmid, enabled) VALUES((select id from client.routeconfig_tbl rt where name='CYBS_Others'), 8, true);