-- DMTC-3229: Configure mPoint to block Dankort for DOT
UPDATE Client.CardAccess_Tbl SET stateid = 2 WHERE clientid IN (10019) AND cardid = 2;
INSERT INTO Client.IINList_Tbl (clientid, iinactionid, min, max) VALUES (10019, 1, 5019, 5019);

-- CMP-1874: Create new card type: VISA / Dankort
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength) SELECT 37, 'VISA / Dankort', position, minlength, maxlength, cvclength FROM System.Card_Tbl WHERE id = 2;
INSERT INTO System.PSPCard_Tbl (cardid, pspid) SELECT 37, pspid FROM System.PSPCard_Tbl WHERE cardid = 2;

-- Set prefix for new card type Visa / Dankort
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (37, 4571, 4571);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 37, pricepointid FROM System.CardPricing_Tbl WHERE cardid = 2; 

-- Enable card type Visa/Dankort (2) for DOT Client
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, position)
  SELECT clientid, 37, pspid, countryid, position FROM Client.CardAccess_Tbl WHERE cardid = 2 AND clientid = 10019;

-- Disable card type Dankort (2) for DOT Client
UPDATE Client.CardAccess_Tbl SET enabled = false WHERE cardid = 2 AND clientid = 10019;

-- Update existing stored Visa/Dankort cards for DOT clients
UPDATE EndUser.Card_Tbl SET cardid = 37 WHERE cardid IN
(SELECT C.id from Enduser.Card_Tbl C
 INNER JOIN Enduser.CLAccess_Tbl CA ON C.accountid = CA.accountid
 WHERE CA.accountid = 10019 AND C.cardid = 2 AND C.mask LIKE '4571%');
