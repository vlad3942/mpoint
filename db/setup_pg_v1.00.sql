/**
 * Setup SQL script for the PostGreSQL databse.
 * The file include any necesarry queries to populate an empty database with initial configuration data
 */
 
INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel) VALUES (20, 'Denmark', 'DKK', '10000000', '99999999', '1230');

INSERT INTO System.PSP_Tbl (name) VALUES ('DIBS');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) SELECT 20, Max(id), 208 FROM System.PSP_Tbl;