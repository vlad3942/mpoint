-- CMP-3484 Wallet Based Routing --
ALTER TABLE client.cardaccess_tbl ADD walletid int4;
drop index cardaccess_card_country_uq;
CREATE UNIQUE INDEX cardaccess_card_country_uq ON client.cardaccess_tbl USING btree (clientid, cardid, pspid, countryid, psp_type,walletid) WHERE (enabled = true);


--DCC---
CREATE TABLE system.externalreferencetype_tbl (
	id serial NOT NULL,
	"name" text NOT NULL,
	created timestamp NULL DEFAULT now(),
	modified timestamp NULL DEFAULT now(),
	enabled bool NULL DEFAULT true,
	CONSTRAINT externalreferencetype_pk PRIMARY KEY (id)
);
ALTER TABLE system.externalreferencetype_tbl OWNER TO mpoint;

ALTER TABLE log.externalreference_tbl ADD type int4 CONSTRAINT externalreferencetype_fk REFERENCES system.externalreferencetype_tbl(id);
ALTER TABLE log.transaction_tbl ADD convetredcurrencyid int4 NULL CONSTRAINT convertedcurrency_fk REFERENCES system.currency_tbl(id);
ALTER TABLE log.transaction_tbl ADD convertedamount int8 NULL;
ALTER TABLE log.transaction_tbl ADD conversionrate decimal DEFAULT 1;
ALTER TABLE client.cardaccess_tbl ADD dccenabled bool NULL DEFAULT false;
---DCC---
DROP TABLE IF EXISTS CLIENT.RETRIAL_TBL;

DROP TABLE IF EXISTS SYSTEM.RETRIALTYPE_TBL;

--pspcurrency UNIQUE CONSTRAINT
CREATE UNIQUE INDEX pspcurrency_psp_currency_uq ON system.pspcurrency_tbl USING btree (pspid, currencyid) WHERE (enabled = true);



-- passenger tbl --
ALTER TABLE log.passenger_tbl alter column first_name type varchar(50);
ALTER TABLE log.passenger_tbl alter column last_name type varchar(50);

-- currency improvement --
ALTER TABLE system.currency_tbl ADD COLUMN symbol VARCHAR(5);
ALTER TABLE system.country_tbl DROP COLUMN symbol;



CREATE TABLE system.externalreferencetype_tbl (
	id serial NOT NULL,
	"name" text NOT NULL,
	created timestamp NULL DEFAULT now(),
	modified timestamp NULL DEFAULT now(),
	enabled bool NULL DEFAULT true,
	CONSTRAINT externalreferencetype_pk PRIMARY KEY (id)
);
ALTER TABLE system.externalreferencetype_tbl OWNER TO mpoint;
ALTER TABLE log.externalreference_tbl ADD type int4 CONSTRAINT externalreferencetype_fk REFERENCES system.externalreferencetype_tbl(id);
ALTER TABLE log.transaction_tbl ADD convetredcurrencyid int4 NULL CONSTRAINT convertedcurrency_fk REFERENCES system.currency_tbl(id);
ALTER TABLE log.transaction_tbl ADD convertedamount int8 NULL;
ALTER TABLE log.transaction_tbl ADD conversionrate decimal DEFAULT 1;
ALTER TABLE client.cardaccess_tbl ADD dccenabled bool NULL DEFAULT false;


/* ========== Order AID - Billing summary:: CMP-3459 ========== */

---
CREATE TABLE log.billing_summary_tbl
(
  id serial NOT NULL,
  order_id integer NOT NULL,
  journey_ref character varying(50),
  bill_type character varying(25) NOT NULL,
  type_id integer NOT NULL,
  description character varying(50) NOT NULL,
  amount character varying(20),
  currency character varying(10) NOT NULL,
  created timestamp without time zone NOT NULL DEFAULT now(),
  modified timestamp without time zone NOT NULL DEFAULT now(),
  CONSTRAINT billing_summary_pk PRIMARY KEY (id),
  CONSTRAINT order_fk FOREIGN KEY (order_id)
      REFERENCES log.order_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITHOUT OIDS;

  ALTER TABLE log.billing_summary_tbl OWNER TO mpoint;



/* ========== Alter address field size  ========== */
ALTER TABLE enduser.address_tbl ALTER COLUMN street TYPE character varying(100)

--DCC--
ALTER TABLE CLIENT.SUREPAY_TBL ADD MAX INT4 DEFAULT 1;


ALTER TABLE Log.Transaction_Tbl ALTER COLUMN attempt SET DEFAULT 1;
ALTER TABLE Log.Transaction_Tbl ALTER COLUMN attempt SET DEFAULT 1;

ALTER TABLE log.additional_data_tbl ALTER COLUMN value TYPE varchar(255);



/* ========== Global Configuration for DragonPay Offline = STARTS ========== */

INSERT INTO system.card_tbl (id, name, logo, enabled, position, minlength, maxlength, cvclength, paymenttype) VALUES (88, 'DRAGONPAYOFFLINE', null, true, 23, -1, -1, -1, 4);

/* ========== Global Common Configuration for DragonPay Offline/DragonPay Online = STARTS ========== */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (61, 'DragonPay',1);

INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (608,61,'PHP');


INSERT INTO system.cardpricing_tbl (pricepointid, cardid, enabled) VALUES (-608, 88, true);

/*
* Dragon pay offline card with Dragon Pay aggregator 
*/

INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (88, 61);


/* ========== Global Configuration for DragonPay Online = STARTS ========== */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (61, 'DragonPay',7);

/* ==========  Global Configuration for DragonPay Online = STARTS ========== */


INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (608,61,'PHP');

/*
* Dragon pay cad with Dragon Pay aggregator 
*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (47, 61);


---DCC--
INSERT INTO system.externalreferencetype_tbl (id, "name") VALUES(0, 'System');
INSERT INTO system.externalreferencetype_tbl (id, "name") VALUES(50, 'UATP');
INSERT INTO system.externalreferencetype_tbl (id, "name") VALUES(1, 'CellPoint Foreign Exchange');

INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(1980, 'Foreign Exchange  Ack Accepted', 'Callback', 'send');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(1981, 'Foreign Exchange  Ack Constructed', 'Callback', 'send');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(1983, 'Foreign Exchange  Ack Connection Failed', 'Callback', 'send');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(1984, 'Foreign Exchange  Ack Transmission Failed', 'Callback', 'send');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(1985, 'Foreign Exchange  Ack Rejected', 'Callback', 'send');


----CYBS (PSP) start ---
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (63, 'CyberSource',1);
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (63, 1);	-- American Express
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (63, 3);	-- Diners Club
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (63, 7);	-- MasterCard
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (63, 8);	-- VISA
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (63, 5);	-- JCB
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (63, 6);	-- Maestro
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (63, 15);	-- Apple Pay
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (63, 23);	-- MasterPass
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (63, 16);	-- VCO
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (63, 41);	-- Google Pay


--Add currency support as required for client
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (208,63,'DKK');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (156,63,'CNY');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (124,63,'CAD');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (36,63,'AUD');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (446,63,'MOP');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (414,63,'KWD');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (410,63,'KRW');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (392,63,'JPY');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (360,63,'IDR');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (356,63,'INR');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (344,63,'HKD');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (458,63,'MYR');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (554,63,'NZD');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (598,63,'PGK');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (608,63,'PHP');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (840,63,'USD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (634,63,'QAR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (682,63,'SAR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (702,63,'SGD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (764,63,'THB');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (784,63,'AED');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (826,63,'GBP');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (901,63,'TWD');


-- google pay api version 2.0 support payment method 'PAN_ONLY','CRYPTOGRAM_3DS'. previously PAN_ONLY is CARD and CRYPTOGRAM_3DS is TOKENIZED_CARD
UPDATE client.additionalproperty_tbl SET VALUES = 'PAN_ONLY' WHERE  key = 'ALLOWEDPAYMENTMETHODS' AND VALUES = 'CARD';

-- currency improvement --
UPDATE system.currency_tbl AS cur SET symbol = con.symbol FROM system.country_tbl AS con WHERE cur.id = con.currencyid;

INSERT INTO system.externalreferencetype_tbl (id, "name") VALUES(0, 'System');
INSERT INTO system.externalreferencetype_tbl (id, "name") VALUES(50, 'UATP');
INSERT INTO system.externalreferencetype_tbl (id, "name") VALUES(1, 'CellPoint Foreign Exchange');

INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(1980, 'Foreign Exchange  Ack Accepted', 'Callback', 'send');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(1981, 'Foreign Exchange  Ack Constructed', 'Callback', 'send');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(1983, 'Foreign Exchange  Ack Connection Failed', 'Callback', 'send');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(1984, 'Foreign Exchange  Ack Transmission Failed', 'Callback', 'send');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(1985, 'Foreign Exchange  Ack Rejected', 'Callback', 'send');


INSERT INTO CLIENT.SUREPAY_TBL (CLIENTID, RESEND, MAX)
SELECT CLIENTID, DELAY, RETRIALVALUE::INTEGER
FROM CLIENT.RETRIAL_TBL;

/* =============== 20-04-FIRST-DATA-Integration.sqlADDED ================== */

INSERT INTO system.pricepoint_tbl (id, amount, enabled, currencyid) VALUES (-608, -1, true, 608);

--//**********system.cardpricing_tbl************//
INSERT INTO system.cardpricing_tbl (pricepointid, cardid, enabled) VALUES (-608, 7, true);
INSERT INTO system.cardpricing_tbl (pricepointid, cardid, enabled) VALUES (-608, 8, true);


--//**********system.psp_tbl************//
INSERT INTO system.psp_tbl (id, name, enabled, system_type) VALUES (62, 'FIRST DATA', true, 1);

--//**********system.pspcard_tbl************//
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (7, 62, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (8, 62, true);

--//**********system.pspcurrency_tbl************//
INSERT INTO system.pspcurrency_tbl (pspid, name, enabled, currencyid) VALUES (62, 'PHL', true, 608);

--------------------------Added config changes for PHP -- SIT --
-- NOTE:: when running SQL please Replace with Sandbox test key for GPay and Apple Pay MIDs
-- delete all existing rules and properties setup against merchant acc id (14 and 44)
delete from client.additionalproperty_tbl where key like 'GlobalPayment.%';

--rules
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.merchantaccountrule', 'merchantaccount ::= (property[@name=''<mid>''])
 mid ::= (transaction/authorized-amount/@currency)=="PHP"="GlobalPayment.Wallet.MID."(transaction/@wallet-id)".PHP":"GlobalPayment.Wallet.MID."(transaction/@wallet-id)', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.usernamerule', 'username ::= (property[@name=''<uname>''])
 uname ::= (transaction/authorized-amount/@currency)=="PHP"="GlobalPayment.Wallet.USERNAME."(transaction/@wallet-id)".PHP":"GlobalPayment.Wallet.USERNAME."(transaction/@wallet-id)"', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.pwdrule', 'password ::= (property[@name=''<passwd>''])
 passwd ::= (transaction.authorized-amount.@currency)=="PHP"=<keywithcurrency>:<keywithoutcurrency>
keywithcurrency ::= "GlobalPayment.Wallet.PASSWORD."(transaction.@wallet-id)".PHP"
keywithoutcurrency ::= "GlobalPayment.Wallet.PASSWORD."(transaction.@wallet-id)', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;


---Google pay -- PHP config
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.MID.44.PHP', 'PAL-IPG PHP GOOGLEPAY', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.44.PHP', 'gpmnl045623832732', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.44.PHP', 'xrELE5yHDUR+jMpa8pcpIccQ2zQGjZMI45piyCzWR15AL9eLgFV5xhPciSHHUQtW1NpFpwip46oV1G2Oy9SQtBjEuszTVVPF3tOQVCaBhO6J3Tfjv8VBNLY2GEUPmpFwEKW+p79eJR0iEpMqdwy/necg2O0FfmDIcQ1ZlGh5G+asjIcgeWyZYjf+8UAy4qH/94TzNf2ku93W1xtobJXaQ5IcyC9dKxoAl3m4cqVTRDj1jKKjRdcsdt6IAopm4yorRlNy3pbZpdDq7OT2Jhb3uAe1O7fUWZye1hnTd4bzZpIxV2k/L81xaMnv9wLVsG/RiML0HqfWRzwcQNi+qpqayA==', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;

-- Google pay -- rest of the currencies config
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.MID.44', 'PAL-IPG GOOGLEPAY', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.44', 'gpmnl042772772760', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.44', 'a37Va81z32Q+qMNhRKG/2l9J1yvw1tYvw+atWXuW3jPHOFKAwcvGY9dPfuyDiST4ruhe9gDAg36lm+zdeiSUnmd3kN99Io/88daPtzVtnyzqEpT/QzgDoI8Y6rbD7rnINzVLEBWAB7xdWfw5NVRFMQrFuvOUE3VqyM3F8uQdEvCBFoPCxxIbsEo3Dn6hjevgzO7nzZPdrmqDrMO8CE9CUqleJhRlC6MfJNA4M87ZtYt03q0XpAXM/zYd8tYJJu29UelxadoeZxBdQzuoU89XXcZtYs8rUJXd5GRqtEuLXMS0tgNrnf2en7+Xe+Zl3tNSOwoJSkwdFj07yjnPF8nvdg==', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;

---Apple pay -- PHP config
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.MID.14.PHP', 'PAL-IPG PHP APPLEPAY', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.14.PHP', 'gpmnl045623832731', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.14.PHP', 'ov/87C1W9ahpp4MroX/Xray0pc/T2q0fE9R6pyAdVXrlw1+I9vU++oIul9AkHMa7H6Emb3msTyB91y34ST6Tysyi4Xvu/hYbB3KoFxairs1xOpXds3siOkNACVtFIhraIPhWi1TXbbMDRKkbe1U/zokXmdxsRRVjw6SJLevPLUSGVDXnjkbjIZM5rJ4PbwFHXfr3UQ5LW/PWgksxjMh34Yco+xT+4/gKO4r5cbr6GxlBmWcGtY//GGIq+lByAhDEiJvFsFLdBg6EyqyiSt4pf74NiN0XCKbljoQZ3U507P8PWi2tjsmeBp81kgpUmsPfE9MkucCDEey71KCpGbAmKA==', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;

-- Apple pay --rest of the currencies config
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.MID.14', 'PAL-IPG APPLEPAY', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.14', 'gpmnl042772772761', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.14', 'AFYDa+MdhNIANeSrpQORmKhqpOMAnMNF8xadipFE1LhLhfngyjowKj71fvDjoaBwUQZgMTN4GTofztmHbYkWNzuD/y8Qz/9QjSKs63P+o+G67fbYFwk8Q+Hcm5vS69TCi5wygsFiCLvoQsH3R0PTRdz2xoFvbFvJpjBU6gviJICMZUYQv9ZYqtVO7uEI/Ue5DSV3QIh4gSg/Rl6Yz9KfXcsm04j0D21IIu/hjxnnP+zeAhBUjxTOu1SggM9UAF+ryeOXXJc1HTDVFblotsZRFTn1mmmDrUKcgxcv1+JfVc3kqfyv0WFExHbKX9fmWZYpvJeQ8ie/3ePWSU+EZKdVhA==', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;
