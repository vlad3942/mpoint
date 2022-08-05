----- AV CALLBACK ROLLBACK-----
UPDATE client.additionalproperty_tbl set value = 'true' where key = 'IS_LEGACY_CALLBACK_FLOW' AND externalid = 10101;


UPDATE client.client_property_tbl set value=true
WHERE clientid=10101 and propertyid= (select id from system.client_property_tbl  where name='IS_LEGACY_CALLBACK_FLOW');