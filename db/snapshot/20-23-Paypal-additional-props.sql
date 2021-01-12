----CMP-4810----
DELETE FROM client.additionalproperty_tbl WHERE id= (SELECT id FROM client.additionalproperty_tbl WHERE key = 'PAYPAL_ORDER_NUMBER_PREFIX' and externalid = 10077);

INSERT INTO client.additionalproperty_tbl ("key", value, enabled, externalid, "type", "scope") VALUES('PAYPAL_ORDER_NAME', 'orderid', true, 10077, 'client', 2);
INSERT INTO client.additionalproperty_tbl ("key", value, enabled, externalid, "type", "scope") VALUES('PAYPAL_ORDER_NAME_PREFIX', 'Cebu Pacific Air - ', true, 10077, 'client', 2);
INSERT INTO client.additionalproperty_tbl ("key", value, enabled, externalid, "type", "scope") VALUES('PAYPAL_ORDER_NUMBER', 'invoiceid', true, 10077, 'client', 2);
