-- CMP-6533
UPDATE client.additionalproperty_tbl SET value = 'true', enabled = true WHERE externalid = 10077 and key = 'enableHppAuthentication';
UPDATE client.client_property_tbl SET value = 'true', enabled = true WHERE clientid = 10077 and propertyid = 53;



