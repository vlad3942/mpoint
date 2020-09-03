ALTER TABLE log.address_tbl add mobile_country_id varchar(4) null;
ALTER TABLE log.address_tbl add mobile varchar(15) null;
ALTER TABLE log.address_tbl add email varchar(50) null;

-- CMP-4323
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('PAYPAL_ORDER_NUMBER_PREFIX', 'Cebu Pacific Air - ', true, 10077, 'client', 2);