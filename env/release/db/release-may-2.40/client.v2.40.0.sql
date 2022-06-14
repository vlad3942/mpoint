-- CMP-6317
insert into client.routefeature_tbl (clientid, routeconfigid, featureid)
select mt.clientid, rt2.id, 4
from client.merchantaccount_tbl mt
         join client.route_tbl rt on rt.clientid = mt.clientid and rt.providerid = mt.pspid
         join client.routeconfig_tbl rt2 on rt2.routeid = rt.id
where  supportedpartialoperations > 0 and supportedpartialoperations%2 = 0 and mt.clientid  in (10077,10101) and mt.enabled = true and rt.enabled = true and rt2.enabled = true
union
select mt.clientid, rt2.id, 6
from client.merchantaccount_tbl mt
         join client.route_tbl rt on rt.clientid = mt.clientid and rt.providerid = mt.pspid
         join client.routeconfig_tbl rt2 on rt2.routeid = rt.id
where supportedpartialoperations > 0 and  supportedpartialoperations%3 = 0 and mt.clientid  in (10077,10101) and mt.enabled = true and rt.enabled = true and rt2.enabled = true
union
select mt.clientid, rt2.id, 19
from client.merchantaccount_tbl mt
         join client.route_tbl rt on rt.clientid = mt.clientid and rt.providerid = mt.pspid
         join client.routeconfig_tbl rt2 on rt2.routeid = rt.id
where supportedpartialoperations > 0 and supportedpartialoperations%5 = 0 and mt.clientid  in (10077,10101) and mt.enabled = true and rt.enabled = true and rt2.enabled = true;

-- Client propert fingerprint enchancment --
UPDATE client.client_property_tbl SET value = '9ozphlqx' where propertyid = (select id from system.client_property_tbl where name = 'CYBS_DM_ORGID') and clientid = 10101;