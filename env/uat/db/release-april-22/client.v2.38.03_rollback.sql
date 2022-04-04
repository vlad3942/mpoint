-- NMI Rollback Quries --

DELETE FROM  client.merchantaccount_tbl WHERE pspid= 74 and clientid = 10101;
DELETE FROM  Client.MerchantSubAccount_Tbl  WHERE pspid= 74;

DELETE FROM client.routecountry_tbl WHERE routeconfigid = <routeconfigid>;
DELETE FROM client.routecurrency_tbl WHERE routeconfigid = <routeconfigid>;
DELETE FROM client.routeconfig_tbl WHERE routeid = <routeid>;
DELETE FROM  client.route_tbl WHERE providerid= 74 and clientid = 10101;

DELETE FROM client.countrycurrency_tbl WHERE clientid = 10101 and  countryid = 428 and  currencyid = 840;
DELETE FROM client.countrycurrency_tbl WHERE clientid = 10101 and  countryid = 408 and  currencyid = 840;
DELETE FROM client.countrycurrency_tbl WHERE clientid = 10101 and  countryid = 410 and  currencyid = 840;
DELETE FROM client.countrycurrency_tbl WHERE clientid = 10101 and  countryid = 412 and  currencyid = 840;
DELETE FROM client.countrycurrency_tbl WHERE clientid = 10101 and  countryid = 427 and  currencyid = 840;
DELETE FROM client.countrycurrency_tbl WHERE clientid = 10101 and  countryid = 209 and  currencyid = 840;
DELETE FROM client.countrycurrency_tbl WHERE clientid = 10101 and  countryid = 429 and  currencyid = 840;
DELETE FROM client.countrycurrency_tbl WHERE clientid = 10101 and  countryid = 404 and  currencyid = 840;
DELETE FROM client.countrycurrency_tbl WHERE clientid = 10101 and  countryid = 439 and  currencyid = 840;
DELETE FROM client.countrycurrency_tbl WHERE clientid = 10101 and  countryid = 432 and  currencyid = 840;
DELETE FROM client.countrycurrency_tbl WHERE clientid = 10101 and  countryid = 304 and  currencyid = 840;
DELETE FROM client.countrycurrency_tbl WHERE clientid = 10101 and  countryid = 407 and  currencyid = 840;
DELETE FROM client.countrycurrency_tbl WHERE clientid = 10101 and  countryid = 201 and  currencyid = 840;
DELETE FROM client.countrycurrency_tbl WHERE clientid = 10101 and  countryid = 202 and  currencyid = 840;
DELETE FROM client.countrycurrency_tbl WHERE clientid = 10101 and  countryid = 406 and  currencyid = 840;
DELETE FROM client.countrycurrency_tbl WHERE clientid = 10101 and  countryid = 200 and  currencyid = 840;


DELETE FROM system.psp_tbl WHERE id = 74 ;
DELETE FROM system.pspcard_tbl WHERE id = 74 ;
DELETE FROM system.pspcurrency_tbl WHERE id = 74 ;