-- HPP form redirect method set to GET for all the client

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10000, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10014, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10062, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10019, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10070, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10071, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10066, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10060, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10072, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10067, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10075, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10061, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10069, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10074, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10020, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10021, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10073, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('hppFormRedirectMethod', 'GET', 10065, 'client', true);


-- Setup for 2c2p-alc with alipay and unionpay--

INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (40, 67);
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (40, 40);

INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (67, -1, -1);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (40, -1, -1);

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 702;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 702;


INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 784;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 840;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 840;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 784;


INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 36;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 36;

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 48;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 48;

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 124;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 124;

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 156;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 156;

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 344;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 344;

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 360;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 360;

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 356;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 356;

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 392;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 392;

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 408;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 408;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 410;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 410;

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 414;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 414;

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 446;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 446;

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 458;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 458;

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 554;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 554;

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 598;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 598;

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 608;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 608;

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 634;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 634;

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 682;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 682;

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 702;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 702;

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 764;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 764;

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 949;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 949;

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 901;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 901;



INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (702,40,'SGD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (784,40,'ARE');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (840,40,'USA');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (36,40,'AUD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (48,40,'BHD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (124,40,'CAN');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (156,40,'CHN');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (344,40,'HKG');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (360,40,'IDN');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (356,40,'IND');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (392,40,'JPN');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (414,40,'KWT');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (446,40,'MAC');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (458,40,'MYS');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (554,40,'NZL');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (598,40,'PGK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (608,40,'PHL');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (634,40,'QAT');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (682,40,'SAU');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (702,40,'SGP');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (764,40,'THA');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (949,40,'TUR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (901,40,'TWN');





