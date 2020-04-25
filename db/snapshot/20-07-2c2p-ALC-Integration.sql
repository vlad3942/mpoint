-- PAL 2C2P-ALC MID's-- [Please change clientid and Currency as per your environment and merchant requirement]
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.<CUR>','<CUR>NMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=40;

--PAL sample queries, please add as many currencies are to be supported for PAL --

INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.THB','THBNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.PHP','PHPNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.USD','USDNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=40;


-- CEBU 2C2P-ALC MID's-- [Please change clientid and Currency as per your environment and merchant requirement]
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.<CUR>','CebuPacific_<CUR>','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=40;

--CEBU Phase 1 queries--

INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.PHP','CebuPacific_PHP','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10077 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.USD','CebuPacific_USD','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10077 AND pspid=40;