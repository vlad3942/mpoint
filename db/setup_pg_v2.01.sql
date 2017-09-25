UPDATE Client.CardAccess_Tbl SET stateid = 2 WHERE clientid IN (10005, 10019) AND cardid = 2;

INSERT INTO Client.IINList_Tbl (clientid, iinactionid, min, max) VALUES (10005, 1, 5019, 5019);
INSERT INTO Client.IINList_Tbl (clientid, iinactionid, min, max) VALUES (10019, 1, 5019, 5019);