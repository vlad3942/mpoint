--Delete enterie for cardid (40) alipay chinese--
DELETE FROM System.PSPCard_Tbl WHERE pspid = 40 AND cardid = 40;
DELETE FROM Client.CardAccess_Tbl WHERE clientid = 10020 AND cardid = 40 AND pspid = 40;
DELETE FROM system.cardpricing_tbl WHERE cardid = 40 and pricepointid IN (-702,-840,-784,-36,-48,-124,-156,-344,-360,-356,-392,-408,-410,-414,-446,-458,-554,-598,-608,-634,-682,-702,-764,-949,-901,-156,-826);


-- Setup for 2c2p-alc with unionpay--

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 826;


-- Setup for 2c2p-alc with alipay--
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 32, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 826;

-- common queries for alipay and unionpay on UAT/SIT enviourment with client id - 100200 --
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10020, PC.cardid, PC.pspid, '103' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,32) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10020, PC.cardid, PC.pspid, '632' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,32) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;

INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (826,40,'GBP');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (410,40,'KRW');

