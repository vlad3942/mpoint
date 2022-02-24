-- CMP-6298
UPDATE client.additionalproperty_tbl SET value = 'false', enabled = false WHERE externalid = 10077 and key = 'enableHppAuthentication';
UPDATE client.additionalproperty_tbl SET value = 'false', enabled = false WHERE externalid = 10101 and key = 'enableHppAuthentication';


-- CMP-6298
-- Added additional SQL due to data migraion in new tables
UPDATE client.client_property_tbl SET value = 'true', enabled = false WHERE clientid = 10077 and propertyid = 53;
UPDATE client.client_property_tbl SET value = 'true', enabled = false WHERE clientid = 10101 and propertyid = 53;

-- Merchant Onboarding - Migration of client configuration (CEBU)

insert into client.client_property_tbl (propertyid,value,clientid)
select distinct sp.id,ap.value,ap.externalid from client.additionalproperty_tbl ap
inner join system.client_property_tbl sp on ap.key=sp.name
where ap.externalid =10077 and ap."type" ='client' on conflict (propertyid, clientid) do  nothing ;

