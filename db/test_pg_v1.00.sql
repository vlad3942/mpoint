/**
 * Test SQL script for the PostGreSQL databse.
 * The file include queries for populating an empty database with test data
 */
 
INSERT INTO Client.Client_Tbl (countryid, flowid, name, username, passwd, maxamount, language, logourl, cssurl, callbackurl, accepturl, cancelurl, terms) VALUES (20, 2, 'Cellpoint Mobile Test', 'CPMDemo', 'DEMOisNO_2', 1000000, 'gb', 'http://demo.ois-inc.com/mpoint/_test/client_logo.jpg', 'http://demo.ois-inc.com/mpoint/_test/styles.css', 'http://demo.ois-inc.com/mpoint/_test/callback.php', 'http://demo.ois-inc.com/mpoint/_test/accept.php', 'http://demo.ois-inc.com/mpoint/_test/cancel.php', 'Very nice Terms & Conditions');
INSERT INTO Client.Account_Tbl (clientid, name, address) SELECT Max(id), 'Test 1', '3053315242' FROM Client.Client_Tbl;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid) SELECT Max(Cl.id), C.id FROM System.Card_Tbl C, Client.Client_Tbl Cl WHERE C.id > 0 GROUP BY C.id;
INSERT INTO Client.Keyword_Tbl (clientid, name, standard) SELECT Max(id), 'CPT', true FROM Client.Client_Tbl;
INSERT INTO Client.Keyword_Tbl (clientid, name, standard) SELECT Max(id), 'MPOINT', false FROM Client.Client_Tbl;
INSERT INTO Client.Product_Tbl (keywordid, name, quantity, price, logourl) SELECT Max(id), 'Flamingo - Black', 1, 9995, 'http://demo.ois-inc.com/mpoint/_test/flamingo.jpg' FROM Client.Keyword_Tbl;
INSERT INTO Client.Product_Tbl (keywordid, name, quantity, price, logourl) SELECT Max(id), 'Flamingo - Silver', 1, 9995, 'http://demo.ois-inc.com/mpoint/_test/flamingo.jpg' FROM Client.Keyword_Tbl;
INSERT INTO Client.Shop_Tbl (clientid, keywordid, shipping, ship_cost, free_ship, del_date) SELECT Max(CL.id), Max(KW.id), 'UPS', 6500, 50000, false FROM Client.Client_Tbl Cl, Client.Keyword_Tbl KW;
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name) SELECT Max(id), 2, '4216310' FROM Client.Client_Tbl;
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) SELECT Max(id), 2, '-1'  FROM Client.Account_Tbl;

INSERT INTO Client.Client_Tbl (countryid, flowid, name, username, passwd, maxamount, language, logourl, cssurl, callbackurl, accepturl, cancelurl, terms) VALUES (10, 2, 'Cellpoint Mobile Test DK', 'CPMDemo', 'DEMOisNO_2', 1000000, 'da', 'http://demo.ois-inc.com/mpoint/_test/client_logo.jpg', 'http://demo.ois-inc.com/mpoint/_test/styles.css', 'http://demo.ois-inc.com/mpoint/_test/callback.php', 'http://demo.ois-inc.com/mpoint/_test/accept.php', 'http://demo.ois-inc.com/mpoint/_test/cancel.php', 'All your moneys are belong to us');
INSERT INTO Client.Account_Tbl (clientid, name, address) SELECT Max(id), 'DK Test 1', '28882861' FROM Client.Client_Tbl;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid) SELECT Max(Cl.id), C.id FROM System.Card_Tbl C, Client.Client_Tbl Cl WHERE C.id > 0 GROUP BY C.id;
INSERT INTO Client.Keyword_Tbl (clientid, name, standard) SELECT Max(id), 'CPT', true FROM Client.Client_Tbl;
INSERT INTO Client.Product_Tbl (keywordid, name, quantity, price, logourl) SELECT Max(id), 'Flamingo - Black', 1, 69500, 'http://demo.ois-inc.com/mpoint/_test/flamingo.jpg' FROM Client.Keyword_Tbl;
INSERT INTO Client.Product_Tbl (keywordid, name, quantity, price, logourl) SELECT Max(id), 'Flamingo - Silver', 1, 69500, 'http://demo.ois-inc.com/mpoint/_test/flamingo.jpg' FROM Client.Keyword_Tbl;
INSERT INTO Client.Shop_Tbl (clientid, keywordid, shipping, ship_cost, free_ship, del_date) SELECT Max(CL.id), Max(KW.id), 'Post Danmark', 2900, 50000, true FROM Client.Client_Tbl Cl, Client.Keyword_Tbl KW;
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name) SELECT Max(id), 2, '4216310' FROM Client.Client_Tbl;
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) SELECT Max(id), 2, '-1'  FROM Client.Account_Tbl;