--- script for legacy callback flow ----

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'IS_LEGACY_CALLBACK_FLOW', 'true',  true, id , 'client', 2 from client.client_tbl;
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('CYBS_MerchantDescriptor','Avianca S.A.',<CYBS-fraud-route-id>,'merchant',2);

-- CMP-5616 - Corrected existing properties for AV-WP
update client.additionalproperty_tbl set value = 'Avianca' where key = 'CARRIER_NAME' and externalid = 10101;
update client.additionalproperty_tbl set value='0', scope=1 where key = 'RestrictedTicket' and type = 'merchant' and scope = 2 and externalid in (select id from client.route_tbl where clientid = 10101 and providerid = 4);
update client.additionalproperty_tbl set value='134', scope=1  where key = 'TravelAgencyCode' and type = 'merchant' and scope = 2 and externalid in  (select id from client.route_tbl where clientid = 10101 and providerid = 4);
update client.additionalproperty_tbl set value='Avianca', scope=1 where key = 'TravelAgencyName' and type = 'merchant' and scope = 2 and externalid in  (select id from client.route_tbl where clientid = 10101 and providerid = 4);
update client.additionalproperty_tbl set value='CO', scope=1 where key = 'IssuerCountryCode' and type = 'merchant' and scope = 2 and externalid in  (select id from client.route_tbl where clientid = 10101 and providerid = 4);
update client.additionalproperty_tbl set scope=1 where key = 'FareBasisCode' and type = 'merchant' and scope = 2 and externalid in (select id from client.route_tbl where clientid = 10101 and providerid = 4);
update client.additionalproperty_tbl set scope=1 where key = 'IssuerAddress1' and type = 'merchant' and scope = 2 and externalid in (select id from client.route_tbl where clientid = 10101 and providerid = 4);
update client.additionalproperty_tbl set scope=1 where key = 'IssuerCity' and type = 'merchant' and scope = 2 and externalid in (select id from client.route_tbl where clientid = 10101 and providerid = 4);
update client.additionalproperty_tbl set scope=1 where key = 'IssuerPostalCode' and type = 'merchant' and scope = 2 and externalid in (select id from client.route_tbl where clientid = 10101 and providerid = 4);

-- CMP-5616 - Added new properties for AV-CYBS
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('FareBasisCode','BK',<CYBS-merchant-id>,'merchant',1);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('IssuerAddress1','AV address',<CYBS-merchant-id>,'merchant',1);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('IssuerCity','Bogata address',<CYBS-merchant-id>,'merchant',1);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('IssuerCountryCode','CO',<CYBS-merchant-id>,'merchant',1);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('IssuerPostalCode','1200',<CYBS-merchant-id>,'merchant',1);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('RestrictedTicket','0',<CYBS-merchant-id>,'merchant',1);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('TravelAgencyCode','134',<CYBS-merchant-id>,'merchant',1);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('TravelAgencyName','Avianca',<CYBS-merchant-id>,'merchant',1);