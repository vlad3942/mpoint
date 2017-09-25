-- DMTC-3229: Configure mPoint to block Dankort for DOT and DSB
UPDATE Client.CardAccess_Tbl SET stateid = 2 WHERE clientid IN (10005, 10019) AND cardid = 2;

INSERT INTO Client.IINList_Tbl (clientid, iinactionid, min, max) VALUES (10005, 1, 5019, 5019);
INSERT INTO Client.IINList_Tbl (clientid, iinactionid, min, max) VALUES (10019, 1, 5019, 5019);
-- CMP-1874: Create new card type: VISA / Dankort
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength) SELECT 37, 'VISA / Dankort', position, minlength, maxlength, cvclength FROM System.Card_Tbl WHERE id = 2; 
INSERT INTO System.PSPCard_Tbl (cardid, pspid) SELECT 37, pspid FROM System.PSPCard_Tbl WHERE cardid = 2;
UPDATE System.CardPrefix_Tbl SET cardid = 37 WHERE cardid = 2 AND min = 4571 AND max = 4571;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 37, pricepointid FROM System.CardPricing_Tbl WHERE cardid = 2; 
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, position) SELECT clientid, 37, pspid, countryid, position FROM Client.CardAccess_Tbl WHERE cardid = 2;
UPDATE EndUser.Card_Tbl SET cardid = 37 WHERE cardid = 2 AND mask LIKE '4571%';
