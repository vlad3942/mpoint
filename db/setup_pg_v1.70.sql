INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (1009, 'Payment Initialized with Payment Service Provider', 'mApp', 'initialize');

INSERT INTO System.Type_Tbl (id, name) VALUES (102, 'Purchase of Points');
INSERT INTO System.Type_Tbl (id, name) VALUES (1004, 'Points Top-Up');
INSERT INTO System.Type_Tbl (id, name) VALUES (1005, 'Purchase using Points');
INSERT INTO System.Type_Tbl (id, name) VALUES (1006, 'Points Transfer');
INSERT INTO System.Type_Tbl (id, name) VALUES (1007, 'Points Reward');

UPDATE EndUser.Account_Tbl SET points = 0;

UPDATE Log.Transaction_Tbl SET refund = 0;
UPDATE Log.Transaction_Tbl SET refund = amount WHERE id IN (SELECT DISTINCT txnid FROM Log.MEssage_Tbl WHERE stateid = 2003);
