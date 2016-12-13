UPDATE Client.Client_Tbl SET salt = 'Fh17_8sFgd' WHERE id = 10005;
UPDATE Client.Client_Tbl SET salt = 'Fh17_8sFgd' WHERE id = 10014;
UPDATE Client.Client_Tbl SET salt = '8sFgd_Fh17' WHERE id = 10019;


/**
 * CMP-1146 Support "Payment Settled" state for mConsole Search transaction API
 */ 
INSERT INTO Log.State_Tbl (id, name) VALUES (2020, 'Payment Settled');


/* ========== Global Configuration for MobilePay Online- Payment Method : START========== */
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength) VALUES (30, 'MobilePay Online', 23, -1, -1, -1);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (30, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 30, id FROM System.PricePoint_Tbl WHERE amount = -1 AND countryid = 103;

INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (30, 24);
/* ========== Global Configuration for MobilePay Online- Payment Method : END========== */