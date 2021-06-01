--- script for legacy callback flow ----

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'IS_LEGACY_CALLBACK_FLOW', 'true',  true, id , 'client', 2 from client.client_tbl;
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('CYBS_MerchantDescriptor','Avianca S.A.',<CYBS-fraud-route-id>,'merchant',2);

-- Update from 'AVINCA' to 'Avianca'
update client.additionalproperty_tbl set value = 'Avianca' where key = 'CARRIER_NAME' and externalid = 10101;

-- Update from '1' to '0'
update client.additionalproperty_tbl set value='0' where key = 'RestrictedTicket' and type = 'merchant' and scope = 2 and externalid in (select id from client.route_tbl where clientid = 10101 and providerid = 4);

-- Update from 'av' to '134'
update client.additionalproperty_tbl set value='134' where key = 'TravelAgencyCode' and type = 'merchant' and scope = 2 and externalid in  (select id from client.route_tbl where clientid = 10101 and providerid = 4);

-- Update from 'Avinca' to 'Avianca'
update client.additionalproperty_tbl set value='Avianca' where key = 'TravelAgencyName' and type = 'merchant' and scope = 2 and externalid in  (select id from client.route_tbl where clientid = 10101 and providerid = 4);


--  Add below properties for AV-CYBS
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('RestrictedTicket','0',<CYBS-merchant-id>,'merchant',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('TravelAgencyCode','134',<CYBS-merchant-id>,'merchant',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('TravelAgencyName','Avianca',<CYBS-merchant-id>,'merchant',2);