
-- Setup for 2c2p-alc with unionpay--

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 826;


-- Setup for 2c2p-alc with alipay--
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 32, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 826;

-- common queries for alipay and unionpay on UAT/SIT enviourment with client id - 100200 --
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10020, PC.cardid, PC.pspid, '103' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,32) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10020, PC.cardid, PC.pspid, '632' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,32) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;



INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (826,40,'GBP');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (410,40,'KRW');

