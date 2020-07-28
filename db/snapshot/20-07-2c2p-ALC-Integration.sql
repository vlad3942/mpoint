-- PAL 2C2P-ALC MID's-- [Please change clientid and Currency as per your environment and merchant requirement]
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type,scope) SELECT 'mid.<CUR>','<CUR>NMA','t', id, 'merchant',2 FROM client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=40;

--PAL sample queries, please add as many currencies are to be supported for PAL --

--delete any old entries if present for PAL
delete from client.additionalproperty_tbl where key like 'mid.%' and externalid = (select id from client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=40);

INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type,scope) SELECT 'mid.THB','THBNMA','t', id, 'merchant',2 FROM client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type,scope) SELECT 'mid.PHP','PHPNMA','t', id, 'merchant',2 FROM client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type,scope) SELECT 'mid.USD','USDNMA','t', id, 'merchant',2 FROM client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=40;


-- CEBU 2C2P-ALC MID's-- [Please change clientid and Currency as per your environment and merchant requirement]
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type,scope) SELECT 'mid.<CUR>','CebuPacific_<CUR>','t', id, 'merchant',2 FROM client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=40;

--CEBU Phase 1 queries--

INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type,scope) SELECT 'mid.PHP','CebuPacific_MCC','t', id, 'merchant',2 FROM client.merchantaccount_tbl WHERE clientid=10077 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type,scope) SELECT 'mid.USD','CebuPacific_USD','t', id, 'merchant',2 FROM client.merchantaccount_tbl WHERE clientid=10077 AND pspid=40;


INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('TIMEZONE', 'Asia/Kuala_Lumpur', true, 10077, 'client', 2);

--CMP-4185
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) SELECT 'invoiceidrule', 'invoiceid ::= (transaction.@id)', true, id, 'merchant', 0 FROM client.merchantaccount_tbl WHERE clientid=10077 AND pspid=40;