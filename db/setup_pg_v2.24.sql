-- Update/Correct invalid country codes in the database
UPDATE System.Country_Tbl SET code = 056 WHERE alpha3code = 'BEL';
UPDATE System.Country_Tbl SET code = 040 WHERE alpha3code = 'AUT';
UPDATE System.Country_Tbl SET code = 004 WHERE alpha3code = 'AFG';
UPDATE System.Country_Tbl SET code = 008 WHERE alpha3code = 'ALB';
UPDATE System.Country_Tbl SET code = 020 WHERE alpha3code = 'AND';
UPDATE System.Country_Tbl SET code = 051 WHERE alpha3code = 'ARM';
UPDATE System.Country_Tbl SET code = 070 WHERE alpha3code = 'BIH';
UPDATE System.Country_Tbl SET code = 028 WHERE alpha3code = 'ATG';
UPDATE System.Country_Tbl SET code = 052 WHERE alpha3code = 'BRB';
UPDATE System.Country_Tbl SET code = 016 WHERE alpha3code = 'ASM';
UPDATE System.Country_Tbl SET code = 060 WHERE alpha3code = 'BMU';
UPDATE System.Country_Tbl SET code = 044 WHERE alpha3code = 'BHS';
UPDATE System.Country_Tbl SET code = 012 WHERE alpha3code = 'DZA';
UPDATE System.Country_Tbl SET code = 024 WHERE alpha3code = 'AGO';
UPDATE System.Country_Tbl SET code = 050 WHERE alpha3code = 'BGD';
UPDATE System.Country_Tbl SET code = 072 WHERE alpha3code = 'BWA';
UPDATE System.Country_Tbl SET code = 032 WHERE alpha3code = 'ARG';
UPDATE System.Country_Tbl SET code = 084 WHERE alpha3code = 'BLZ';
UPDATE System.Country_Tbl SET code = 076 WHERE alpha3code = 'BRA';
UPDATE System.Country_Tbl SET code = 010 WHERE alpha3code = 'ATA';
UPDATE System.Country_Tbl SET code = 074 WHERE alpha3code = 'BVT';
UPDATE System.Country_Tbl SET code = 036 WHERE alpha3code = 'AUS';
UPDATE System.Country_Tbl SET code = 096 WHERE alpha3code = 'BRN';
UPDATE System.Country_Tbl SET code = 090 WHERE alpha3code = 'SLB';
UPDATE System.Country_Tbl SET code = 048 WHERE alpha3code = 'BHR';
UPDATE System.Country_Tbl SET code = 031 WHERE alpha3code = 'AZE';
UPDATE System.Country_Tbl SET code = 064 WHERE alpha3code = 'BTN';

--Update/Correct the invalid country_calling_code in the database
UPDATE System.Country_Tbl SET country_calling_code = 441624 WHERE alpha3code = 'IMN';
UPDATE System.Country_Tbl SET country_calling_code = 1 WHERE alpha3code = 'CAN';
UPDATE System.Country_Tbl SET country_calling_code = 1264 WHERE alpha3code = 'AIA';
UPDATE System.Country_Tbl SET country_calling_code = 1268 WHERE alpha3code = 'ATG';
UPDATE System.Country_Tbl SET country_calling_code = 1246 WHERE alpha3code = 'BRB';
UPDATE System.Country_Tbl SET country_calling_code = 1284 WHERE alpha3code = 'VGB';
UPDATE System.Country_Tbl SET country_calling_code = 1345 WHERE alpha3code = 'CYM';
UPDATE System.Country_Tbl SET country_calling_code = 1809 WHERE alpha3code = 'DOM';
UPDATE System.Country_Tbl SET country_calling_code = 1876 WHERE alpha3code = 'JAM';
UPDATE System.Country_Tbl SET country_calling_code = 1684 WHERE alpha3code = 'ASM';
UPDATE System.Country_Tbl SET country_calling_code = 1441 WHERE alpha3code = 'BMU';
UPDATE System.Country_Tbl SET country_calling_code = 1242 WHERE alpha3code = 'BHS';
UPDATE System.Country_Tbl SET country_calling_code = 212 WHERE alpha3code = 'ESH';
UPDATE System.Country_Tbl SET country_calling_code = 672 WHERE alpha3code = 'ATA';
UPDATE System.Country_Tbl SET country_calling_code = 1767 WHERE alpha3code = 'DMA';
UPDATE System.Country_Tbl SET country_calling_code = 1473 WHERE alpha3code = 'GRD';
UPDATE System.Country_Tbl SET country_calling_code = 1787 WHERE alpha3code = 'PRI';
UPDATE System.Country_Tbl SET country_calling_code = 1649 WHERE alpha3code = 'TCA';
UPDATE System.Country_Tbl SET country_calling_code = 1868 WHERE alpha3code = 'TTO';
UPDATE System.Country_Tbl SET country_calling_code = 61 WHERE alpha3code = 'CXR';
UPDATE System.Country_Tbl SET country_calling_code = 7 WHERE alpha3code = 'KAZ';
UPDATE System.Country_Tbl SET country_calling_code = 996 WHERE alpha3code = 'KGZ';
UPDATE System.Country_Tbl SET country_calling_code = 1671 WHERE alpha3code = 'GUM';
UPDATE System.Country_Tbl SET country_calling_code = 1670 WHERE alpha3code = 'MNP';
UPDATE System.Country_Tbl SET country_calling_code = 886 WHERE alpha3code = 'TWN';
UPDATE System.Country_Tbl SET country_calling_code = 675 WHERE alpha3code = 'PGK';
UPDATE System.Country_Tbl SET country_calling_code = 95 WHERE alpha3code = 'MMR';

--Add/Update missing standard ISO countries in the database.
INSERT INTO system.country_tbl(name, minmob, maxmob, channel, priceformat, decimals, alpha2code,alpha3code, code, currencyid, country_calling_code)
VALUES ('Aland Islands', '1000', '9999999999', '123', '', 2,'AX', 'ALA', 248, 978, 358);

INSERT INTO system.country_tbl(name, minmob, maxmob, channel, priceformat, decimals, alpha2code,alpha3code, code, currencyid, country_calling_code)
VALUES ('British Indian Ocean Territory', '1000', '9999999999', '123', '', 2,'IO', 'IOT', 086, 840, 246);

INSERT INTO system.country_tbl(name, minmob, maxmob, channel, priceformat, decimals, alpha2code,alpha3code, code, currencyid, country_calling_code)
VALUES ('French Southern Territories', '1000', '9999999999', '123', '', 2,'TF', 'ATF', 260, 978, 262);

INSERT INTO system.country_tbl(name, minmob, maxmob, channel, priceformat, decimals, alpha2code,alpha3code, code, currencyid, country_calling_code)
VALUES ('Heard and McDonald Islands', '1000', '9999999999', '123', '', 2,'HM', 'HMD', 334, 36, 672);

INSERT INTO system.country_tbl(name, minmob, maxmob, channel, priceformat, decimals, alpha2code,alpha3code, code, currencyid, country_calling_code)
VALUES ('Vatican', '1000', '9999999999', '123', '', 2,'VA', 'VAT', 336, 978, 379);

INSERT INTO system.country_tbl(name, minmob, maxmob, channel, priceformat, decimals, alpha2code,alpha3code, code, currencyid, country_calling_code)
VALUES ('Laos', '1000', '9999999999', '123', '', 2,'LA', 'LAO', 418, 418, 856);

INSERT INTO system.country_tbl(name, minmob, maxmob, channel, priceformat, decimals, alpha2code,alpha3code, code, currencyid, country_calling_code)
VALUES ('Myanmar', '1000', '9999999999', '123', '', 2,'MM', 'MMR', 104, 104, 95);

INSERT INTO system.country_tbl(name, minmob, maxmob, channel, priceformat, alpha2code,alpha3code, code, country_calling_code)
VALUES ('Palestine', '1000', '9999999999', '123', '', 'PS', 'PSE', 275, 970);

INSERT INTO system.country_tbl(name, minmob, maxmob, channel, priceformat, decimals, alpha2code,alpha3code, code, currencyid, country_calling_code)
VALUES ('Svalbard & Jan Mayen Islands', '1000', '9999999999', '123', '', 2,'SJ', 'SJM', 774, 578, 47);

INSERT INTO system.country_tbl(name, minmob, maxmob, channel, priceformat, decimals, alpha2code,alpha3code, code, currencyid, country_calling_code)
VALUES ('Eswatini', '1000', '9999999999', '123', '', 2,'SZ', 'SWZ', 748, 748, 268);

INSERT INTO system.country_tbl(name, minmob, maxmob, channel, priceformat, decimals, alpha2code,alpha3code, code, currencyid, country_calling_code)
VALUES ('Tajikistan', '1000', '9999999999', '123', '', 2,'TJ', 'TJK', 762, 972, 992);

INSERT INTO system.country_tbl(name, minmob, maxmob, channel, priceformat, decimals, alpha2code,alpha3code, code, currencyid, country_calling_code)
VALUES ('Timor-Leste', '1000', '9999999999', '123', '', 2,'TL', 'TLS', 626, 840, 670);

INSERT INTO system.country_tbl(name, minmob, maxmob, channel, priceformat, decimals, alpha2code,alpha3code, code, currencyid, country_calling_code)
VALUES ('Tokelau', '1000', '9999999999', '123', '', 2,'TK', 'TKL', 772, 554, 690);

INSERT INTO system.country_tbl(name, minmob, maxmob, channel, priceformat, decimals, alpha2code,alpha3code, code, currencyid, country_calling_code)
VALUES ('US Virgin Islands', '1000', '9999999999', '123', '', 2,'VI', 'VIR', 850, 840, 1);

INSERT INTO system.country_tbl(name, minmob, maxmob, channel, priceformat, decimals, alpha2code,alpha3code, code, currencyid, country_calling_code)
VALUES ('Serbia and Montenegro', '1000', '9999999999', '123', '', 2,'CS', 'SCG', 891, 978, 381);

INSERT INTO system.country_tbl(name, minmob, maxmob, channel, priceformat, decimals, alpha2code,alpha3code, code, currencyid, country_calling_code)
VALUES ('Cote DIvoire', '1000', '9999999999', '123', '', 0,'CI', 'CIV', 384, 952, 225);

INSERT INTO system.country_tbl(name, minmob, maxmob, channel, priceformat, alpha2code,alpha3code, code, country_calling_code)
VALUES ('South Georgia and the South Sandwich Islands', '1000', '9999999999', '123', '', 'GS', 'SGS', 239, 500);

INSERT INTO system.country_tbl(name, minmob, maxmob, channel, priceformat, decimals, alpha2code,alpha3code, code, currencyid, country_calling_code)
VALUES ('Saint Barthelemy', '1000', '9999999999', '123', '', 2,'BL', 'BLM', 652, 978, 590);

INSERT INTO system.country_tbl(name, minmob, maxmob, channel, priceformat, decimals, alpha2code,alpha3code, code, currencyid, country_calling_code)
VALUES ('Bonaire, Saint Eustatius and Saba', '1000', '9999999999', '123', '', 2,'BQ', 'BES', 535, 840, 599);

INSERT INTO system.country_tbl(name, minmob, maxmob, channel, priceformat, decimals, alpha2code,alpha3code, code, currencyid, country_calling_code)
VALUES ('Curacao', '1000', '9999999999', '123', '', 2,'CW', 'CUW', 531, 532, 599);

INSERT INTO system.country_tbl(name, minmob, maxmob, channel, priceformat, decimals, alpha2code,alpha3code, code, currencyid, country_calling_code)
VALUES ('Guernsey', '1000', '9999999999', '123', '', 2,'GG', 'GGY', 831, 826, 44);

INSERT INTO system.country_tbl(name, minmob, maxmob, channel, priceformat, decimals, alpha2code,alpha3code, code, currencyid, country_calling_code)
VALUES ('Jersey', '1000', '9999999999', '123', '', 2,'JE', 'JEY', 832, 826, 44);

INSERT INTO system.country_tbl(name, minmob, maxmob, channel, priceformat, decimals, alpha2code,alpha3code, code, currencyid, country_calling_code)
VALUES ('Saint Martin', '1000', '9999999999', '123', '', 2,'MF', 'MAF', 663, 978, 590);

INSERT INTO system.country_tbl(name, minmob, maxmob, channel, priceformat, decimals, alpha2code,alpha3code, code, currencyid, country_calling_code)
VALUES ('South Sudan', '1000', '9999999999', '123', '', 2,'SS', 'SSD', 728, 728, 221);

UPDATE System.Country_Tbl SET code = 704, decimals = 2, alpha2code = 'VN', alpha3code = 'VNM', country_calling_code = 84 WHERE id = 649 AND currencyid = 704;

UPDATE System.Country_Tbl SET code = 383, decimals = 2, alpha2code = 'XK', alpha3code = 'XKX', country_calling_code = 383 WHERE id = 148 AND currencyid = 978;

UPDATE System.Country_Tbl SET code = 398, alpha2code = 'KZ', alpha3code = 'KAZ', country_calling_code = 7 WHERE id = 633 AND currencyid = 398;

UPDATE System.Country_Tbl SET code = 410, alpha2code = 'KR', alpha3code = 'KOR', country_calling_code = 82 WHERE id = 632 AND currencyid = 410;

UPDATE System.Country_Tbl SET code = 408, alpha2code = 'KP', alpha3code = 'PRK', country_calling_code = 850 WHERE id = 631 AND currencyid = 408;

UPDATE System.Country_Tbl SET code = 104, decimals = 2, alpha2code = 'BU', alpha3code = 'BUR', country_calling_code = 95 WHERE id = 625 AND currencyid = 104;


INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) select 'MAX_DOWNLOAD_FILE_LIMIT', '2', id, 'merchant', from client.merchantaccount_tbl WHERE clientid=10069 AND pspid=52;

INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (2004, '3ds Card Not Enrolled', 'Payment', '');

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

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'mpi_rule', 'isProceedAuth::=<status>=="2"OR<status>=="5"OR<status>=="6"
status::=(additional-data.param[@name=''status''])', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10077 AND pspid=4;