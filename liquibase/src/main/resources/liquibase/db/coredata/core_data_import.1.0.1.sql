--from setup_pg_CRS_v2.22.sql
-- Add new transaction types
INSERT INTO System.Type_Tbl (id, name) VALUES (1, 'Shopping Online');
INSERT INTO System.Type_Tbl (id, name) VALUES (2, 'Shopping Offline');
INSERT INTO System.Type_Tbl (id, name) VALUES (3, 'Self Service Online');
INSERT INTO System.Type_Tbl (id, name) VALUES (4, 'Self Service Offline	');