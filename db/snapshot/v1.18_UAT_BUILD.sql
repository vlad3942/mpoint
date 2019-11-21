--//********Cellulant*******************//

--//**********system.card_tbl************//
INSERT INTO system.card_tbl (id, name, logo, enabled, position, minlength, maxlength, cvclength, paymenttype) VALUES (86, 'CELLULANT', null, true, 23, -1, -1, -1, 4);

--//**********system.pricepoint_tbl************//
INSERT INTO system.pricepoint_tbl (id, amount, enabled, currencyid) VALUES (-404, -1, true, 404);

--//**********system.cardpricing_tbl************//
INSERT INTO system.cardpricing_tbl (pricepointid, cardid, enabled) VALUES (-404, 86, true);

--//**********system.psp_tbl************//
INSERT INTO system.psp_tbl (id, name, enabled, system_type) VALUES (58, 'CELLULANT', true, 1);

--//**********system.pspcard_tbl************//
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (86, 58, true);

--//**********system.pspcurrency_tbl************//
INSERT INTO system.pspcurrency_tbl (pspid, name, enabled, currencyid) VALUES (58, 'KES', true, 404);

--//**********system.cardprefix_tbl Bin range************//
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (86,0	,0, true);
//********END OF Cellulant*******************//


/* Ticket level transaction - Add new column fees in log.order_tbl */
ALTER TABLE Log.order_tbl ADD COLUMN fees integer DEFAULT 0;

-- Create new table system.businesstype_tbl to store businesstype for each client
CREATE TABLE system.businesstype_tbl
(
  id serial NOT NULL,
  name character varying(50),
  enabled boolean DEFAULT true,
  CONSTRAINT businesstype_pk PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);

ALTER TABLE system.businesstype_tbl OWNER TO mpoint;
GRANT ALL ON TABLE system.businesstype_tbl TO mpoint;
GRANT ALL ON TABLE system.businesstype_tbl TO jona;

-- Insert business type details
insert into system.businesstype_tbl (id,name) values
(0,'None'),
(1,'Non-Industry-Specific'),
(2,'Airline Industry'),
(3,'Auto Rental Industry'),
(4,'Cruise Industry'),
(5,'Hospitality Industry'),
(6,'Entertainment/Ticketing Industry'),
(7,'e-commerce Industry');

-- Added new column and foreign key constraint
ALTER TABLE client.account_tbl
	ADD COLUMN businessType integer DEFAULT 0,
	ADD CONSTRAINT businessType_pk FOREIGN KEY (businessType) REFERENCES system.businesstype_tbl (id);

-- Set businesstype 2 for UATP client
update client.account_tbl set businesstype = 2 where clientid = 10069;

-- Insert New transactions states into log.state_tbl
insert into log.state_tbl (id,name, module,func) values
(2010101,'Failed during Capture','Payment','Capture'),
(2010201,'Failed during Cancel','Payment','Cancel'),
(2010301,'Failed during Refund','Payment','Refund');

ALTER TABLE Log.Order_Tbl ADD COLUMN orderref character varying(40);

CREATE INDEX account_tbl_businessType_index ON client.account_tbl (businessType);
CREATE INDEX order_tbl_orderref_index ON Log.Order_Tbl (orderref);
CREATE INDEX eu_account_tbl_profileid_index ON enduser.account_tbl (profileid);

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('IS_TICKET_LEVEL_SETTLEMENT', 'true', <merchant-table-id>, 'merchant', 0);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('IS_TICKET_LEVEL_SETTLEMENT', 'true', <merchant-table-id>, 'merchant', 0);

/* ========== Global Configuration for VeriTrans4G = STARTS ========== */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (59, 'VeriTrans4G',2);

INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (392,1,'JPY');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (392,59,'JPY');

/*Amex*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (1, 59);
/*MasterCard*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (7, 59);
/*VISA*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (8, 59);


/* ========== Global Configuration for VeriTrans4G = STARTS ========== */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (59, 'VeriTrans4G',2);

INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (392,1,'JPY');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (392,59,'JPY');

/*Amex*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (1, 59);
/*MasterCard*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (7, 59);
/*VISA*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (8, 59);

-- Setup for 2c2p-alc with alipay and unionpay--
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (40, 67);
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (40, 40);

INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (67, -1, -1);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (40, -1, -1);

INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) SELECT 10055, PC.cardid, PC.pspid FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;

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

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 156;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 156;

INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (702,40,'SGD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (784,40,'ARE');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (840,40,'USA');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (36,40,'AUD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (48,40,'BHD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (124,40,'CAN');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (156,40,'CNY');
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