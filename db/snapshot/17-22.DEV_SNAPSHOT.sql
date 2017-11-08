INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (2004, 'Payment approved for partial amount', 'Payment', '');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (2005, '3d verification required for Authorization', 'Payment', '');


/*=========================PayTabs===================================== */

INSERT INTO System.PSP_Tbl (id, name) VALUES (38, 'PayTabs');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (602,38,'AED');
INSERT INTO System.PspCard_Tbl(cardid, pspid) VALUES (31, 38);
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 38, 'PayTabs', 'Arun123', 'zoVCrg1wOzCN22cXIZt5YM3TnAKoA5paulNWBOtqo6eq8roRqSWoEZh1A2qb7PlCa9yMX2cm8qMgSb7i34HH3ZID19P9YaL9jkVh');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 38, '-1');
UPDATE Client.CardAccess_Tbl SET pspid = 38, countryid = 602 WHERE clientid = 10007 AND cardid = 31;


INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('MERCHANT_URL', 'test_sadad@paytabs.com', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10007 and pspid = 38), 'merchant');

/*=========================PayTabs===================================== */


/*========================= 2C2P ALC =================================== */

INSERT INTO System.EndPoint_Tbl (productid, path, authentication, note) SELECT Max(id), '/mpoint/2c2p-alc/initialize','basic', 'mesb - 2c2p-alc initialize of payment' FROM System.Product_Tbl WHERE id = 4;
INSERT INTO System.Transformation_Tbl (endpointid, contenttypeid, note) SELECT EP.id, 2, EP.note  FROM System.EndPoint_Tbl EP WHERE path = '/mpoint/2c2p-alc/initialize';
INSERT INTO Client.EndPointAccess_Tbl (groupid, endpointid) SELECT 29, EP.id FROM  System.EndPoint_Tbl EP WHERE path = '/mpoint/2c2p-alc/initialize';

INSERT INTO System.EndPoint_Tbl (productid, path, authentication, note) SELECT Max(id), '/mpoint/2c2p-alc/authorize-payment','basic', 'mesb - 2c2p-alc authorize-payment' FROM System.Product_Tbl WHERE id = 4;
INSERT INTO System.Transformation_Tbl (endpointid, contenttypeid, note) SELECT EP.id, 2, EP.note  FROM System.EndPoint_Tbl EP WHERE path = '/mpoint/2c2p-alc/authorize-payment';
INSERT INTO Client.EndPointAccess_Tbl (groupid, endpointid) SELECT 29, EP.id FROM  System.EndPoint_Tbl EP WHERE path = '/mpoint/2c2p-alc/authorize-payment';

INSERT INTO System.EndPoint_Tbl (productid, path, authentication, note) SELECT Max(id), '/mpoint/2c2p-alc/threed-redirect','none', 'mesb - 2c2p-alc threed-redirect' FROM System.Product_Tbl WHERE id = 4;
INSERT INTO System.Transformation_Tbl (endpointid, contenttypeid, note) SELECT EP.id, 3, EP.note  FROM System.EndPoint_Tbl EP WHERE path = '/mpoint/2c2p-alc/threed-redirect';
INSERT INTO Client.EndPointAccess_Tbl (groupid, endpointid) SELECT 29, EP.id FROM  System.EndPoint_Tbl EP WHERE path = '/mpoint/2c2p-alc/threed-redirect';

/*========================= 2C2P ALC ends ================================ */