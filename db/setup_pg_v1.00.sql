/**
 * Setup SQL script for the PostGreSQL databse.
 * The file include any necesarry queries to populate an empty database with initial configuration data
 */

INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat) VALUES (10, 'Denmark', 'kr', '10000000', '99999999', '1230', '{PRICE}{CURRENCY}');
INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat) VALUES (20, 'USA', '$', '1000000000', '9999999999', '20100', '{CURRENCY}{PRICE}');

INSERT INTO System.PSP_Tbl (name) VALUES ('DIBS');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) SELECT 20, Max(id), 208 FROM System.PSP_Tbl;

INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (10, 'Undefined Client ID', 'Validate', 'valBasic');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (12, 'Invalid Client ID', 'Validate', 'valBasic');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (13, 'Unknown Client ID', 'Validate', 'valBasic');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (14, 'Client Disabled', 'Validate', 'valBasic');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (21, 'Undefined Account', 'Validate', 'valBasic');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (22, 'Invalid Account', 'Validate', 'valBasic');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (23, 'Unknown Account', 'Validate', 'valBasic');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (24, 'Account Disabled', 'Validate', 'valBasic');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (31, 'Undefined Address', 'Validate', 'valAddress');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (32, 'Address is too short', 'Validate', 'valAddress');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (33, 'Address is too long', 'Validate', 'valAddress');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (41, 'Undefined Operator ID', 'Validate', 'valOperator');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (42, 'Operator ID is too short', 'Validate', 'valOperator');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (43, 'Operator ID is too big', 'Validate', 'valOperator');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (44, 'Operator not supported', 'Call Centre', 'sendLink');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (51, 'Undefined Amount', 'Validate', 'valAmount');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (52, 'Recipient is too small', 'Validate', 'valAmount');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (53, 'Recipient is too big', 'Validate', 'valAmount');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (61, 'Undefined Product Names', 'Validate', 'valProducts');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (62, 'Undefined Product Quantities', 'Validate', 'valProducts');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (63, 'Undefined Product Prices', 'Validate', 'valProducts');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (64, 'Invalid Arrays sizes', 'Validate', 'valProducts');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (65, 'Array key not found in Product Quantities', 'Validate', 'valProducts');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (66, 'Array key not found in Product Prices', 'Validate', 'valProducts');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (67, 'Invalid URL found in array of Logo URLs', 'Validate', 'valProducts');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (71, 'Undefined Client Logo URL', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (72, 'Client Logo URL is too short', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (73, 'Client Logo URL is too long', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (74, 'Client Logo URL is malformed', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (75, 'Client Logo URL is Invalid, no Protocol specified', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (76, 'Client Logo URL is Invalid, no Host specified', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (77, 'Client Logo URL is Invalid, no Path specified', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (81, 'Undefined CSS URL', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (82, 'CSS URL is too short', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (83, 'CSS URL is too long', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (84, 'CSS URL is malformed', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (85, 'CSS URL is Invalid, no Protocol specified', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (86, 'CSS URL is Invalid, no Host specified', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (87, 'CSS URL is Invalid, no Path specified', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (91, 'Undefined Accept URL', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (92, 'Accept URL is too short', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (93, 'Accept URL is too long', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (94, 'Accept URL is malformed', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (95, 'Accept URL is Invalid, no Protocol specified', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (96, 'Accept URL is Invalid, no Host specified', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (97, 'Accept URL is Invalid, no Path specified', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (101, 'Undefined Cancel URL', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (102, 'Cancel URL is too short', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (103, 'Cancel URL is too long', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (104, 'Cancel URL is malformed', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (105, 'Cancel URL is Invalid, no Protocol specified', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (106, 'Cancel URL is Invalid, no Host specified', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (107, 'Cancel URL is Invalid, no Path specified', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (111, 'Undefined Callback URL', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (112, 'Callback URL is too short', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (113, 'Callback URL is too long', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (114, 'Callback URL is malformed', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (115, 'Callback URL is Invalid, no Protocol specified', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (116, 'Callback URL is Invalid, no Host specified', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (117, 'Callback URL is Invalid, no Path specified', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (121, 'Undefined Return URL', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (122, 'Return URL is too short', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (123, 'Return URL is too long', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (124, 'Return URL is malformed', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (125, 'Return URL is Invalid, no Protocol specified', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (126, 'Return URL is Invalid, no Host specified', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (127, 'Return URL is Invalid, no Path specified', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (131, 'Undefined Language', 'Validate', 'valLanguage');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (132, 'Invalid Language', 'Validate', 'valLanguage');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (133, 'Language not supported', 'Validate', 'valLanguage');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (1001, 'Input Valid', 'API', 'Controller');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (1002, 'Products', 'Call Centre', 'logProducts');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (1003, 'Client Variables', 'API', 'logClientVars');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (1010, 'Message accepted by GoMobile', 'Call Centre', 'sendLink');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (1011, 'Unable to connect to GoMobile', 'Call Centre', 'sendLink');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (1012, 'Message rejected by GoMobile', 'Call Centre', 'sendLink');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (1020, 'Payment link Constructed', 'Call Centre', 'constLink');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (1021, 'Payment link Activated', 'Payment', 'Overview');

INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (1990, 'Callback Accepted', 'Callback', 'send');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (1991, 'Callback Constructed', 'Callback', 'send');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (1992, 'Callback Conencted', 'Callback', 'send');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (1993, 'Callback Connection Failed', 'Callback', 'send');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (1994, 'Callback Transmission Failed', 'Callback', 'send');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (1995, 'Callback Rejeced', 'Callback', 'send');

INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (2000, 'Payment successfully cleared by PSP', 'Callback', 'completeTransaction');