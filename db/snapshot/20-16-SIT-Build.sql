-------------G-CASH 2C2P-ALC FOR CEBU------------
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (95, 'Gcash', 23, -1, -1, -1, 3);
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (40, 95);
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) SELECT 10077, PC.cardid, PC.pspid 
FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (95,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 95, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 608;
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (608,40,'PHP');