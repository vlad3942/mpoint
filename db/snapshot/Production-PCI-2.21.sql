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

ALTER TABLE client.cardaccess_tbl ADD walletid int4;
drop index cardaccess_card_country_uq;
DROP INDEX client.cardaccess_card_country_uq RESTRICT;
CREATE UNIQUE INDEX cardaccess_card_country_uq ON client.cardaccess_tbl USING btree (clientid, cardid, pspid, countryid, psp_type,walletid) WHERE (enabled = true);

ALTER TABLE enduser.address_tbl ALTER COLUMN street TYPE character varying(100);

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

ALTER TABLE Log.Transaction_Tbl ALTER COLUMN attempt SET DEFAULT 1;
ALTER TABLE CLIENT.SUREPAY_TBL ADD MAX INT4 DEFAULT 1;
INSERT INTO CLIENT.SUREPAY_TBL (CLIENTID, RESEND, MAX) SELECT CLIENTID, DELAY, RETRIALVALUE::INTEGER FROM CLIENT.RETRIAL_TBL;


ALTER TABLE log.passenger_tbl alter column first_name type varchar(50);
ALTER TABLE log.passenger_tbl alter column last_name type varchar(50);

ALTER TABLE system.currency_tbl ADD COLUMN symbol VARCHAR(5);
UPDATE system.currency_tbl AS cur SET symbol = con.symbol FROM system.country_tbl AS con WHERE cur.id = con.currencyid;


INSERT INTO system.externalreferencetype_tbl (id, "name") VALUES(0, 'System');
INSERT INTO system.externalreferencetype_tbl (id, "name") VALUES(50, 'UATP');
INSERT INTO system.externalreferencetype_tbl (id, "name") VALUES(1, 'CellPoint Foreign Exchange');

INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(1980, 'Foreign Exchange  Ack Accepted', 'Callback', 'send');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(1981, 'Foreign Exchange  Ack Constructed', 'Callback', 'send');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(1983, 'Foreign Exchange  Ack Connection Failed', 'Callback', 'send');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(1984, 'Foreign Exchange  Ack Transmission Failed', 'Callback', 'send');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(1985, 'Foreign Exchange  Ack Rejected', 'Callback', 'send');



/* ========== Global Configuration for DragonPay = STARTS ========== */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (61, 'DragonPay',1);
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (608,61,'PHP');
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (47, 61);
/* ========== Global Configuration for DragonPay = END ========== */

INSERT INTO system.psp_tbl (id, name, enabled, system_type) VALUES (62, 'FIRST DATA', true, 1);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (1, 62, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (5, 62, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (7, 62, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (8, 62, true);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'AED',true,784);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'AUD',true,36);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'BHD',true,48);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'BND',true,96);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'CAD',true,124);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'CHF',true,756);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'CNY',true,156);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'DKK',true,208);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'EUR',true,978);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'GBP',true,826);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'HKD',true,344);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'IDR',true,360);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'INR',true,356);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'JPY',true,392);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'KRW',true,410);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'KWD',true,414);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'LKR',true,144);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'MOP',true,446);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'MYR',true,458);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'NOK',true,578);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'NZD',true,554);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'PHP',true,608);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'QAR',true,634);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'SAR',true,682);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'SEK',true,752);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'SGD',true,702);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'THB',true,764);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'TWD',true,901);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'USD',true,840);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (62,'VND',true,704);

INSERT INTO system.psp_tbl (id, name, enabled, system_type) VALUES (63, 'CyberSource', true, 1);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (1, 63, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (5, 63, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (7, 63, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (8, 63, true);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'AED',true,784);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'AUD',true,36);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'BHD',true,48);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'BND',true,96);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'CAD',true,124);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'CHF',true,756);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'CNY',true,156);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'DKK',true,208);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'EUR',true,978);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'GBP',true,826);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'HKD',true,344);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'IDR',true,360);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'INR',true,356);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'JPY',true,392);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'KRW',true,410);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'KWD',true,414);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'LKR',true,144);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'MOP',true,446);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'MYR',true,458);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'NOK',true,578);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'NZD',true,554);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'PHP',true,608);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'QAR',true,634);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'SAR',true,682);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'SEK',true,752);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'SGD',true,702);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'THB',true,764);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'TWD',true,901);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'USD',true,840);
INSERT INTO "system".pspcurrency_tbl (pspid,"name",enabled,currencyid) VALUES (63,'VND',true,704);

/* Stored Card Route for stored card 10018*/
INSERT INTO client.cardaccess_tbl (clientid, cardid, created, modified, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,walletid)
SELECT  clientid, cardid, created, modified, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,36 FROM client.cardaccess_tbl where clientid = 10018 and enabled = true and cardid in (8,7,1,5);

/* Stored Card Route for stored card 10021*/
INSERT INTO client.cardaccess_tbl (clientid, cardid, created, modified, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,walletid)
SELECT  clientid, cardid, created, modified, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,36
FROM client.cardaccess_tbl where clientid = 10021 and enabled = true and cardid in (8,7,1,5,71);

/* Stored Card Route for stored card 10062*/
INSERT INTO client.cardaccess_tbl (clientid, cardid, created, modified, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,walletid)
SELECT  clientid, cardid, created, modified, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,36 FROM client.cardaccess_tbl where clientid = 10062 and enabled = true and cardid in (8,7,1,5,22,3);

/* Wallet Based Routing scripts*/
INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,walletid)VALUES(10074, 7, 21, 405, 1, NULL, false, 1, 0, 0, 3,14);
INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,walletid)VALUES(10074, 8, 21, 405, 1, NULL, false, 1, 0, 0, 3,14);
INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,walletid)VALUES( 10081, 7, 21, 429, 1, NULL, false, 1, 0, 0, 3,14);
INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,walletid)VALUES( 10081, 8, 21, 429, 1, NULL, false, 1, 0, 0, 3,14);
INSERT INTO client.cardaccess_tbl (clientid, cardid,pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,walletid) VALUES(10069,8,52, 200, 1, NULL, false, 1, 0, 6, 1,14);
INSERT INTO client.cardaccess_tbl (clientid, cardid,pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,walletid) VALUES(10069,7,52, 200, 1, NULL, false, 1, 0, 6, 1,14);
INSERT INTO client.cardaccess_tbl (clientid, cardid,pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,walletid) VALUES(10069,1,52, 200, 1, NULL, false, 1, 0, 6, 1,14);
INSERT INTO client.cardaccess_tbl (clientid, cardid,pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,walletid) VALUES(10069,22,52, 200, 1, NULL, false, 1, 0, 6, 1,14);
INSERT INTO client.cardaccess_tbl (clientid, cardid,pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,walletid) VALUES(10099, 8,18, 200, 1, NULL, false, 1, 0, 0, 2,44);
INSERT INTO client.cardaccess_tbl (clientid, cardid,pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,walletid) VALUES(10099, 7,18, 200, 1, NULL, false, 1, 0, 0, 2,44);

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES ('FILE_EXPIRY', '1', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10069 and pspid = 50), 'merchant',1);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES ('FILE_EXPIRY', '6', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10069 and pspid = 52), 'merchant',1);

--PAL 2C2P ALC----
delete from client.additionalproperty_tbl where key like 'mid.%' and externalid in (select id FROM client.merchantaccount_tbl WHERE clientid=10020 AND pspid=40);
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.NZD','NZDNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10020 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.AED','AEDNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10020 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.BHD','BHDNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10020 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.THB','THBNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10020 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.QAR','QARNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10020 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.USD','USDNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10020 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.HKD','HKDNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10020 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.TRY','TRYNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10020 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.IDR','IDRNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10020 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.KWD','KWDNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10020 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.MYR','MYRNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10020 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.GBP','GBPNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10020 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.PHP','PHPNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10020 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.MOP','MOPNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10020 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.PGK','PGKNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10020 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.SAR','SARNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10020 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.USD','USDNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10020 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.KRW','KRWNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10020 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.USD','USDNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10020 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.VND','VNDNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10020 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.CNY','CNYNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10020 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.SGD','SGDNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10020 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.AUD','AUDNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10020 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.TWD','TWDNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10020 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.JPY','JPYNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10020 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.CAD','CADNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10020 AND pspid=40;

--cvcmandatory
INSERT INTO CLIENT.STATICROUTELEVELCONFIGURATION (CARDACCESSID) SELECT id FROM CLIENT.CARDACCESS_TBL WHERE cardid in (select id from system.card_tbl where paymenttype=1) and clientid not in (10069);
INSERT INTO CLIENT.STATICROUTELEVELCONFIGURATION (CARDACCESSID) SELECT id FROM CLIENT.CARDACCESS_TBL WHERE cardid in (71) and clientid not in (10069);--MADA Card


--Missing state
INSERT INTO log.state_tbl(id, name, module) VALUES(4031,'Session Partially Completed','Payment');
