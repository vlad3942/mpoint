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

-- VA
insert into client.client_property_tbl (propertyid,value,clientid) select id, true, 10106 from system.client_property_tbl where name = 'binsearch_required';
insert into client.client_property_tbl (propertyid,value,clientid) select id, true, 10107 from system.client_property_tbl where name = 'binsearch_required';

-- AVIANCA scripts to enable CMD callback flow
DELETE FROM client.additionalproperty_tbl WHERE externalid=10101 and key='IS_LEGACY_CALLBACK_FLOW';
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('IS_LEGACY_CALLBACK_FLOW', 'false', true, 10101, 'client', 2);
UPDATE client.client_property_tbl set value=false WHERE clientid=10101 and propertyid= (select id from system.client_property_tbl  where name='IS_LEGACY_CALLBACK_FLOW');