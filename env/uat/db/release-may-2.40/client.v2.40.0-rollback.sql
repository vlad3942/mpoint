-- VA
DELETE FROM client.client_property_tbl WHERE clientid = 10106 AND propertyid = (SELECT id FROM system.client_property_tbl where name = 'binsearch_required');
DELETE FROM client.client_property_tbl WHERE clientid = 10107 AND propertyid = (SELECT id FROM system.client_property_tbl where name = 'binsearch_required');

-- AVIANCA scripts to enable CMD callback flow
DELETE FROM client.additionalproperty_tbl WHERE externalid=10101 and key='IS_LEGACY_CALLBACK_FLOW';
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('IS_LEGACY_CALLBACK_FLOW', 'true', true, 10101, 'client', 2);
UPDATE client.client_property_tbl set value=true WHERE clientid=10101 and propertyid=(select id from system.client_property_tbl  where name='IS_LEGACY_CALLBACK_FLOW');