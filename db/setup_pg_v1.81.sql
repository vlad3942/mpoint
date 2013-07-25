INSERT INTO System.URLType_Tbl (id, name) VALUES (1, 'Import Customer Data');

INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10007, 1, 'http://cpmdemo.mesb.test.cellpointmobile.com:10080/mpoint/sdc/import-customer-data');

-- UPDATE Client.MerchantAccount_Tbl SET pspid = 4, name = 'CELLPOINTREC', username = 'CELLPOINTREC', passwd = 'live2011', stored_card = true WHERE id = 8;
-- UPDATE Client.MerchantAccount_Tbl SET clientid = 10012, stored_card = false WHERE id = 33;
-- UPDATE Client.MErchantSubAccount_Tbl SET accountid = 100012 WHERE id = 9;
-- UPDATE Client.CardAccess_Tbl SET pspid = 4 WHERE pspid = 7 AND clientid = 10012;
-- UPDATE Client.MerchantAccount_Tbl SET passwd = 'oisJona1' WHERE id IN (8, 33)