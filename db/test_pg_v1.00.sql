/**
 * Test SQL script for the PostGreSQL databse.
 * The file include queries for populating an empty database with test data
 */
 
INSERT INTO Client.Client_Tbl (countryid, name, username, passwd, maxamount, logourl, cssurl, callbackurl, accepturl, cancelurl) VALUES (20, 'Cellpoint Mobile Test', 'CPMDemo', 'DEMOisNO_2', 10000, 'http://mpoint.localhost/_test/client_logo.jpg', 'http://mpoint.localhost/_test/styles.css', 'http://mpoint.localhost/_test/callback.php', 'http://mpoint.localhost/_test/accept.php', 'http://mpoint.localhost/_test/cancel.php');
INSERT INTO Client.Account_Tbl (clientid, name, address) VALUES (10000, 'Test 1', '3053315242');
INSERT INTO Client.CardAccess_Tbl (clientid, cardid) SELECT 10000, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO Client.Keyword_Tbl (clientid, name, price) VALUES (10000, 'CPT', -1);