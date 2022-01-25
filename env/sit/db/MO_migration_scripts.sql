
insert into client.services_tbl (clientid,legacy_flow_enabled) select id,true from client.client_tbl ON CONFLICT (clientid) DO NOTHING;

UPDATE client.services_tbl set dcc_enabled=true,pcc_enabled=true,fraud_enabled=true,splitpayment_enabled=true,mpi_enabled=true,legacy_flow_enabled=false
WHERE clientid=10077;

UPDATE client.services_tbl set fraud_enabled=true,splitpayment_enabled=true,mpi_enabled=true,legacy_flow_enabled=false WHERE clientid=10101;

insert into client.pm_tbl (pmid,clientid) select distinct cardid,clientid from client.cardaccess_tbl where psp_type in (1,2,3,4,7,11) and clientid in (10077,10101) ON CONFLICT (pmid,clientid) DO NOTHING;

-- Merchant Onboarding - Migration of client configuration (CEBU)

-- Fraud Config
insert into client.fraud_config_tbl (clientid, pmid, providerid, countryid, currencyid, typeoffraud, enabled)
select  clientid, CA.cardid as pmid, pspid as providerid, COALESCE(countryid,0) as countryid , 0 as currencyid,
        case when psp_type = 9 then 1
             when psp_type = 10 then 2
             else 0
            end typeoffraud,
        true as enabled
from client.cardaccess_tbl CA
where CA.clientid = 10077 and psp_type in (9,10) and enabled = true on conflict (pmid, clientid, countryid, currencyid, typeoffraud) do  nothing ;

-- DCC Config
insert into client.dcc_config_tbl (clientid, pmid, countryid, currencyid, enabled)
select distinct  clientid, CA.cardid as pmid, COALESCE(countryid,0) as countryid , 0 as currencyid,
                 true as enabled
from client.cardaccess_tbl CA
where CA.clientid = 10077 and psp_type not in (9,10,6) and  dccenabled =true and walletid is null  and enabled = true on conflict  (pmid, clientid, countryid, currencyid) do  nothing ;

-- Client Config
insert into client.client_property_tbl (propertyid,value,clientid)
select distinct sp.id,ap.value,ap.externalid from client.additionalproperty_tbl ap
                                                      inner join system.client_property_tbl sp on ap.key=sp.name
where ap.externalid =10077 and ap."type" ='client' on conflict (propertyid, clientid) do  nothing ;

-- PSP Config
insert into client.psp_property_tbl (propertyid,value,clientid)
select distinct sp.id,ap.value, rt.clientid from client.route_tbl rt
                                                     inner join client.additionalproperty_tbl ap on ap.externalid=rt.id
                                                     inner join system.psp_property_tbl sp on ap.key=sp.name
    and rt .providerid = sp.pspid
where ap."type" ='merchant' and rt.clientid =10077 and ap.enabled =true on conflict (propertyid, clientid) do  nothing;

-- Route Config
insert into client.route_property_tbl (propertyid,value,routeconfigid)
select distinct sp.id,ap.value, rc.id from client.route_tbl rt
                                               inner join client.additionalproperty_tbl ap on ap.externalid=rt.id
                                               inner join system.route_property_tbl sp on ap.key=sp.name and rt .providerid = sp.pspid
                                               inner join client.routeconfig_tbl rc on rt.id=rc.routeid
where ap."type" ='merchant' and rt.clientid =10077 and ap.enabled =true on conflict (propertyid, routeconfigid) do  nothing;

--------PSP Property which is wrongly added under client level
insert into client.psp_property_tbl (propertyid,value,clientid)
select sp.id,ap.value,ap.externalid from client.additionalproperty_tbl ap inner join system.psp_property_tbl sp on ap.key=sp.name
where ap.externalid =10077 and ap."type" ='client' on conflict  (propertyid, clientid) do  nothing;

-- Split Payment Config
INSERT INTO client.split_property_tbl
(clientid, is_rollback, is_reoffer, enabled)
VALUES(10077, true, false, true)
    ON CONFLICT (clientid) DO UPDATE
                                  SET is_reoffer = false,
                                  is_rollback = true;


-- Merchant Onboarding - Migration of client configuration (AVIANCA)

-- Fraud Config
insert into client.fraud_config_tbl (clientid, pmid, providerid, countryid, currencyid, typeoffraud, enabled)
select  clientid, CA.cardid as pmid, pspid as providerid, COALESCE(countryid,0) as countryid , 0 as currencyid,
        case when psp_type = 9 then 1
             when psp_type = 10 then 2
             else 0
            end typeoffraud,
        true as enabled
from client.cardaccess_tbl CA
where CA.clientid = 10101 and psp_type in (9,10) and enabled = true;

-- DCC Config
insert into client.dcc_config_tbl (clientid, pmid, countryid, currencyid, enabled)
select distinct  clientid, CA.cardid as pmid, COALESCE(countryid,0) as countryid , 0 as currencyid,
                 true as enabled
from client.cardaccess_tbl CA
where CA.clientid = 10101 and psp_type not in (9,10,6) and  dccenabled =true and walletid is null  and enabled = true;

-- Client Config
insert into client.client_property_tbl (propertyid,value,clientid)
select distinct sp.id,ap.value,ap.externalid from client.additionalproperty_tbl ap inner join system.client_property_tbl sp on ap.key=sp.name
where ap.externalid =10101 and ap."type" ='client';

-- PSP Config
insert into client.psp_property_tbl (propertyid,value,clientid)
select distinct sp.id,ap.value, rt.clientid from client.route_tbl rt
                                                     inner join client.additionalproperty_tbl ap on ap.externalid=rt.id
                                                     inner join system.psp_property_tbl sp on ap.key=sp.name
    and rt .providerid = sp.pspid
where ap."type" ='merchant' and rt.clientid =10101 and ap.enabled =true;

-- Route Config
insert into client.route_property_tbl (propertyid,value,routeconfigid)
select distinct sp.id,ap.value, rc.id from client.route_tbl rt
                                               inner join client.additionalproperty_tbl ap on ap.externalid=rt.id
                                               inner join system.route_property_tbl sp on ap.key=sp.name and rt .providerid = sp.pspid
                                               inner join client.routeconfig_tbl rc on rt.id=rc.routeid
where ap."type" ='merchant' and rt.clientid =10101 and ap.enabled =true;

--------PSP Property which is wrongly added under client level
insert into client.psp_property_tbl (propertyid,value,clientid)
select sp.id,ap.value,ap.externalid from client.additionalproperty_tbl ap inner join system.psp_property_tbl sp on ap.key=sp.name
where ap.externalid =10101 and ap."type" ='client'

-- Split Payment Config
    INSERT INTO client.split_property_tbl
(clientid, is_rollback, is_reoffer, enabled)
VALUES(10101, false, true, true)
ON CONFLICT (clientid) DO UPDATE
                              SET is_reoffer = true,
                              is_rollback = false;


--fraud for other client
insert into client.fraud_config_tbl (clientid, pmid, providerid, countryid, currencyid, typeoffraud, enabled)
select  clientid, CA.cardid as pmid, pspid as providerid, COALESCE(countryid,0) as countryid , 0 as currencyid,
        case when psp_type = 9 then 1
             when psp_type = 10 then 2
             else 0
            end typeoffraud,
        true as enabled
from client.cardaccess_tbl CA
where CA.clientid not in ( 10077,10101) and psp_type in (9,10) and enabled = true;

