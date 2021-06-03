insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('CYBS_INSTALL','true',<CYBS-merchant-id>,'merchant',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('FareBasisCode','BK',<WP-merchant-id>,'merchant',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('IssuerAddress1','AV address',<WP-merchant-id>,'merchant',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('IssuerCity','Bogata address',<WP-merchant-id>,'merchant',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('IssuerCountryCode','12970',<WP-merchant-id>,'merchant',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('IssuerPostalCode','1200',<WP-merchant-id>,'merchant',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('RestrictedTicket','1',<WP-merchant-id>,'merchant',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('TravelAgencyCode','av',<WP-merchant-id>,'merchant',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('TravelAgencyName','Avinca',<WP-merchant-id>,'merchant',2);

insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('AIRLINE_NUMRIC_CODE',134,<client-id>,'client',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('CARRIER_NAME','AVINCA',<client-id>,'client',2);

insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('DFP_GEN','true',<client-id>,'client',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('CYBS_DM_ORGID','1snn5n9w',<client-id>,'client',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('CYBS_DM_MID','avianca_master',<client-id>,'client',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('ADOBE_TARGET_SCRIPT','true',<client-id>,'client',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('ADOBE_TARGET_SCRIPT_PATH','6ac3e976c146/92ff2d2716e2/launch-9a08edba7641-staging.min.js',<client-id>,'client',2);

insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('INGENICO_AUTH_MODE','SALE',<route-id>,'merchant',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('CYBS_MerchantDescriptor','Avianca S.A.',<CYBS-fraud-route-id>,'merchant',2);


-- Update from 'AVINCA' to 'Avianca'
update client.additionalproperty_tbl set value = 'Avianca' where key = 'CARRIER_NAME' and externalid = 10101;

-- Update from '1' to '0'
update client.additionalproperty_tbl set value='0' where key = 'RestrictedTicket' and type = 'merchant' and scope = 2 and externalid in (select id from client.route_tbl where clientid = 10101 and providerid = 4);

-- Update from 'av' to '134'
update client.additionalproperty_tbl set value='134' where key = 'TravelAgencyCode' and type = 'merchant' and scope = 2 and externalid in  (select id from client.route_tbl where clientid = 10101 and providerid = 4);

-- Update from 'Avinca' to 'Avianca'
update client.additionalproperty_tbl set value='Avianca' where key = 'TravelAgencyName' and type = 'merchant' and scope = 2 and externalid in  (select id from client.route_tbl where clientid = 10101 and providerid = 4);

-- Update from '12970' to 'CO'
update client.additionalproperty_tbl set value='CO' where key = 'IssuerCountryCode' and type = 'merchant' and scope = 2 and externalid in  (select id from client.route_tbl where clientid = 10101 and providerid = 4);

--  Add below properties for AV-CYBS
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('FareBasisCode','BK',<CYBS-merchant-id>,'merchant',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('IssuerAddress1','AV address',<CYBS-merchant-id>,'merchant',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('IssuerCity','Bogata address',<CYBS-merchant-id>,'merchant',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('IssuerCountryCode','CO',<CYBS-merchant-id>,'merchant',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('IssuerPostalCode','1200',<CYBS-merchant-id>,'merchant',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('RestrictedTicket','0',<CYBS-merchant-id>,'merchant',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('TravelAgencyCode','134',<CYBS-merchant-id>,'merchant',2);
insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('TravelAgencyName','Avianca',<CYBS-merchant-id>,'merchant',2);
