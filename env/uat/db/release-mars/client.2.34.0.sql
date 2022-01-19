-----CMP-6219-----
UPDATE client.account_tbl SET name = 'Southwest iOS App' WHERE id = 100691;

UPDATE client.account_tbl SET name = 'Southwest mWeb' WHERE id = 100690;
---------------

---- AV ELO card support for fraud-check call----
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid, psp_type, capture_type) VALUES (10101, 82, 64, true, 1, 9, 1)