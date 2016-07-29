UPDATE Client.Client_Tbl SET salt = 'Fh17_8sFgd' WHERE id = 10005;
UPDATE Client.Client_Tbl SET salt = 'Fh17_8sFgd' WHERE id = 10014;
UPDATE Client.Client_Tbl SET salt = '8sFgd_Fh17' WHERE id = 10019;

/* ========== Mobile Optimized 3D Secure BEGIN ========== */
INSERT INTO System.URLType_Tbl (id, name) VALUES (12, 'Parse 3D Secure Challenge URL');
INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (12, 10005, 'http://dsb.mesb.cellpointmobile.com:10080/mpoint/parse-3dsecure-challenge');
INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (12, 10014, 'http://dsb.mesb.test.cellpointmobile.com:10080/mpoint/parse-3dsecure-challenge');
/* ========== Mobile Optimized 3D Secure END ========== */
