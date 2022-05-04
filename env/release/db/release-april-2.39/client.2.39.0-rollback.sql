DELETE FROM client.fraud_config_tbl WHERE clientid = 10101 AND pmid = 22 AND providerid = 64 AND countryid = 0 AND currencyid = 0 AND typeoffraud = 1;

-- NMI Rollback Quries --
DELETE FROM client.account_tbl where clientid=10101 and id IN (101117,101118,101119,101120,101121,101122);
DELETE FROM client.MerchantSubAccount_Tbl  WHERE accountid IN (101117,101118,101119,101120,101121,101122);

--DELETE FROM client.routecountry_tbl WHERE routeconfigid = <routeconfigid>;
--DELETE FROM client.routecurrency_tbl WHERE routeconfigid = <routeconfigid>;
--DELETE FROM client.routeconfig_tbl WHERE routeid = <routeid>;

DELETE FROM client.route_tbl WHERE providerid= 74 and clientid = 10101;
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

--WorldPay_USA
--DELETE FROM client.routecountry_tbl WHERE routeconfigid = <routeconfigid>;
--DELETE FROM client.routecurrency_tbl WHERE routeconfigid = <routeconfigid>;
--DELETE FROM client.routeconfig_tbl WHERE routeid = <routeid>;

--WorldPay_others
--DELETE FROM client.routecountry_tbl WHERE routeconfigid = <routeconfigid>;
--DELETE FROM client.routecurrency_tbl WHERE routeconfigid = <routeconfigid>;
--DELETE FROM client.routeconfig_tbl WHERE routeid = <routeid>;

--WorldPay_CR
--DELETE FROM client.routecountry_tbl WHERE routeconfigid = <routeconfigid>;
--DELETE FROM client.routecurrency_tbl WHERE routeconfigid = <routeconfigid>;
--DELETE FROM client.routeconfig_tbl WHERE routeid = <routeid>;