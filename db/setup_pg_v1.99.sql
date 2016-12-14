
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

/***
 * CMP-1030
 */
INSERT INTO System.CardChargeType_Tbl(id, name) VALUES
(4, 'CHARGE'),
(5, 'DEFERRED_DEBIT'),
(6, 'NONE');

/***
 * CMP-1041
 */
INSERT INTO System.PspCard_Tbl(cardid, pspid) VALUES (21, 9);
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10001, 21, 9);
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 9, '-1');


/**
 * EKW-517
 */
UPDATE System.Country_Tbl SET decimals = 3 WHERE currency = 'OMR' and id = 605;
UPDATE System.Country_Tbl SET decimals = 3 WHERE currency = 'KWD' and id = 604;

/**
 * CMP-1100
 */
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 116;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 117;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 300;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 213;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 118;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 301;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 203;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 204;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 400;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 119;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 401;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 500;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 114;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 611;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 215;
UPDATE System.COUNTRY_TBL SET decimals = 3 WHERE id = 601;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 302;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 205;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 111;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 402;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 214;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 612;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 304;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 121;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 305;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 416;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 403;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 423;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 501;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 122;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 309;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 613;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 202;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 207;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 609;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 508;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 507;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 405;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 315;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 502;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 406;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 123;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 208;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 124;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 125;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 100;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 418;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 209;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 407;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 317;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 408;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 337;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 126;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 319;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 127;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 503;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 104;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 108;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 421;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 409;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 321;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 115;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 322;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 129;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 105;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 130;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 420;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 210;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 627;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 410;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 411;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 211;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 412;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 614;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 131;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 603;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 505;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 615;
UPDATE System.COUNTRY_TBL SET decimals = 3 WHERE id = 628;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 133;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 134;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 106;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 107;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 212;
UPDATE System.COUNTRY_TBL SET decimals = 3 WHERE id = 617;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 633;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 325;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 630;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 631;
UPDATE System.COUNTRY_TBL SET decimals = 3 WHERE id = 604;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 619;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 621;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 326;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 327;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 136;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 138;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 636;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 328;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 329;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 638;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 623;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 139;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 635;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 426;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 331;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 332;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 339;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 201;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 506;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 140;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 141;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 624;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 142;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 144;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 333;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 334;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 340;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 511;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 639;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 110;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 513;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 427;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 342;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 512;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 510;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 637;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 102;
UPDATE System.COUNTRY_TBL SET decimals = 3 WHERE id = 605;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 610;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 641;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 428;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 429;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 640;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 112;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 147;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 431;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 343;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 149;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 607;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 424;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 425;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 145;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 440;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 519;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 151;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 349;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 608;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 345;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 348;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 642;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 152;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 153;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 514;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 434;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 355;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 113;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 634;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 346;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 435;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 350;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 101;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 109;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 643;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 646;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 353;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 644;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 515;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 438;
UPDATE System.COUNTRY_TBL SET decimals = 3 WHERE id = 352;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 154;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 645;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 437;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 516;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 155;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 647;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 103;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 218;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 200;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 439;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 648;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 441;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 206;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 336;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 650;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 356;
UPDATE System.COUNTRY_TBL SET decimals = 2 WHERE id = 358;

/* ========== Mobile Optimized 3D Secure BEGIN ========== */
INSERT INTO System.URLType_Tbl (id, name) VALUES (12, 'Parse 3D Secure Challenge URL');
INSERT INTO Log.State_Tbl (id, name) VALUES (1100, '3D Secure Activated');
INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (12, 10005, 'http://dsb.mesb.cellpointmobile.com:10080/mpoint/parse-3dsecure-challenge');
INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (12, 10014, 'http://dsb.mesb.test.cellpointmobile.com:10080/mpoint/parse-3dsecure-challenge');
/* ========== Mobile Optimized 3D Secure END ========== */



/* ========== Global Configuration for CCAvenue ========== */
INSERT INTO System.PSP_Tbl (id, name) VALUES (25, 'CCAvenue');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) SELECT countryid, 25, name FROM System.PSPCurrency_Tbl WHERE pspid = 4;
INSERT INTO System.PSPCard_Tbl (cardid, pspid) SELECT cardid, 25 FROM System.PSPCard_Tbl WHERE pspid = 4;
/* ========== CONFIGURE CCAvenue END ========== */

/* ========== CONFIGURE Test account - 100001 FOR CCAvenue STARTS ========== */
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 25, '110880', 'bha_110880', 'malindo123$');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 25, '-1');
-- Route VISA Card to CCAvenue
UPDATE Client.CardAccess_Tbl SET pspid = 25 WHERE clientid = 10001 AND cardid = 8;
/* ==========  CONFIGURE Test account - 100001 FOR CCAvenue END ====== */

/* ========== Global Configuration for PayFort = STARTS ========== */
INSERT INTO System.PSP_Tbl (id, name) VALUES (23, 'PayFort');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (608,1,'SAR');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (608,23,'SAR');

INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (7, 23);
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (8, 23);

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 23, 'CPMDemo', 'CTjbJcSI', 'BMMVFHwUGyfjDZk2PzMc');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 23, '-1');
/* ========== Global Configuration for PayFort = ENDS ========== */

/* ========== Global Configuration for PayPal ========== */
INSERT INTO System.PSP_Tbl (id, name) VALUES (24, 'PayPal');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) SELECT countryid, 24, name FROM System.PSPCurrency_Tbl WHERE pspid = 4;


INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength) VALUES (28, 'PayPal', 23, -1, -1, -1);
INSERT INTO System.CardPrefix_Tbl (cardid, min, "max") VALUES (28, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 28, id FROM System.PricePoint_Tbl WHERE amount = -1 AND countryid = 103;

INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (28, 24);
/* ========== CONFIGURE PayPal END ========== */

/* ========== CONFIGURE Test account - 100001 FOR PayPal STARTS ========== */
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 24, 'AFcWxV21C7fd0v3bYYYRCpSSRl31ADxVAF5rd9Z-52J.7gdxYOzAv3RD', 'business_api1.cellpointmobile.com', 'M7XXPU99YPFATTPL');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 24, '-1');
-- Route Paypal Card to PayPal
INSERT INTO Client.CardAccess_Tbl (pspid, clientid, cardid) VALUES (24, 10001, 28);
/* ==========  CONFIGURE Test account - 100001 FOR PayPal END ====== */

/* ============= SETTLED PAYMENT STATE added for mPoint Settlement & Reconciliation feature ========= */
INSERT INTO Log.State_Tbl (id, name,module, func, enabled) VALUES (2020, 'Payment Settled', 'Payment' ,'settleTransaction',true);
