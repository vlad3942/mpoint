UPDATE Client.Client_Tbl SET salt = 'Fh17_8sFgd' WHERE id = 10005;
UPDATE Client.Client_Tbl SET salt = 'Fh17_8sFgd' WHERE id = 10014;
UPDATE Client.Client_Tbl SET salt = '8sFgd_Fh17' WHERE id = 10019;

/**
 * CMP-917
 */
INSERT INTO System.Type_Tbl (id, name) VALUES (10091, 'New Card Purchase');
/**
 * CMP-999
 */
UPDATE system.country_tbl SET symbol='Kr.' WHERE id = 100;
/**
 *CMC-3289
 */
Delete from system.cardpricing_tbl c where cardid=16
Insert into system.cardpricing_tbl  (cardid , pricepointid) VALUES (16,-200);
Insert into system.cardpricing_tbl  (cardid , pricepointid) SELECT 16 , id from system.pricepoint_tbl  where countryid =500;
Insert into system.cardpricing_tbl  (cardid , pricepointid) SELECT 16 , id from system.pricepoint_tbl  where countryid =202;
Insert into system.cardpricing_tbl  (cardid , pricepointid) SELECT 16 , id from system.pricepoint_tbl  where countryid =400;
Insert into system.cardpricing_tbl  (cardid , pricepointid) SELECT 16 , id from system.pricepoint_tbl  where countryid =403;
Insert into system.cardpricing_tbl  (cardid , pricepointid) SELECT 16 , id from system.pricepoint_tbl  where countryid =404;
Insert into system.cardpricing_tbl  (cardid , pricepointid) SELECT 16 , id from system.pricepoint_tbl  where countryid =609;
Insert into system.cardpricing_tbl  (cardid , pricepointid) SELECT 16 , id from system.pricepoint_tbl  where countryid =405;
Insert into system.cardpricing_tbl  (cardid , pricepointid) SELECT 16 , id from system.pricepoint_tbl  where countryid =614;
Insert into system.cardpricing_tbl  (cardid , pricepointid) SELECT 16 , id from system.pricepoint_tbl  where countryid =429;
Insert into system.cardpricing_tbl  (cardid , pricepointid) SELECT 16 , id from system.pricepoint_tbl  where countryid =638;
Insert into system.cardpricing_tbl  (cardid , pricepointid) SELECT 16 , id from system.pricepoint_tbl  where countryid =201;
Insert into system.cardpricing_tbl  (cardid , pricepointid) SELECT 16 , id from system.pricepoint_tbl  where countryid =513;
Insert into system.cardpricing_tbl  (cardid , pricepointid) SELECT 16 , id from system.pricepoint_tbl  where countryid =642;
Insert into system.cardpricing_tbl  (cardid , pricepointid) SELECT 16 , id from system.pricepoint_tbl  where countryid =355;
Insert into system.cardpricing_tbl  (cardid , pricepointid) SELECT 16 , id from system.pricepoint_tbl  where countryid =647;
