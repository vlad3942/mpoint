-- mPoint DB Scripts:  
-- AVPOP-780
UPDATE client.client_property_tbl SET value = '45ssiuz3' where propertyid = (select id from system.client_property_tbl where name = 'CYBS_DM_ORGID') and clientid = 10101;