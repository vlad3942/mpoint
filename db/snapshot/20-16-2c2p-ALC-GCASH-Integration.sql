-------------G-CASH 2C2P-ALC FOR CEBU------------
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) SELECT <client ID>, PC.cardid, PC.pspid FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (93,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;

-------------G-CASH 2C2P-ALC FOR CEBU------------
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (93, 'Gcash', 23, -1, -1, -1, 3);
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (40, 93);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 93, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 608;
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (608,40,'PHP');

 


