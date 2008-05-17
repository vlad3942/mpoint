ALTER TABLE Client.Client_Tbl ADD auto_capture BOOL;
ALTER TABLE Client.Client_Tbl ALTER auto_capture SET DEFAULT true;
UPDATE Client.Client_Tbl SET auto_capture = true;

ALTER TABLE Client.Client_Tbl ADD send_pspid BOOL;
ALTER TABLE Client.Client_Tbl ALTER send_pspid SET DEFAULT true;
UPDATE Client.Client_Tbl SET send_pspid = false;

ALTER TABLE Log.Transaction_Tbl ADD auto_capture BOOL;
UPDATE Log.Transaction_Tbl SET auto_capture = true;

UPDATE Log.State_tbl SET id = 2010 WHERE id = 2001;
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (2001, 'Payment captured cleared by PSP', 'Callback', 'completeTransaction');
INSERT INTO Log.Message_Tbl (txnid, stateid) SELECT DISTINCT txnid, 2001 FROM Log.Message_Tbl WHERE stateid = 2000;