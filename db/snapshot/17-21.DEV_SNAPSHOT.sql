<-- Pay Tabs Database Script-->

INSERT INTO System.PSP_Tbl (id, name) VALUES (40, 'PayTabs');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (602,40,'AED');
INSERT INTO System.PspCard_Tbl(cardid, pspid) VALUES (31, 40);
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 40, 'PayTabs', '10004931', 'zoVCrg1wOzCN22cXIZt5YM3TnAKoA5paulNWBOtqo6eq8roRqSWoEZh1A2qb7PlCa9yMX2cm8qMgSb7i34HH3ZID19P9YaL9jkVh');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 40, '-1');
UPDATE Client.CardAccess_Tbl SET pspid = 40, countryid = 602 WHERE clientid = 10007 AND cardid = 31;

INSERT INTO System.EndPoint_Tbl (productid, path, note) SELECT Max(id), 'mpoint/paytabs/initialize', 'mesb - Paytabs initialize of payment' FROM System.Product_Tbl WHERE id = 4;
INSERT INTO System.Transformation_Tbl (endpointid, contenttypeid, note, xsl) SELECT Max(id), 4, 'pay tabs initalize', '' FROM System.EndPoint_Tbl;
INSERT INTO Client.EndPointAccess_Tbl (groupid, endpointid) SELECT 29, Max(id) FROM System.EndPoint_Tbl;