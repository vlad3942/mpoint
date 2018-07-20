
/**=====  Entries for Papua New Guenea =====  **/
INSERT INTO System.Country_Tbl (id, name,  minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (651, 'Papua New Guinea', '1000', '9999999999', '123', '', 0, '0');
UPDATE System.Country_Tbl SET alpha2code = 'PG', alpha3code = 'PGK', code = 598, currencyid = 598 WHERE id = 651;
INSERT INTO system.cardpricing_tbl (cardid,pricepointid,enabled) values (8,-598,'t');
INSERT INTO system.cardpricing_tbl (cardid,pricepointid,enabled) values (7,-598,'t');
-- Make sure below entry does not exist already, duplicate causes failure in payments
INSERT INTO System.PSPCurrency_Tbl (pspid,currencyid,enabled) Values (40,598,'t');
INSERT INTO Client.CardAccess_Tbl (countryid, clientid, cardid, pspid,enabled) values (651,10055, 7, 40, 't' );
INSERT INTO Client.CardAccess_Tbl (countryid, clientid, cardid, pspid,enabled) values (651,10055, 8, 40, 't' );