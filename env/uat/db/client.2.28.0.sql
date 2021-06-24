
-- mPoint DB Scripts :

-- CMP-5664------
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES('IS_STORE_BILLING_ADDRS', 'true', 10101, 'client', true, 0);

-- Insert the Property for CYBS
INSERT INTO client.additionalproperty_tbl ( key, value, externalid, type, enabled, scope) VALUES ('CYBS_MerchantDescriptor', 'Avianca S.A.',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10101 and pspid = 63) ,'merchant', true, 1);

-- CMP-5444 ----
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('DFP_GEN','true',10101,'client',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('CYBS_DM_ORGID','1snn5n9w',10101,'client',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('CYBS_DM_MID','avianca_master',10101,'client',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('ADOBE_TARGET_SCRIPT','true',10101,'client',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('ADOBE_TARGET_SCRIPT_PATH','6ac3e976c146/92ff2d2716e2/launch-9a08edba7641-staging.min.js',10101,'client',2);


-- CMP-5616 - Corrected existing properties for AV-WP
update client.additionalproperty_tbl set value = 'Avianca' where key = 'CARRIER_NAME' and externalid = 10101;
update client.additionalproperty_tbl set value='0', scope=1 where key = 'RestrictedTicket' and type = 'merchant' and scope = 2 and externalid in (select id from client.route_tbl where clientid = 10101 and providerid = 4);
update client.additionalproperty_tbl set value='134', scope=1  where key = 'TravelAgencyCode' and type = 'merchant' and scope = 2 and externalid in  (select id from client.route_tbl where clientid = 10101 and providerid = 4);
update client.additionalproperty_tbl set value='Avianca', scope=1 where key = 'TravelAgencyName' and type = 'merchant' and scope = 2 and externalid in  (select id from client.route_tbl where clientid = 10101 and providerid = 4);
update client.additionalproperty_tbl set value='CO', scope=1 where key = 'IssuerCountryCode' and type = 'merchant' and scope = 2 and externalid in  (select id from client.route_tbl where clientid = 10101 and providerid = 4);
update client.additionalproperty_tbl set scope=1 where key = 'FareBasisCode' and type = 'merchant' and scope = 2 and externalid in (select id from client.route_tbl where clientid = 10101 and providerid = 4);
update client.additionalproperty_tbl set scope=1, value='AVENIDA CALLE 26 No. 59-15' where key = 'IssuerAddress1' and type = 'merchant' and scope = 2 and externalid in (select id from client.route_tbl where clientid = 10101 and providerid = 4);
update client.additionalproperty_tbl set scope=1, value='Bogota' where key = 'IssuerCity' and type = 'merchant' and scope = 2 and externalid in (select id from client.route_tbl where clientid = 10101 and providerid = 4);