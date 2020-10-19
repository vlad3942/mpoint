<!-- Paymaya on-board sqls start -->
INSERT INTO System.PSP_Tbl (id, name, system_type) VALUES (68, 'paymaya', 4);
INSERT INTO System.PSPCurrency_Tbl (pspid, name, currencyid) VALUES (68,'PHP', 608);
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (95, 'paymaya', 23, -1, -1, -1,4);
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (68, 'paymaya',4);
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (640,68,'PHP');
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (95, 0, 0);
INSERT INTO System.pspcard_tbl (cardid, pspid, enabled) VALUES (95, 68, 'true');
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) VALUES (95, -608);
INSERT INTO system.pricepoint_tbl (id, amount, enabled, currencyid) VALUES (-608, -1, true, 608);

INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, countryid,psp_type,capture_type) VALUES (10077, 95, 68, true, 640,4,2); 
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10077,68, 'paymaya', 'pk-MOfNKu3FmHMVHtjyjG7vhr7vFevRkWxmxYL1Yq6iFk5', 'sk-NMda607FeZNGRt9xCdsIRiZ4Lqu6LT898ItHbN4qPSe');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100773, 68, 'paymaya');
INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid) VALUES (10077,640,608);
<!-- Paymaya on-board sqls end -->