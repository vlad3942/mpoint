
INSERT INTO Admin.Role_Tbl (id, name, assignable, note) VALUES (1, 'Administrator', false, 'A member of this role may create other users who have access to the same client accounts.');
INSERT INTO Admin.Role_Tbl (id, name, note) VALUES (2, 'Customer Service', 'A member of this role will have access to customer service features.');
INSERT INTO Admin.Role_Tbl (id, name, note) VALUES (3, 'Finance', 'A member of this role may access financial data and functions.');
INSERT INTO Admin.Role_Tbl (id, name, note) VALUES (4, 'Statistics', 'A member of this role may access statistical data and functions.');
INSERT INTO Admin.Role_Tbl (id, name, note) VALUES (5, 'Controller', 'A member of this role may access the mController module.');

INSERT INTO Admin.RoleInfo_Tbl (roleid, languageid, name, note) VALUES (1, 1, 'Administrator', 'A member of this role may create other users who have access to the same client accounts.');
INSERT INTO Admin.RoleInfo_Tbl (roleid, languageid, name, note) VALUES (1, 2, 'Administrator', 'A member of this role may create other users who have access to the same client accounts.');
INSERT INTO Admin.RoleInfo_Tbl (roleid, languageid, name, note) VALUES (1, 3, 'Administrator', 'Et medlem af denne rolle har adgang til at oprette brugere med samme klient.');
INSERT INTO Admin.RoleInfo_Tbl (roleid, languageid, name, note) VALUES (2, 1, 'Customer Service', 'A member of this role will have access to customer service features.');
INSERT INTO Admin.RoleInfo_Tbl (roleid, languageid, name, note) VALUES (2, 2, 'Customer Service', 'A member of this role will have access to customer service features.');
INSERT INTO Admin.RoleInfo_Tbl (roleid, languageid, name, note) VALUES (2, 3, 'Kundeservice', 'Et medlem af denne rolle vil have adgang til kundeservice funktioner.');
INSERT INTO Admin.RoleInfo_Tbl (roleid, languageid, name, note) VALUES (3, 1, 'Finance', 'A member of this role may access financial data and functions.');
INSERT INTO Admin.RoleInfo_Tbl (roleid, languageid, name, note) VALUES (3, 2, 'Finance', 'A member of this role may access financial data and functions.');
INSERT INTO Admin.RoleInfo_Tbl (roleid, languageid, name, note) VALUES (3, 3, 'Økonomi', 'Et medlem af denne rolle har adgang til financielle data og funktioner.');
INSERT INTO Admin.RoleInfo_Tbl (roleid, languageid, name, note) VALUES (4, 1, 'Statistics', 'A member of this role may access statistical data and functions.');
INSERT INTO Admin.RoleInfo_Tbl (roleid, languageid, name, note) VALUES (4, 2, 'Statistics', 'A member of this role may access statistical data and functions.');
INSERT INTO Admin.RoleInfo_Tbl (roleid, languageid, name, note) VALUES (4, 3, 'Statistik', 'Et medlem af denne rolle har adgang til statistiskdata og funktioner.');
INSERT INTO Admin.RoleInfo_Tbl (roleid, languageid, name, note) VALUES (5, 1, 'Controller', 'A member of this role may access the mController module.');
INSERT INTO Admin.RoleInfo_Tbl (roleid, languageid, name, note) VALUES (5, 2, 'Controller', 'A member of this role may access the mController module.');
INSERT INTO Admin.RoleInfo_Tbl (roleid, languageid, name, note) VALUES (5, 3, 'Kontrollør', 'Et medlem af denne rolle har adgang til mController modulet');

INSERT INTO System.PSP_Tbl (name) VALUES ('CPG');
INSERT INTO System.PSPCard_Tbl (pspid, cardid) SELECT Max(PSP.id), C.id FROM System.PSP_Tbl PSP, System.Card_Tbl C WHERE C.id < 10 GROUP BY C.id;
