-- mPoint Sctipts

-- CMP-5799

INSERT INTO client.additionalproperty_tbl("key", value, modified, created, enabled, externalid, "type", "scope")VALUES('minPollingInterval', '5', now(), now(), true, 10077, 'client', 2);

INSERT INTO client.additionalproperty_tbl("key", value, modified, created, enabled, externalid, "type", "scope")VALUES('maxPollingInterval', '15', now(), now(), true, 10077, 'client', 2);