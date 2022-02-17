-- CMP-6433
-- mPoint script for CEBU for turning on CMD callback for UAT environment

-- Note : Below queries to be executed only on UAT and not on production until client confirmation

DELETE FROM client.additionalproperty_tbl WHERE externalid=10077 and key='IS_LEGACY_CALLBACK_FLOW';

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES
    ('IS_LEGACY_CALLBACK_FLOW', 'false', true, 10077, 'client', 2);

UPDATE client.client_property_tbl set value=false
WHERE clientid=10077 and propertyid= (select id from system.client_property_tbl  where name='IS_LEGACY_CALLBACK_FLOW');