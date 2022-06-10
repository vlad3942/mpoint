Insert into client.countrycurrency_tbl (clientid, countryid, currencyid) values (10077, 200, 840);

INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, enabled, username, passwd, supportedpartialoperations)
VALUES (10077, 78, 'Reach', true, 'cellpoint', '3BfNgyHmrXfbixW', 0);

INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100770, 78, '-1');

INSERT INTO client.route_tbl (clientid, providerid) VALUES(10077, 78);

INSERT INTO client.routeconfig_tbl (id,routeid,"name",capturetype,mid,username,"password",enabled,isdeleted) VALUES
(614,131,'Reach_AE_VISA_MASTERCARD',2,'f30aab7f-7b56-4560-84c4-b9f3804e903a','cellpoint','3BfNgyHmrXfbixW',true,false);


INSERT INTO client.routecountry_tbl (id,routeconfigid,countryid,enabled VALUES
(783,614,200,true);


INSERT INTO client.routecurrency_tbl (id,routeconfigid,currencyid,enabled) VALUES
(757,614,840,true);


INSERT INTO client.routefeature_tbl (id,clientid,routeconfigid,featureid,enabled) VALUES
(231,10077,614,5,true),
(232,10077,614,6,true),
(233,10077,614,9,true);


INSERT INTO client.routepm_tbl (id,routeconfigid,pmid,enabled) VALUES
(1,614,1,true),
(2,614,7,true),
(3,614,8,true);
(3,614,3,true);
(3,614,22,true);

INSERT INTO client.cardaccess_tbl
(clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled)
VALUES(10077, 7, true, 78, 200, 1, NULL, false, 1, 0, 0, 2, NULL, false);

INSERT INTO client.cardaccess_tbl
(clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled)
VALUES(10077, 8, true, 78, 200, 1, NULL, false, 1, 0, 0, 2, NULL, false);

INSERT INTO client.cardaccess_tbl
(clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled)
VALUES(10077, 3, true, 78, 200, 1, NULL, false, 1, 0, 0, 2, NULL, false);

INSERT INTO client.cardaccess_tbl
(clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled)
VALUES(10077, 1, true, 78, 200, 1, NULL, false, 1, 0, 0, 2, NULL, false);

INSERT INTO client.cardaccess_tbl
(clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled)
VALUES(10077, 22, true, 78, 200, 1, NULL, false, 1, 0, 0, 2, NULL, false);

INSERT INTO client.psp_property_tbl
(clientid, propertyid, value, enabled)
VALUES(10077, (SELECT id FROM system.psp_property_tbl WHERE pspid = 78 and name = 'REACH_HMAC_SECRET'), 'QjTCPUk3zHita3Usk2CWEXqS5h9M571S5kBf2AFP0RVHiRO4umxuJ1sTN3ZcF89M', true);
