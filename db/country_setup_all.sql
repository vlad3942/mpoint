--mPoint's System.Country_Tbl needs to be modified so that the column "name" 
--allows 100 characters (e.g. "French Departments and Territories in the Indian Ocean"), 
--and "currency" be aligned with GoMobile's 5 characters, instead of 3, to accomodate currencies like "kuna" and "peso". 
--This also goes for the "currency" column in mPoint's System.PSPCurrency_Tbl.

INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (105, 'Afghanistan', 'AFN', '10000000', '99999999', '123', 'AFN', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-105, 105, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -105, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (105, 9, 'AFN');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (106, 'Albania', 'lek', '10000000', '99999999', '123', 'lek', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-106, 106, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -106, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (106, 9, 'lek');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (107, 'Andorra', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-107, 107, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -107, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (107, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (108, 'Austria', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-108, 108, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -108, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (108, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (109, 'Belarus', 'BYR', '10000000', '99999999', '123', 'BYR', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-109, 109, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -109, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (109, 9, 'BYR');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (110, 'Belgium', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-110, 110, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -110, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (110, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (111, 'Bosnia  Herzegovina', 'BAM', '10000000', '99999999', '123', 'BAM', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-111, 111, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -111, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (111, 9, 'BAM');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (112, 'Bulgaria', 'BGN', '10000000', '99999999', '123', 'BGN', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-112, 112, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -112, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (112, 9, 'BGN');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (113, 'Croatia', 'kuna', '10000000', '99999999', '123', 'kuna', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-113, 113, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -113, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (113, 9, 'kuna');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (114, 'Cyprus', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-114, 114, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -114, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (114, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (115, 'Czech Republic', 'CZK', '10000000', '99999999', '123', 'CZK', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-115, 115, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -115, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (115, 9, 'CZK');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (116, 'Estonia', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-116, 116, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -116, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (116, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (117, 'Faroe Islands', 'kr', '10000000', '99999999', '123', 'kr', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-117, 117, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -117, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (117, 9, 'kr');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (118, 'France', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-118, 118, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -118, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (118, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (119, 'Georgia', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-119, 119, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -119, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (119, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (120, 'Germany', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-120, 120, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -120, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (120, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (121, 'Gibraltar', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-121, 121, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -121, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (121, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (122, 'Greece', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-122, 122, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -122, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (122, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (123, 'Greenland', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-123, 123, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -123, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (123, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (124, 'Hungary', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-124, 124, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -124, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (124, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (125, 'Iceland', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-125, 125, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -125, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (125, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (126, 'Ireland', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-126, 126, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -126, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (126, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (127, 'Italy', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-127, 127, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -127, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (127, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (128, 'Latvia', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-128, 128, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -128, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (128, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (129, 'Liechtenstein', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-129, 129, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -129, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (129, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (130, 'Lithuania', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-130, 130, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -130, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (130, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (131, 'Luxembourg', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-131, 131, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -131, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (131, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (132, 'Malta', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-132, 132, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -132, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (132, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (133, 'Moldova', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-133, 133, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -133, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (133, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (201, 'Mexico', '$', '1000000000', '9999999999', '123', '$', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-201, 201, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -201, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (201, 9, '$');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (202, 'Canada', '$', '1000000000', '9999999999', '123', '$', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-202, 202, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -202, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (202, 9, '$');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (203, 'Anguilla', 'XCD', '10000000', '99999999', '123', 'XCD', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-203, 203, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -203, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (203, 9, 'XCD');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (204, 'Antigua and Barbuda', 'XCD', '10000000', '99999999', '123', 'XCD', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-204, 204, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -204, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (204, 9, 'XCD');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (205, 'Barbados', 'BBD', '10000000', '99999999', '123', 'BBD', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-205, 205, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -205, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (205, 9, 'BBD');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (206, 'British Virgin Islands', '$', '10000000', '99999999', '123', '$', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-206, 206, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -206, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (206, 9, '$');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (207, 'Cayman Islands', 'KYD', '10000000', '99999999', '123', 'KYD', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-207, 207, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -207, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (207, 9, 'KYD');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (208, 'Cuba', 'peso', '1000000000', '9999999999', '123', 'peso', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-208, 208, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -208, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (208, 9, 'peso');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (209, 'Dominican Republic', 'peso', '1000000000', '9999999999', '123', 'peso', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-209, 209, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -209, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (209, 9, 'peso');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (210, 'Guadeloupe', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-210, 210, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -210, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (210, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (211, 'Haiti', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-211, 211, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -211, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (211, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (212, 'Jamaica', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-212, 212, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -212, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (212, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (300, 'Algeria', 'DZD', '10000000', '99999999', '123', 'DZD', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-300, 300, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -300, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (300, 9, 'DZD');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (301, 'Angola', 'AOA', '10000000', '99999999', '123', 'AOA', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-301, 301, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -301, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (301, 9, 'AOA');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (302, 'Bahrain', 'BHD', '10000000', '99999999', '123', 'BHD', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-302, 302, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -302, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (302, 9, 'BHD');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (303, 'Bangladesh', 'BDT', '1000000000', '9999999999', '123', 'BDT', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-303, 303, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -303, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (303, 9, 'BDT');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (304, 'Benin', 'XOF', '10000000', '99999999', '123', 'XOF', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-304, 304, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -304, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (304, 9, 'XOF');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (305, 'Bolivia', 'BOB', '10000000', '99999999', '123', 'BOB', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-305, 305, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -305, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (305, 9, 'BOB');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (306, 'Botswana', 'BWP', '10000000', '99999999', '123', 'BWP', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-306, 306, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -306, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (306, 9, 'BWP');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (307, 'Burkina Faso', 'XOF', '10000000', '99999999', '123', 'XOF', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-307, 307, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -307, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (307, 9, 'XOF');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (308, 'Burundi', 'BIF', '10000000', '99999999', '123', 'BIF', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-308, 308, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -308, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (308, 9, 'BIF');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (309, 'Cameroon', 'XAF', '10000000', '99999999', '123', 'XAF', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-309, 309, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -309, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (309, 9, 'XAF');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (310, 'Cape Verde', 'CVE', '10000000', '99999999', '123', 'CVE', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-310, 310, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -310, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (310, 9, 'CVE');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (311, 'Central African Republic', 'XAF', '10000000', '99999999', '123', 'XAF', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-311, 311, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -311, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (311, 9, 'XAF');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (312, 'Chad', 'XAF', '10000000', '99999999', '123', 'XAF', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-312, 312, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -312, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (312, 9, 'XAF');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (313, 'Comoros', 'KMF', '10000000', '99999999', '123', 'KMF', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-313, 313, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -313, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (313, 9, 'KMF');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (314, 'Congo', 'XAF', '10000000', '99999999', '123', 'XAF', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-314, 314, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -314, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (314, 9, 'XAF');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (315, 'Côte d''Ivoire', 'XOF', '10000000', '99999999', '123', 'XOF', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-315, 315, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -315, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (315, 9, 'XOF');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (316, 'Democratic Republic of the Congo', 'CDF', '10000000', '99999999', '123', 'CDF', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-316, 316, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -316, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (316, 9, 'CDF');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (317, 'Djibouti', 'DJF', '10000000', '99999999', '123', 'DJF', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-317, 317, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -317, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (317, 9, 'DJF');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (318, 'Egypt', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-318, 318, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -318, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (318, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (319, 'Equatorial Guinea', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-319, 319, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -319, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (319, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (320, 'Ethiopia', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-320, 320, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -320, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (320, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (321, 'Gabon', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-321, 321, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -321, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (321, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (322, 'Gambia', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-322, 322, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -322, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (322, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (323, 'Ghana', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-323, 323, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -323, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (323, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (324, 'Guinea', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-324, 324, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -324, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (324, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (325, 'Guinea-Bissau', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-325, 325, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -325, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (325, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (326, 'Kenya', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-326, 326, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -326, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (326, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (327, 'Lesotho', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-327, 327, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -327, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (327, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (328, 'Liberia', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-328, 328, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -328, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (328, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (329, 'Madagascar', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-329, 329, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -329, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (329, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (330, 'Malawi', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-330, 330, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -330, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (330, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (331, 'Mali', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-331, 331, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -331, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (331, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (332, 'Mauritania', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-332, 332, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -332, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (332, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (333, 'Mauritius', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-333, 333, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -333, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (333, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (334, 'Morocco', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-334, 334, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -334, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (334, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (335, 'Mozambique', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-335, 335, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -335, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (335, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (400, 'Argentina', 'peso', '1000000000', '9999999999', '123', 'peso', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-400, 400, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -400, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (400, 9, 'peso');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (401, 'Aruba', 'AWG', '10000000', '99999999', '123', 'AWG', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-401, 401, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -401, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (401, 9, 'AWG');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (402, 'Belize', 'BZD', '10000000', '99999999', '123', 'BZD', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-402, 402, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -402, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (402, 9, 'BZD');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (403, 'Brazil', 'R$', '1000000000', '9999999999', '123', 'R$', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-403, 403, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -403, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (403, 9, 'R$');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (404, 'Chile', 'peso', '1000000000', '9999999999', '123', 'peso', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-404, 404, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -404, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (404, 9, 'peso');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (405, 'Colombia', 'peso', '1000000000', '9999999999', '123', 'peso', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-405, 405, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -405, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (405, 9, 'peso');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (406, 'Costa Rica', 'CRC', '1000000000', '9999999999', '123', 'CRC', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-406, 406, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -406, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (406, 9, 'CRC');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (407, 'Ecuador', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-407, 407, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -407, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (407, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (408, 'El Salvador', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-408, 408, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -408, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (408, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (409, 'French Departments and Territories', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-409, 409, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -409, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (409, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (410, 'Guatemala', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-410, 410, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -410, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (410, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (411, 'Guyana', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-411, 411, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -411, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (411, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (412, 'Honduras', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-412, 412, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -412, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (412, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (500, 'Australia', 'AUD', '1000000000', '9999999999', '123', 'AUD', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-500, 500, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -500, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (500, 9, 'AUD');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (501, 'Brunei Darussalam', 'BND', '10000000', '99999999', '123', 'BND', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-501, 501, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -501, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (501, 9, 'BND');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (502, 'Cook Islands', 'NZD', '10000000', '99999999', '123', 'NZD', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-502, 502, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -502, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (502, 9, 'NZD');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (503, 'Fiji', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-503, 503, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -503, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (503, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (504, 'French Polynesia', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-504, 504, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -504, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (504, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (505, 'Indonesia', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-505, 505, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -505, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (505, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (506, 'Micronesia', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-506, 506, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -506, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (506, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (600, 'Azerbaijan', 'AZN', '10000000', '99999999', '123', 'AZN', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-600, 600, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -600, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (600, 9, 'AZN');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (601, 'Bhutan', 'BTN', '10000000', '99999999', '123', 'BTN', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-601, 601, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -601, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (601, 9, 'BTN');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (602, 'Cambodia', 'KHR', '10000000', '99999999', '123', 'KHR', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-602, 602, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -602, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (602, 9, 'KHR');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (603, 'China', 'TWD', '1000000000', '9999999999', '123', 'TWD', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-603, 603, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -603, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (603, 9, 'TWD');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (604, 'Hong Kong', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-604, 604, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -604, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (604, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (605, 'Iran', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-605, 605, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -605, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (605, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (606, 'India', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-606, 606, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -606, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (606, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (607, 'Israel', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-607, 607, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -607, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (607, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (608, 'Japan', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-608, 608, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -608, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (608, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (609, 'Jordan', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-609, 609, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -609, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (609, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (610, 'Kazakhstan', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-610, 610, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -610, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (610, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (611, 'Kuwait', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-611, 611, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -611, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (611, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (612, 'Kyrgyzstan', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-612, 612, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -612, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (612, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (613, 'Lao P.D.R.', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-613, 613, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -613, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (613, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (614, 'Maldives', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-614, 614, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -614, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (614, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (615, 'Mongolia', '', '10000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-615, 615, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -615, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (615, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (701, 'United Arab Emirates', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-701, 701, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -701, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (701, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (702, 'Armenia', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-702, 702, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -702, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (702, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (703, 'Netherlands Antilles', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-703, 703, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -703, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (703, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (704, 'Antarctica', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-704, 704, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -704, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (704, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (705, 'American Samoa', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-705, 705, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -705, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (705, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (706, 'Bermuda', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-706, 706, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -706, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (706, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (707, 'Bahamas', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-707, 707, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -707, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (707, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (708, 'Bouvet Island', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-708, 708, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -708, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (708, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (709, 'Scott Base', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-709, 709, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -709, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (709, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (710, 'Cocos (Keeling) Islands', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-710, 710, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -710, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (710, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (711, 'Tristan Da Cunha', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-711, 711, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -711, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (711, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (712, 'Christmas Island', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-712, 712, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -712, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (712, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (713, 'Diego Garcia', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-713, 713, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -713, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (713, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (714, 'Dominica', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-714, 714, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -714, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (714, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (715, 'Western Sahara', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-715, 715, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -715, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (715, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (716, 'Eritrea', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-716, 716, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -716, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (716, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (717, 'Falkland Islands', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-717, 717, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -717, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (717, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (718, 'Grenada', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-718, 718, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -718, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (718, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (719, 'French Guiana', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-719, 719, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -719, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (719, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (720, 'South Georgia And IS', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-720, 720, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -720, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (720, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (721, 'Guam', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-721, 721, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -721, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (721, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (722, 'Isle of Man', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-722, 722, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -722, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (722, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (723, 'British Int Ocean Tertry', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-723, 723, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -723, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (723, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (724, 'Iraq', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-724, 724, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -724, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (724, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (725, 'Johnston Island', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-725, 725, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -725, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (725, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (726, 'Kerguelen Archipelago', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-726, 726, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -726, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (726, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (727, 'Kiribati', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-727, 727, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -727, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (727, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (728, 'Kaliningrad', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-728, 728, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -728, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (728, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (729, 'St. Christopher (St. Kitts) Nevis', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-729, 729, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -729, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (729, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (730, 'Korea, Democratic Peoples Republic of', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-730, 730, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -730, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (730, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (731, 'Republic of Korea', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-731, 731, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -731, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (731, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (732, 'Lao Peoples Democratic Republic', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-732, 732, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -732, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (732, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (733, 'St. Lucia', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-733, 733, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -733, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (733, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (734, 'Sri Lanka', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-734, 734, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -734, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (734, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (735, 'Libyan Arab Jamahiriya', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-735, 735, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -735, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (735, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (736, 'Monaco', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-736, 736, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -736, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (736, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (737, 'Montenegro', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-737, 737, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -737, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (737, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (738, 'Marshall Islands', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-738, 738, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -738, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (738, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (739, 'Midway Island', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-739, 739, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -739, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (739, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (740, 'Republic of Macedonia', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-740, 740, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -740, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (740, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (741, 'Myanmar, Union of', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-741, 741, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -741, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (741, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (742, 'Macau', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-742, 742, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -742, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (742, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (743, 'Northern Mariana Islands', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-743, 743, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -743, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (743, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (744, 'Martinique', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-744, 744, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -744, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (744, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (745, 'Monserrat', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-745, 745, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -745, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (745, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (746, 'Malaysia', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-746, 746, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -746, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (746, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (747, 'Namibia', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-747, 747, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -747, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (747, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (748, 'New Caledonia', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-748, 748, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -748, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (748, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (749, 'Not Defined', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-749, 749, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -749, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (749, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (750, 'Niger', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-750, 750, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -750, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (750, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (751, 'Norfolk Island', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-751, 751, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -751, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (751, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (752, 'Nigeria', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-752, 752, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -752, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (752, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (753, 'Nicaragua', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-753, 753, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -753, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (753, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (754, 'Nepal', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-754, 754, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -754, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (754, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (755, 'Nauru', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-755, 755, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -755, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (755, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (756, 'Niue', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-756, 756, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -756, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (756, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (757, 'New Zealand', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-757, 757, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -757, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (757, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (758, 'Carriacou', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-758, 758, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -758, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (758, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (759, 'Panama', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-759, 759, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -759, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (759, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (760, 'Peru', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-760, 760, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -760, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (760, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (761, 'Philippines', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-761, 761, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -761, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (761, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (762, 'St. Pierre and Miquelon', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-762, 762, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -762, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (762, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (763, 'Pitcairn Island', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-763, 763, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -763, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (763, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (764, 'Puerto Rico', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-764, 764, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -764, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (764, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (765, 'Palestinian Territory', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-765, 765, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -765, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (765, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (766, 'Portugal', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-766, 766, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -766, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (766, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (767, 'Palau', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-767, 767, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -767, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (767, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (768, 'Paraguay', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-768, 768, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -768, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (768, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (769, 'Qatar', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-769, 769, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -769, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (769, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (770, 'Reunion', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-770, 770, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -770, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (770, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (771, 'Kosovo', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-771, 771, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -771, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (771, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (772, 'Romania', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-772, 772, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -772, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (772, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (773, 'Republic of Serbia', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-773, 773, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -773, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (773, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (774, 'Rwanda', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-774, 774, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -774, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (774, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (775, 'Solomon Islands', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-775, 775, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -775, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (775, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (776, 'Seychelles', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-776, 776, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -776, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (776, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (777, 'Sudan', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-777, 777, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -777, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (777, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (778, 'Singapore', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-778, 778, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -778, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (778, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (779, 'St. Helena', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-779, 779, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -779, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (779, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (780, 'Slovenia', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-780, 780, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -780, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (780, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (781, 'Slovakia', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-781, 781, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -781, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (781, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (782, 'Sierra Leone', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-782, 782, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -782, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (782, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (783, 'San Marino', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-783, 783, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -783, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (783, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (784, 'Senegal', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-784, 784, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -784, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (784, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (785, 'Somalia', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-785, 785, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -785, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (785, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (786, 'Suriname', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-786, 786, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -786, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (786, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (787, 'Sao Tome and Principe', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-787, 787, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -787, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (787, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (788, 'St. Maarten', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-788, 788, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -788, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (788, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (789, 'Syrian Arab Republic', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-789, 789, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -789, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (789, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (790, 'Swaziland', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-790, 790, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -790, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (790, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (791, 'Turks and Caicos Islands', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-791, 791, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -791, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (791, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (792, 'Togo', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-792, 792, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -792, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (792, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (793, 'Thailand', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-793, 793, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -793, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (793, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (794, 'Turkmenistan', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-794, 794, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -794, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (794, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (795, 'Tunisia', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-795, 795, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -795, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (795, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (796, 'Tonga', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-796, 796, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -796, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (796, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (797, 'Turkey', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-797, 797, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -797, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (797, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (798, 'Trinidad and Tobago', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-798, 798, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -798, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (798, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (799, 'Tuvalu', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-799, 799, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -799, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (799, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (800, 'Taiwan', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-800, 800, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -800, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (800, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (801, 'Tanzania', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-801, 801, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -801, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (801, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (802, 'Ukraine', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-802, 802, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -802, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (802, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (803, 'Uganda', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-803, 803, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -803, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (803, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (804, 'United States Minor Outlying Islands', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-804, 804, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -804, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (804, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (805, 'United States', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-805, 805, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -805, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (805, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (806, 'Unserviced Destn', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-806, 806, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -806, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (806, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (807, 'Uruguay', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-807, 807, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -807, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (807, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (808, 'Uzbekistan', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-808, 808, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -808, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (808, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (809, 'Vatican City State', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-809, 809, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -809, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (809, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (810, 'St. Vincent and The Grenadines', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-810, 810, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -810, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (810, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (811, 'Venezuela', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-811, 811, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -811, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (811, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (812, 'Virgin Islands, United States', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-812, 812, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -812, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (812, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (813, 'Viet Nam', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-813, 813, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -813, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (813, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (814, 'Vanuatu', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-814, 814, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -814, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (814, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (815, 'Wallis and Futuna Islands', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-815, 815, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -815, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (815, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (816, 'Samoa', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-816, 816, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -816, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (816, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (817, 'Yemen', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-817, 817, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -817, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (817, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (818, 'Mayotte', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-818, 818, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -818, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (818, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (819, 'Yugoslavia', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-819, 819, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -819, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (819, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (820, 'South Africa', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-820, 820, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -820, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (820, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (821, 'Zambia', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-821, 821, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -821, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (821, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (822, 'Zaire', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-822, 822, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -822, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (822, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (823, 'Zimbabwe', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-823, 823, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -823, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (823, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (824, 'Switzerland', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-824, 824, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -824, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (824, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (825, 'Spain', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-825, 825, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -825, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (825, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (826, 'Finland', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-826, 826, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -826, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (826, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (827, 'Netherlands', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-827, 827, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -827, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (827, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (828, 'Oman', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-828, 828, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -828, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (828, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (829, 'Papua New Guinea', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-829, 829, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -829, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (829, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (830, 'Pakistan', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-830, 830, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -830, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (830, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (831, 'Poland', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-831, 831, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -831, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (831, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (832, 'Russia', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-832, 832, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -832, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (832, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (833, 'Saudi Arabia', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-833, 833, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -833, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (833, 9, '');


INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (834, 'Lebanon', '', '1000000000', '9999999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-834, 834, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -834, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (834, 9, '');


