
-- mPoint DB Scripts :

-- CMP-5444
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('DFP_GEN','true',<client-id>,'merchant',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('CYBS_DM_ORGID','k8vif92e',<client-id>,'client',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('CYBS_DM_MID','avianca_master',<client-id>,'client',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('ADOBE_TARGET_SCRIPT','true',<client-id>,'client',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('ADOBE_TARGET_SCRIPT_PATH','6ac3e976c146/92ff2d2716e2/launch-18e8855fefb3.min.js',<client-id>,'client',2);

--- script for legacy callback flow ----

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'IS_LEGACY_CALLBACK_FLOW', 'true',  true, id , 'client', 2 from client.client_tbl;