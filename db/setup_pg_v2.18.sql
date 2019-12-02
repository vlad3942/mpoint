--Adding unionpay card for 2c2p-alc--
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (67, 'UnionPay', 23, -1, -1, -1, 4);
INSERT INTO system.cardprefix_tbl (cardid, min, max) VALUES (67, 0, 0);


--Setup for 2c2p-alc with unionpay--
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (40, 67);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (67, -1, -1);

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 840;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 702;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 784;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 36;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 48;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 124;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 156;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 344;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 360;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 356;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 392;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 408;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 410;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 414;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 446;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 458;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 554;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 598;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 608;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 634;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 682;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 702;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 764;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 949;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 901;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 156;


--Setup for 2c2p-alc with alipay--
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (40, 40);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (40, -1, -1);

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 702;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 840;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 784;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 36;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 48;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 124;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 156;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 344;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 360;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 356;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 392;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 408;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 410;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 414;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 446;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 458;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 554;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 598;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 608;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 634;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 682;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 702;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 764;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 949;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 901;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 156;

--common queries for alipay and unionpay on UAT enviourment with client id 100200--
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10020, PC.cardid, PC.pspid, '651' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10020, PC.cardid, PC.pspid, '202' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10020, PC.cardid, PC.pspid, '603' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10020, PC.cardid, PC.pspid, '614' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10020, PC.cardid, PC.pspid, '505' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10020, PC.cardid, PC.pspid, '513' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10020, PC.cardid, PC.pspid, '616' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10020, PC.cardid, PC.pspid, '636' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10020, PC.cardid, PC.pspid, '622' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10020, PC.cardid, PC.pspid, '638' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10020, PC.cardid, PC.pspid, '604' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10020, PC.cardid, PC.pspid, '606' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10020, PC.cardid, PC.pspid, '608' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10020, PC.cardid, PC.pspid, '642' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10020, PC.cardid, PC.pspid, '646' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10020, PC.cardid, PC.pspid, '647' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10020, PC.cardid, PC.pspid, '644' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
NSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid)  SELECT 10020, PC.cardid, PC.pspid, '154' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10020, PC.cardid, PC.pspid, '640' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10020, PC.cardid, PC.pspid, '609' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10020, PC.cardid, PC.pspid, '200' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10020, PC.cardid, PC.pspid, '500' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;



--common queries for alipay and unionpay on SIT-L enviourment with client id 10055--
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10055, PC.cardid, PC.pspid, '651' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10055, PC.cardid, PC.pspid, '202' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10055, PC.cardid, PC.pspid, '603' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10055, PC.cardid, PC.pspid, '614' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10055, PC.cardid, PC.pspid, '505' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10055, PC.cardid, PC.pspid, '513' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10055, PC.cardid, PC.pspid, '616' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10055, PC.cardid, PC.pspid, '636' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10055, PC.cardid, PC.pspid, '622' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10055, PC.cardid, PC.pspid, '638' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10055, PC.cardid, PC.pspid, '604' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10055, PC.cardid, PC.pspid, '606' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10055, PC.cardid, PC.pspid, '608' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10055, PC.cardid, PC.pspid, '642' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10055, PC.cardid, PC.pspid, '646' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10055, PC.cardid, PC.pspid, '647' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10055, PC.cardid, PC.pspid, '644' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
NSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid)  SELECT 10055, PC.cardid, PC.pspid, '154' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10055, PC.cardid, PC.pspid, '640' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10055, PC.cardid, PC.pspid, '609' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10055, PC.cardid, PC.pspid, '200' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid) SELECT 10055, PC.cardid, PC.pspid, '500' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;



INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (702,40,'SGD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (784,40,'AED');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (840,40,'USD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (36,40,'AUD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (48,40,'BHD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (124,40,'CAD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (156,40,'CNY');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (344,40,'HKD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (360,40,'IDR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (356,40,'INR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (392,40,'JPY');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (414,40,'KWD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (446,40,'MOP');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (458,40,'MYR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (554,40,'NZD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (598,40,'PGK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (608,40,'PHP');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (634,40,'QAR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (682,40,'SAR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (764,40,'THB');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (949,40,'TRY');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (901,40,'TWD');


--//********OMANNET*******************//

--//**********system.card_tbl************//

INSERT INTO system.card_tbl (id, name, logo, enabled, position, minlength, maxlength, cvclength, paymenttype) VALUES (87, 'OMANNET', null, true, 23, -1, -1, -1, 4);

--//**********system.pricepoint_tbl************//
INSERT INTO system.pricepoint_tbl (id, amount, enabled, currencyid) VALUES (-512, -1, true, 512);

--//**********system.cardpricing_tbl************//
INSERT INTO system.cardpricing_tbl (pricepointid, cardid, enabled) VALUES (-512, 87, true);


--//**********system.psp_tbl************//
INSERT INTO system.psp_tbl (id, name, enabled, system_type, installment) VALUES (38, 'PayTabs', true, 1, 0);

--//**********system.pspcard_tbl************//
INSERT INTO system.pspcard_tbl (cardid, pspid,enabled) VALUES (87, 38, true);

--//**********system.pspcurrency_tbl************//
INSERT INTO system.pspcurrency_tbl (pspid, name, enabled, currencyid) VALUES (38, 'OMR', true, 512);

--//**********client.merchantaccount_tbl************//
INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, enabled, username, passwd, stored_card) VALUES (<clientid>, 38, 'PayTabs', true, 'arun123', 'Sunrise@123', null);

--//**********client.merchantsubaccount_tbl************//
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (<accountid>, 38, '-1', true);

--//**********client.cardaccess_tbl************//
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment, capture_method) VALUES (<clientid>, 87, true, 38, 605, 1, null, false, 1, 0, 0);


--//**********client.additionalproperty_tbl For SIT/UAT************//
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ('OMANNET_Secret_Key', 'nJkmOlYkWDlV8YQaGbqbZU0FawETEZr2JMox7nS40e08cQiT1I51D5GOClHhl9k5VIC35v76J7eWXxQZgV0U1HIE2qVnzgsuY5x4', true, <merchant-id>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ('OMANNET_MID', '10015651', true, <merchant-id>, 'merchant', 2);

--//**********client.additionalproperty_tbl Place hodlers************//
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ('OMANNET_Secret_Key',<secrete_key>, true, <merchant-id>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ('OMANNET_MID', <MID>, true, <merchant-id>, 'merchant', 2);


--//********END OF OMANNET*******************//
-- Gpay constant when psp support specific payment method. Use in global payment
UPDATE client.additionalproperty_tbl SET VALUES = 'PAN_ONLY' WHERE  key = 'ALLOWEDPAYMENTMETHODS' AND VALUES = 'CARD';

INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type,scope)
SELECT 'ALLOWEDPAYMENTMETHODS','PAN_ONLY',true, id, 'merchant', 1 FROM client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=44;
