
/**********client.additionalproperty_tbl For DEV/SIT/UAT************/

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES( 'SSO_PREFERENCE', <SSO_PREFERENCE_VALUE>,true,  <CLIENT-ID> ,'client',2)


INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES( 'SSO_PREFERENCE', 'LOOSE',true,  10077,'client',2)


INSERT INTO log.state_tbl(id, name, module, func,enabled) VALUES (211, 'Auth token or SSO token not received', 'verification', 'verify', true);

INSERT INTO log.state_tbl(id, name, module, func,enabled) VALUES (212, 'Mandatory fields are missing', 'verification', 'verify', true);

INSERT INTO log.state_tbl(id, name, module, func,enabled) VALUES (213, 'Profile authentication failed', 'verification', 'verify', true);