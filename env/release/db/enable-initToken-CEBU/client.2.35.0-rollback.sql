-- CMP-6533
UPDATE client.additionalproperty_tbl SET value = 'false', enabled = false WHERE externalid = 10077 and key = 'enableHppAuthentication';
UPDATE client.client_property_tbl SET value = 'false', enabled = false WHERE clientid = 10077 and propertyid = 53;



