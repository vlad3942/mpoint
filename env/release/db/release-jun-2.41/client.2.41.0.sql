--CEBU CMD CALLBACK--
DELETE FROM client.additionalproperty_tbl WHERE externalid=10077 and key="IS_LEGACY_CALLBACK_FLOW";

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES
    ('IS_LEGACY_CALLBACK_FLOW', 'false', true, 10077, 'client', 2);

UPDATE client.client_property_tbl set value=false
WHERE clientid=10077 and propertyid= (select id from system.client_property_tbl  where name='IS_LEGACY_CALLBACK_FLOW');


--AV CMD CALLBACK--

DELETE FROM client.additionalproperty_tbl WHERE externalid=10101 and key='IS_LEGACY_CALLBACK_FLOW';

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES
    ('IS_LEGACY_CALLBACK_FLOW', 'false', true, 10101, 'client', 2);


UPDATE client.client_property_tbl set value=false
WHERE clientid=10101 and propertyid= (select id from system.client_property_tbl  where name='IS_LEGACY_CALLBACK_FLOW');