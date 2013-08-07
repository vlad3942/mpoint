INSERT INTO System.URLType_Tbl (id, name) VALUES (1, 'Import Customer Data');

INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10007, 1, 'http://cpmdemo.mesb.test.cellpointmobile.com:10080/mpoint/sdc/import-customer-data');

-- UPDATE Client.MerchantAccount_Tbl SET pspid = 4, name = 'CELLPOINTREC', username = 'CELLPOINTREC', passwd = 'live2011', stored_card = true WHERE id = 8;
-- UPDATE Client.MerchantAccount_Tbl SET clientid = 10012, stored_card = false WHERE id = 33;
-- UPDATE Client.MErchantSubAccount_Tbl SET accountid = 100012 WHERE id = 9;
-- UPDATE Client.CardAccess_Tbl SET pspid = 4 WHERE pspid = 7 AND clientid = 10012;
-- UPDATE Client.MerchantAccount_Tbl SET passwd = 'oisJona1' WHERE id IN (8, 33)

INSERT INTO System.URLType_Tbl (id, name) VALUES (2, 'Single Sign-On Authentication');

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd, stored_card) VALUES (10010, 4, 'PIZZAHUTECOMMTREC', 'PIZZAHUTECOMMTREC', '3Pjge5RTT1', true);
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd, stored_card) VALUES (10011, 4, 'PIZZAHUTECOMMBKREC', 'PIZZAHUTECOMMBKREC', '3Pjge5RTT1', true);
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd, stored_card) VALUES (10018, 4, 'YUMMOBDELREC', 'YUMMOBDELREC', '3Pjge5RTT1', true);
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd, stored_card) VALUES (10021, 4, 'PIZZAHUTECOMMREC', 'PIZZAHUTECOMMREC', '3Pjge5RTT1', true);
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd, stored_card) VALUES (10022, 4, 'PIZZAHUTFRANREC', 'PIZZAHUTFRANREC', '3Pjge5RTT1', true);
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd, stored_card) VALUES (10023, 4, 'YUMWEBDELREC', 'YUMWEBDELREC', '3Pjge5RTT1', true);

INSERT INTO Client.CardAccess_Tbl (cardid, clientid, pspid) VALUES (11, 10010, 1);
INSERT INTO Client.CardAccess_Tbl (cardid, clientid, pspid) VALUES (11, 10011, 1);
INSERT INTO Client.CardAccess_Tbl (cardid, clientid, pspid) VALUES (11, 10018, 1);
INSERT INTO Client.CardAccess_Tbl (cardid, clientid, pspid) VALUES (11, 10021, 1);
INSERT INTO Client.CardAccess_Tbl (cardid, clientid, pspid) VALUES (11, 10022, 1);
INSERT INTO Client.CardAccess_Tbl (cardid, clientid, pspid) VALUES (11, 10023, 1);

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name) VALUES (10010, 1, 'CPM');
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name) VALUES (10011, 1, 'CPM');
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name) VALUES (10018, 1, 'CPM');
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name) VALUES (10021, 1, 'CPM');
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name) VALUES (10022, 1, 'CPM');
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name) VALUES (10023, 1, 'CPM');

INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) SELECT id, 1, '-1' FROM Client.Account_Tbl WHERE clientid = 10010;
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) SELECT id, 1, '-1' FROM Client.Account_Tbl WHERE clientid = 10011;
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) SELECT id, 1, '-1' FROM Client.Account_Tbl WHERE clientid = 10018;
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) SELECT id, 1, '-1' FROM Client.Account_Tbl WHERE clientid = 10021;
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) SELECT id, 1, '-1' FROM Client.Account_Tbl WHERE clientid = 10022;
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) SELECT id, 1, '-1' FROM Client.Account_Tbl WHERE clientid = 10023;

INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (201, 'Undefined Auth URL', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (202, 'Auth URL is too short', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (203, 'Auth URL is too long', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (204, 'Auth URL is malformed', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (205, 'Auth URL is Invalid, no Protocol specified', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (206, 'Auth URL is Invalid, no Host specified', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (207, 'Auth URL is Invalid, no Path specified', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (208, 'URL domain doesn''t match configured URL', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (209, 'Auth URL must be configured for Client', 'Validate', 'valURL');

INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10012, 2, 'http://mpoint.test.cellpointmobile.com');
INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10021, 2, 'http://www.uat.pizzahut.co.uk');
INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10022, 2, 'http://www.uat.pizzahut.co.uk');
INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10023, 2, 'http://www.uat.pizzahut.co.uk');