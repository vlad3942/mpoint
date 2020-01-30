

INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid)  SELECT 10020, PC.cardid, PC.pspid, '154' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;


/* ========== Global Configuration for POLi - Card========== */
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength) VALUES (34, 'POLi', 23, -1, -1, -1);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (34, 0, 0);

-- CardPricing_Tbl for Australia
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) VALUES (34, -36);

-- CardPricing_Tbl for New Zealand
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) VALUES (34, -554);
/* ========== Global Configuration for POLi - Card ========== */

/* ========== Global Configuration for POLi = PSP ========== */
INSERT INTO System.PSP_Tbl (id, name, system_type) VALUES (32, 'POLi', 4);
-- CardPricing_Tbl for Australia
INSERT INTO System.PSPCurrency_Tbl (pspid, name, currencyid) VALUES (32,'AUD', 36);
-- CardPricing_Tbl for New Zealand
INSERT INTO System.PSPCurrency_Tbl (pspid, name, currencyid) VALUES (32,'NZD', 554);
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (34, 32);
/* ========== Global Configuration for POLi = PSP ========== */

/* ========== Global Configuration for POLi  Merchant = STARTS ========== */
-- MerchantAccount_Tbl for Australia default entry
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (<clientid>, 32, 'POLi', '6101816', 'MdXqHAM!Y2EWQvVC4WsT');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<accountid>, 32, 'POLi');
/* ========== Global Configuration for POLi  Merchant = STARTS ========== */

-- Route POLi Card to POLi with country Australia
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, countryid) VALUES (<clientid>, 34, 32, true, 500);
INSERT INTO system.pricepoint_tbl (id, amount, enabled, currencyid) VALUES (-36, -1, true, 36);

-- Route POLi Card to POLi with country New Zealand
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, countryid) VALUES (<clientid>, 34, 32, true, 513);
INSERT INTO system.pricepoint_tbl (id, amount, enabled, currencyid) VALUES (-554, -1, true, 554);

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('MID.513', 'T6400234', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = <client id> and pspid = <pspid>), 'merchant', 0);

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('AUTHCODE.513', 'B!q0Zi8@uaAp5$qP2^', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = <client id> and pspid = <pspid>), 'merchant', 0);

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('mechantaccountrule',
'username ::= (property[@name=''<midpath>''])
midpath ::= "MID."(@country-id)
password ::= (property[@name=''<authpath>''])
authpath ::= "AUTHCODE."(@country-id)', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = <clientid> and pspid = 32), 'merchant', 0);

--PAL  2C2P-ALC Query Start-

-- Setup for 2c2p-alc with alipay--
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (40, 32);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (32, -1, -1);

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 32, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 36;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 32, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 124;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 32, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 156;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 32, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 826;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 32, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 344;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 32, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 392;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 32, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 446;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 32, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 554;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 32, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 702;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 32, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 764;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 32, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 901;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 32, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 840;

-- Setup for 2c2p-alc with unionpay--
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 826;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 784;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 36;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 124;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 156;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 826;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 344;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 360;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 356;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 392;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 410;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 446;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 458;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 554;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 608;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 702;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 32, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 764;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 901;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 840;

-- common queries for alipay and unionpay --
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT <cleintid>, PC.cardid, PC.pspid, '647' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT <cleintid>, PC.cardid, PC.pspid, '500' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,32) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT <cleintid>, PC.cardid, PC.pspid, '202' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,32) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT <cleintid>, PC.cardid, PC.pspid, '609' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,32) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT <cleintid>, PC.cardid, PC.pspid, '103' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,32) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT <cleintid>, PC.cardid, PC.pspid, '614' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,32) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT <cleintid>, PC.cardid, PC.pspid, '505' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT <cleintid>, PC.cardid, PC.pspid, '603' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT <cleintid>, PC.cardid, PC.pspid, '616' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,32) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT <cleintid>, PC.cardid, PC.pspid, '632' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT <cleintid>, PC.cardid, PC.pspid, '636' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,32) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT <cleintid>, PC.cardid, PC.pspid, '638' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT <cleintid>, PC.cardid, PC.pspid, '513' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,32) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT <cleintid>, PC.cardid, PC.pspid, '640' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT <cleintid>, PC.cardid, PC.pspid, '642' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,32) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT <cleintid>, PC.cardid, PC.pspid, '644' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,32) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT <cleintid>, PC.cardid, PC.pspid, '646' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,32) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT <cleintid>, PC.cardid, PC.pspid, '200' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,32) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT <cleintid>, PC.cardid, PC.pspid, '622' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,32) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;

INSERT INTO system.pspcurrency_tbl (id, pspid, name) VALUES ('36','40','AUD');
INSERT INTO system.pspcurrency_tbl (id, pspid, name) VALUES ('124','40','CAD');
INSERT INTO system.pspcurrency_tbl (id, pspid, name) VALUES ('156','40','CNY');
INSERT INTO system.pspcurrency_tbl (id, pspid, name) VALUES ('344','40','HKD');
INSERT INTO system.pspcurrency_tbl (id, pspid, name) VALUES ('356','40','INR');
INSERT INTO system.pspcurrency_tbl (id, pspid, name) VALUES ('360','40','IDR');
INSERT INTO system.pspcurrency_tbl (id, pspid, name) VALUES ('392','40','JPY');
INSERT INTO system.pspcurrency_tbl (id, pspid, name) VALUES ('410','40','KRW');
INSERT INTO system.pspcurrency_tbl (id, pspid, name) VALUES ('446','40','MOP');
INSERT INTO system.pspcurrency_tbl (id, pspid, name) VALUES ('458','40','MYR');
INSERT INTO system.pspcurrency_tbl (id, pspid, name) VALUES ('554','40','NZD');
INSERT INTO system.pspcurrency_tbl (id, pspid, name) VALUES ('608','40','PHP');
INSERT INTO system.pspcurrency_tbl (id, pspid, name) VALUES ('702','40','SGD');
INSERT INTO system.pspcurrency_tbl (id, pspid, name) VALUES ('764','40','THB');
INSERT INTO system.pspcurrency_tbl (id, pspid, name) VALUES ('784','40','AED');
INSERT INTO system.pspcurrency_tbl (id, pspid, name) VALUES ('826','40','GBP');
INSERT INTO system.pspcurrency_tbl (id, pspid, name) VALUES ('840','40','USD');
INSERT INTO system.pspcurrency_tbl (id, pspid, name) VALUES ('901','40','TWD');

--PAL  2C2P-ALC Query End-

-- Hpp flag
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES ('isAutoRedirect', 'true', <clientid>, 'client', true, 2);


-- Amex - Start of New Route - Jordan

INSERT INTO client.cardaccess_tbl (clientid,cardid,enabled,pspid,countryid,stateid,position,preferred,psp_type)
VALUES(<clientid>,1,true,45,617,1,NULL,false,1);

INSERT INTO client.countrycurrency_tbl (clientid, countryid, currencyid, enabled) VALUES (<clientid>, 617, 840, true);

INSERT INTO client.additionalproperty_tbl (key, value, enabled, type, externalid, scope)
SELECT 'AMEX_MERCHANT_NUMBER_840', '<value>', true, 'merchant', id, 2 FROM client.merchantaccount_tbl WHERE pspid=45 AND clientid=<clientid>;

INSERT INTO system.pspcurrency_tbl (pspid, name, currencyid, enabled) VALUES (45, 'USD', 840, true);



-- DataCash - Start of New Route - Jordan

INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type)
VALUES (<clientid>, 7, true, 17, 617, 1, null, false, 1);

INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type)
VALUES (<clientid>, 8, true, 17, 617, 1, null, false, 1);

INSERT INTO client.countrycurrency_tbl (clientid, countryid, currencyid, enabled) VALUES (<clientid>, 617, 840, true);

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope)
SELECT 'mid.USD', 'SGBSABB01', id, 'merchant', 2 from client.merchantaccount_tbl WHERE clientid=10021 AND pspid=17 ;

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope)
SELECT 'username.USD', 'merchant.SGBSABB01', id, 'merchant', 2 from client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=17 ;

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope)
SELECT 'password.USD', 'bebd68b2fa491f807e40462a6f85617e', id, 'merchant', 2 from client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=17 ;

INSERT INTO system.pspcurrency_tbl (pspid, name, currencyid, enabled) VALUES (17, 'USD', 840, true);


