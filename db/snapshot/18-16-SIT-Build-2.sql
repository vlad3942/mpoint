/*=================== Web session timeout in minutes : START =======================*/
UPDATE client.additionalproperty_tbl SET value = 10 WHERE key ='webSessionTimeout' AND externalid = 10007
/*=================== Web session timeout in minutes : END =======================*/
-- NETS 3D Optimized Flow Url Configuration
INSERT INTO client.url_tbl ( urltypeid, clientid, url) VALUES(12, <CLIENTID>, '<MESB-URL>/mpoint/parse-3dsecure-challenge');
-- End NETS 3D Optimized Flow Url Configuration

-- Modirum 3D Optimized Flow Url Configuration
INSERT INTO client.url_tbl ( urltypeid, clientid, url) VALUES(12, <CLIENTID>, '<MESB-URL>/mpoint/parse-3dsecure-challenge');
-- End Modirum 3D Optimized Flow Url Configuration

