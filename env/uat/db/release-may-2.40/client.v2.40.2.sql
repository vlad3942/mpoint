-- VA Shorten Carrier-Name
UPDATE client.client_property_tbl SET value='Virgin Atlantic Holidays' WHERE propertyid=12 and clientid=10106;
UPDATE client.client_property_tbl SET value='Virgin Atlantic Holidays' WHERE propertyid=12 and clientid=10107;
UPDATE client.additionalproperty_tbl SET value='Virgin Atlantic Holidays' WHERE externalid=10106 and key='CARRIER_NAME';
UPDATE client.additionalproperty_tbl SET value='Virgin Atlantic Holidays' WHERE externalid=10107 and key='CARRIER_NAME';