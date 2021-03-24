-- mPoint DB Scripts :

--Table Name : Client.Additionalproperty_Tbl

-- CMP-5212
UPDATE client.additionalproperty_tbl SET value = 13 WHERE externalid = 10077 and key = 'webSessionTimeout';