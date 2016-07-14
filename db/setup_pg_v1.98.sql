UPDATE Client.Client_Tbl SET salt = 'Fh17_8sFgd' WHERE id = 10005;
UPDATE Client.Client_Tbl SET salt = 'Fh17_8sFgd' WHERE id = 10014;
UPDATE Client.Client_Tbl SET salt = '8sFgd_Fh17' WHERE id = 10019;

/**
 * Below queries are added to address CMP-848
 */
DELETE FROM system.pspcard_tbl WHERE cardid = 27 and pspid IN (SELECT id FROM system.pspcard_tbl WHERE id > 0);
INSERT INTO  system.pspcard_tbl(cardid, pspid)
SELECT 27, id
FROM system.psp_tbl WHERE id > 0;

DELETE FROM system.pspcard_tbl WHERE cardid = 25 and pspid IN (SELECT id FROM system.pspcard_tbl WHERE id > 0);
INSERT INTO  system.pspcard_tbl(cardid, pspid)
SELECT 25, id
FROM system.psp_tbl WHERE id > 0;

DELETE FROM system.pspcard_tbl WHERE cardid = 23 and pspid IN (SELECT id FROM system.pspcard_tbl WHERE id > 0);
INSERT INTO  system.pspcard_tbl(cardid, pspid)
SELECT 23, id
FROM system.psp_tbl WHERE id > 0;

DELETE FROM system.pspcard_tbl WHERE cardid = 16 and pspid IN (SELECT id FROM system.pspcard_tbl WHERE id > 0);
INSERT INTO  system.pspcard_tbl(cardid, pspid)
SELECT 16, id
FROM system.psp_tbl WHERE id > 0;