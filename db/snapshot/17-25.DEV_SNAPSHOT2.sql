
--2C2P-ALC MID's-- [Please change clientid as per your environment]

INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.THB','NMATHB','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10007 AND pspid=40
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.PHP','NMAPHP','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10007 AND pspid=40
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.USD','NMAUSD','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10007 AND pspid=40