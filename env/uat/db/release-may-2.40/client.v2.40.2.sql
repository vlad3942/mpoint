<<<<<<< HEAD
-- VA Shorten Carrier-Name
UPDATE client.client_property_tbl SET value='Virgin Atlantic Holidays' WHERE propertyid=12 and clientid=10106;
UPDATE client.client_property_tbl SET value='Virgin Atlantic Holidays' WHERE propertyid=12 and clientid=10107;
UPDATE client.additionalproperty_tbl SET value='Virgin Atlantic Holidays' WHERE externalid=10106 and key='CARRIER_NAME';
UPDATE client.additionalproperty_tbl SET value='Virgin Atlantic Holidays' WHERE externalid=10107 and key='CARRIER_NAME';
=======
-- VA
INSERT INTO client.client_property_tbl (clientid, propertyid, value, enabled) VALUES (10106, 68, '4722', true);
INSERT INTO client.client_property_tbl (clientid, propertyid, value, enabled) VALUES (10107, 68, '4722', true);
>>>>>>> d029bf00e5ec6bdccb7a4b319617ad21d0bc6829
