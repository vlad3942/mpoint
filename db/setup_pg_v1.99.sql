UPDATE Client.Client_Tbl SET salt = 'Fh17_8sFgd' WHERE id = 10005;
UPDATE Client.Client_Tbl SET salt = 'Fh17_8sFgd' WHERE id = 10014;
UPDATE Client.Client_Tbl SET salt = '8sFgd_Fh17' WHERE id = 10019;

/**
 * CMP-917
 */
INSERT INTO System.Type_Tbl (id, name) VALUES (10091, 'New Card Purchase');
/**
 * CMP-999
 */
UPDATE system.country_tbl SET symbol='Kr.' WHERE id = 100;