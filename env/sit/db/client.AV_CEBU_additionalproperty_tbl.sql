-- mPoint DB Scripts :

--Table Name : Client.Additionalproperty_Tbl

-- CMP-6298
UPDATE client.additionalproperty_tbl SET value = 'false' WHERE externalid = 10077 and key = 'enableHppAuthentication';

UPDATE client.additionalproperty_tbl SET value = 'false' WHERE externalid = 10101 and key = 'enableHppAuthentication';