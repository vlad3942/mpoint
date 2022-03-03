-- Merchant Onboarding - Migration of client configuration (CEBU)

insert into client.client_property_tbl (propertyid,value,clientid)
select distinct sp.id,ap.value,ap.externalid from client.additionalproperty_tbl ap
inner join system.client_property_tbl sp on ap.key=sp.name
where ap.externalid =10077 and ap."type" ='client' on conflict (propertyid, clientid) do  nothing ;
