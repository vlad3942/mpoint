INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, als) VALUES (101, 'Sweden', 'kr', '10000000', '99999999', '72790', '{PRICE}{CURRENCY}', 0, true);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-101, 101, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -101, cardid FROM System.CardPricing_Tbl WHERE pricepointid = -100;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (101, 2, '752');