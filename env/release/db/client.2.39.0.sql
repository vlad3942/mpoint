-- VA
insert into client.client_property_tbl (propertyid,value,clientid)
select id, true, 10106 from system.client_property_tbl where name = 'binsearch_required';

insert into client.client_property_tbl (propertyid,value,clientid)
select id, true, 10107 from system.client_property_tbl where name = 'binsearch_required';
