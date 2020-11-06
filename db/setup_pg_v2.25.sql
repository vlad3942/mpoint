/* ========== Paymaya on-board sqls start ========== */
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, countryid,psp_type,capture_type) VALUES (10077, 95, 68, true, 640,4,2); 
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10077,68, 'paymaya', 'pk-MOfNKu3FmHMVHtjyjG7vhr7vFevRkWxmxYL1Yq6iFk5', 'sk-NMda607FeZNGRt9xCdsIRiZ4Lqu6LT898ItHbN4qPSe');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100773, 68, 'paymaya');
INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid) VALUES (10077,640,608);
/* ========== Paymaya on-board sqls end ========== */