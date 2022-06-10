-- VA
DELETE FROM client.client_property_tbl WHERE clientid = 10106 AND propertyid = (SELECT id FROM system.client_property_tbl where name = 'binsearch_required');
DELETE FROM client.client_property_tbl WHERE clientid = 10107 AND propertyid = (SELECT id FROM system.client_property_tbl where name = 'binsearch_required');

-- Client propert fingerprint enchancment --
UPDATE client.client_property_tbl SET value = 'k8vif92e' where propertyid = (select id from system.client_property_tbl where name = 'CYBS_DM_ORGID') and clientid = 10101;
