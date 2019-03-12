-- Set HPP Payment complete method Per client
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirect', 'POST', <clientId>, 'client', true);