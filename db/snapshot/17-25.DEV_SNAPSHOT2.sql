
--2C2P-ALC MID's-- [Please change clientid as per your environment]

INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.THB','THBNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10007 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.PHP','PHPNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10007 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.USD','USDNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10007 AND pspid=40;