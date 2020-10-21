--- Fraud check review status
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3017, 'Pre Auth Rev Success', 'Fraud', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3018, 'Pre Auth Rev Fail', 'Fraud', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3118, 'Post Auth Rev Success', 'Fraud', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3119, 'Post Auth Rev Fail', 'Fraud', '');
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

INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (2004, '3ds Card Not Enrolled', 'Payment', '');
UPDATE Log.State_Tbl set name = '3ds Card Not Enrolled' WHERE id = 2004;

INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2004002, 'Authentication Card Not enrolled.', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2004003, 'Authentication Card Not enrolled cache.', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2004095, 'Authentication No directory found for PAN/cardtype', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2004096, 'Authentication No version 2 directory found for PAN/cardtype', 'sub-code', '');

INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2005001, 'Authentication Card is enrolled Attempt authentication using 3DSv1.0', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2005002, 'Authentication Card is enrolled Attempt authentication using 3DSv2.0', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2005003, 'Authentication Attempt authentication by loading Unknown HTML Format', 'sub-code', '');

INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2006001, 'Authentication Fully', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2006004, 'Authentication Attempt (Proof of authentication attempt, may continue to transaction)', 'sub-code', '');

INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2016000, 'Not Authenticated', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2016005, 'Authentication grey area', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2016006, 'Authentication Error received (from Directory or ACS)', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2016091, 'Authentication Network error', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2016092, 'Authentication Directory error (read timeout)', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2016093, 'Authentication Configuration error', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2016094, 'Authentication Input Errors', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2016097, 'Authentication If transaction not found on continue or service query', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2016099, 'Authentication System error', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2016998, 'Authentication Not Applicable', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2016999, 'Authentication Unknown Error', 'sub-code', '');

DELETE FROM client.additionalproperty_tbl where key = 'mpi_rule';

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'mpi_rule', 'isProceedAuth::=<status>=="2"OR<status>=="5"OR<status>=="6"
status::=(additional-data.param[@name=''status''])', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10077 AND pspid=4;