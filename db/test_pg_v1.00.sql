/**
 * Test SQL script for the PostGreSQL databse.
 * The file include queries for populating an empty database with test data
 */
 
INSERT INTO Client.Client_Tbl (countryid, name, username, passwd, maxamount, logourl, cssurl, callbackurl, accepturl, cancelurl) VALUES (20, 'Cellpoint Mobile Test', 'CPMDemo', 'DEMOisNO_2', 10000, 'http://mpoint.localhost/_test/client_logo.jpg', 'http://mpoint.localhost/_test/styles.css', 'http://mpoint.localhost/_test/callback.php', 'http://mpoint.localhost/_test/accept.php', 'http://mpoint.localhost/_test/cancel.php');
INSERT INTO Client.Account_Tbl (clientid, name, address) SELECT Max(id), 'Test 1', '3053315242' FROM Client.Client_Tbl;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid) SELECT Max(Cl.id), C.id FROM System.Card_Tbl C, Client.Client_Tbl Cl WHERE C.id > 0 GROUP BY C.id;
INSERT INTO Client.Keyword_Tbl (clientid, name, standard) SELECT Max(id), 'CPT', true FROM Client.Client_Tbl;
INSERT INTO Client.Keyword_Tbl (clientid, name, standard) SELECT Max(id), 'MPOINT', false FROM Client.Client_Tbl;
INSERT INTO Client.Product_Tbl (keywordid, name, quantity, price, logourl) SELECT Max(id), 'SMS Test Product', 2, 1000, '' FROM Client.Keyword_Tbl;
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name) SELECT Max(id), 1, '4216310' FROM Client.Client_Tbl;
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) SELECT Max(id), 1, '-1'  FROM Client.Account_Tbl;

INSERT INTO Client.Client_Tbl (countryid, name, username, passwd, maxamount, logourl, cssurl, callbackurl, accepturl, cancelurl) VALUES (10, 'Cellpoint Mobile Test DK', 'CPMDemo', 'DEMOisNO_2', 10000, 'http://mpoint.localhost/_test/client_logo.jpg', 'http://mpoint.localhost/_test/styles.css', 'http://mpoint.localhost/_test/callback.php', 'http://mpoint.localhost/_test/accept.php', 'http://mpoint.localhost/_test/cancel.php');
INSERT INTO Client.Account_Tbl (clientid, name, address) SELECT Max(id), 'DK Test 1', '28882861' FROM Client.Client_Tbl;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid) SELECT Max(Cl.id), C.id FROM System.Card_Tbl C, Client.Client_Tbl Cl WHERE C.id > 0 GROUP BY C.id;
INSERT INTO Client.Keyword_Tbl (clientid, name, standard) SELECT Max(id), 'CPT', true FROM Client.Client_Tbl;
INSERT INTO Client.Product_Tbl (keywordid, name, quantity, price, logourl) SELECT Max(id), 'SMS Test Product', 2, 1000, '' FROM Client.Keyword_Tbl;
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name) SELECT Max(id), 1, '4216310' FROM Client.Client_Tbl;
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) SELECT Max(id), 1, '-1'  FROM Client.Account_Tbl;