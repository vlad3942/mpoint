/**
 * Test SQL script for the PostGreSQL databse.
 * The file include queries for populating an empty database with test data
 */
 
INSERT INTO Client.Client_Tbl (countryid, name, username, passwd, logourl, cssurl, callbackurl, accepturl, cancelurl) VALUES (20, 'Cellpoint Mobile Test', 'CPMTest', 'TESTisNO_3', 'http://mpoint.localhost/_test/logo.jpg', 'http://mpoint.localhost/_test/styles.css', 'http://mpoint.localhost/_test/callback.php', 'http://mpoint.localhost/_test/accept.php', 'http://mpoint.localhost/_test/cancel.php');
INSERT INTO Client.Account_Tbl (clientid, name, address) VALUES (10000, 'Test 1', '28882861');
INSERT INTO Client.CardAccess_Tbl (clientid, cardid) SELECT 10000, id FROM System.Card_Tbl WHERE id > 0;