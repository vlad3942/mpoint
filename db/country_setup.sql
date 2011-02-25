INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (103, 'UK', 'GBP', '1000000000', '9999999999', '123', '{CURRENCY}{PRICE}', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-103, 103, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -103, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (103, 4, 'GBP');