-- Paytabs Start

-- IF not mechant account is exsit
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 38, 'Paytabs', '10028311', 'wTMJj6gbalSZdnSWzdMF6m0Q1dkkFzlct7WMecXoyQoLegIOuutBRurTHMemlzyPHuCVHAkfqI1EZJtteX45rwZ8iduEM3tuy5qf');
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (100007, 38, '-1', true);

-- IF mechant account is exsit
UPDATE client.merchantaccount_tbl SET username = '10028311', passwd = 'wTMJj6gbalSZdnSWzdMF6m0Q1dkkFzlct7WMecXoyQoLegIOuutBRurTHMemlzyPHuCVHAkfqI1EZJtteX45rwZ8iduEM3tuy5qf' WHERE clientid = 10007  AND pspid = 38;

-- KNet Start
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (48,38,'BHD');

INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (70, 'BENEFIT', 23, -1, -1, -1,4);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (70, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 70, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 48;

INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position, psp_type) VALUES (10007, 70, true, 38, 601, 1, null, 1);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (70, 38, true);

-- KNet End

-- BENEFIT Start

INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (414,38,'KWD');

INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (69, 'KNet', 23, -1, -1, -1,4);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (69, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 69, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 414;

INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position, psp_type) VALUES (10007, 69, true, 38, 604, 1, null, 1);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (69, 38, true);

-- BENEFIT Stop

-- Paytabs Stop