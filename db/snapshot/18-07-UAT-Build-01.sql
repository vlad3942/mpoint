/*  ===========  START : Adding Table System.ProductType_Tbl  ==================  */
CREATE TABLE system.ProductType_Tbl
(
  id INT PRIMARY KEY,
  name VARCHAR(10) NOT NULL
);
CREATE UNIQUE INDEX ProductType_Tbl_name_uindex ON system.ProductType_Tbl (name);
COMMENT ON COLUMN system.ProductType_Tbl.id IS 'Unique number of product type';
COMMENT ON COLUMN system.ProductType_Tbl.name IS 'Product type name';
COMMENT ON TABLE system.ProductType_Tbl IS 'Contains all product types';

ALTER TABLE system.ProductType_Tbl
  OWNER TO mpoint;

/*  ===========  END : Adding Table System.ProductType_Tbl  ==================  */

/*  ===========  START : Adding producttype to Log.Transaction_Tbl  ==================  */
ALTER TABLE log.transaction_tbl ADD producttype INT DEFAULT 100 NOT NULL;
COMMENT ON COLUMN log.transaction_tbl.producttype IS 'Product type of transaction';
ALTER TABLE log.transaction_tbl
  ADD CONSTRAINT transaction_tbl_producttype_tbl_id_fk
FOREIGN KEY (producttype) REFERENCES system.producttype_tbl (id);

/*  ===========  END : Adding producttype to Log.Transaction_Tbl  ==================  */

/* =============== Added product tables ============ */




-- DROP TABLE client.producttype_tbl;

CREATE TABLE client.producttype_tbl
(
  id serial NOT NULL,
  productid integer NOT NULL,
  clientid integer NOT NULL,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  CONSTRAINT clientproducttype_pk PRIMARY KEY (id),
  CONSTRAINT client_fk FOREIGN KEY (clientid)
      REFERENCES client.client_tbl (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT product_fk FOREIGN KEY (productid)
      REFERENCES system.ProductType_Tbl(id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
ALTER TABLE client.producttype_tbl
  OWNER TO mpoint;

 
  
 /* ========== Removing as Moved to BRE =============*/
  
DROP TABLE client.rulecondition_tbl;
DROP TABLE client.routing_tbl;
DROP TABLE client.rule_tbl;
DROP TABLE system.condition_tbl;
DROP TABLE system.operator_tbl;



ALTER TABLE client.cardaccess_tbl ADD psp_type INT DEFAULT 1 NOT NULL;
ALTER TABLE client.cardaccess_tbl
  ADD CONSTRAINT cardaccess_tbl_processortype_tbl_id_fk
FOREIGN KEY (psp_type) REFERENCES system.processortype_tbl (id);
DROP INDEX client.cardaccess_card_country_uq RESTRICT;
UPDATE client.cardaccess_tbl
SET psp_type = psp_tbl.system_type
FROM system.psp_tbl
WHERE psp_tbl.id = cardaccess_tbl.pspid;
CREATE UNIQUE INDEX cardaccess_card_country_uq ON client.cardaccess_tbl (clientid, cardid, countryid, psp_type);




/*=========== Gateway Triggers ============*/

-- Table: system.triggerunit_tbl

-- DROP TABLE system.triggerunit_tbl;

CREATE TABLE system.triggerunit_tbl
(
  id serial NOT NULL,
  name character varying(200),
  description character varying(200),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  CONSTRAINT trigger_pk PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE system.triggerunit_tbl
  OWNER TO mpoint;
  
  
 -- Table: client.gatewaytrigger_tbl

-- DROP TABLE client.gatewaytrigger_tbl;

CREATE TABLE client.gatewaytrigger_tbl
(
  id serial NOT NULL,
  gatewayid integer,
  enabled boolean NOT NULL DEFAULT false,
  healthtriggerunit integer,
  healthtriggervalue integer,
  aggregationtriggerunit integer,
  clientid integer,
  aggregationtriggervalue integer,
  resetthresholdunit integer,
  resetthresholdvalue integer,
  created timestamp without time zone NOT NULL DEFAULT now(),
  modified timestamp without time zone NOT NULL DEFAULT now(),
  CONSTRAINT trigger_pk PRIMARY KEY (id),
  CONSTRAINT atriggerunit_fk FOREIGN KEY (aggregationtriggerunit)
      REFERENCES system.triggerunit_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT gateway_fk FOREIGN KEY (gatewayid)
      REFERENCES system.psp_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT htriggerunit_fk FOREIGN KEY (healthtriggerunit)
      REFERENCES system.triggerunit_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT triggeclient_fk FOREIGN KEY (clientid)
      REFERENCES client.client_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT ttriggerunit_fk FOREIGN KEY (resetthresholdunit)
      REFERENCES system.triggerunit_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE client.gatewaytrigger_tbl
  OWNER TO mpoint;


  
  /* ========= Gateway trigger system data ========== */

INSERT INTO system.triggerunit_tbl( id, name, description) VALUES (1, 'time', 'Time based triggers counted in seconds');
INSERT INTO system.triggerunit_tbl( id, name, description) VALUES (2, 'volume', 'Transaction based triggers counted in number of txns');

/* ========= Gateway trigger system data ========== */



/*=============== Gateway Stat Data -================ */

-- Table: system.statisticstype_tbl

-- DROP TABLE system.statisticstype_tbl;

CREATE TABLE system.statisticstype_tbl
(
  id serial NOT NULL,
  name character varying(200),
  description character varying(200),
  enabled boolean NOT NULL DEFAULT true,
  created timestamp without time zone NOT NULL DEFAULT now(),
  modified timestamp without time zone NOT NULL DEFAULT now(),
  CONSTRAINT stattype_pk PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE system.statisticstype_tbl
  OWNER TO mpoint;

  
  -- Table: client.gatewaystat_tbl

-- DROP TABLE client.gatewaystat_tbl;

CREATE TABLE client.gatewaystat_tbl
(
  id serial NOT NULL,
  gatewayid integer NOT NULL,
  clientid integer NOT NULL,
  statetypeid integer NOT NULL,
  statvalue integer NOT NULL,
  enabled boolean NOT NULL DEFAULT true,
  created timestamp without time zone NOT NULL DEFAULT now(),
  modified timestamp without time zone NOT NULL DEFAULT now(),
  reseton timestamp without time zone,
  CONSTRAINT stat_pk PRIMARY KEY (id),
  CONSTRAINT clientstat_fk FOREIGN KEY (clientid)
      REFERENCES client.client_tbl (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT gatewaystat_fk FOREIGN KEY (gatewayid)
      REFERENCES system.psp_tbl (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT stattype_fk FOREIGN KEY (statetypeid)
      REFERENCES system.statisticstype_tbl (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
ALTER TABLE client.gatewaystat_tbl
  OWNER TO mpoint;


/*===========================  Updating for gateway delete functionality   ======================*/  
ALTER TABLE client.gatewaytrigger_tbl ADD COLUMN status boolean NOT NULL DEFAULT false;
ALTER TABLE client.gatewaytrigger_tbl ALTER COLUMN enabled SET DEFAULT true ;

ALTER TABLE client.gatewaystat_tbl ALTER COLUMN statvalue TYPE numeric ;

/*=================== Moving triggers to BRE =================== */
ALTER TABLE client.gatewaytrigger_tbl DROP COLUMN healthtriggerunit ;
ALTER TABLE client.gatewaytrigger_tbl DROP COLUMN healthtriggervalue ;
ALTER TABLE client.gatewaytrigger_tbl DROP COLUMN resetthresholdunit ;
ALTER TABLE client.gatewaytrigger_tbl DROP COLUMN resetthresholdvalue ;
/*=================== Moving triggers to BRE =================== */
 

-- 2c2p alc Airline data improvement -- start --
-- Alter Log.Passenger Tbl to store additional passenger data

ALTER TABLE log.passenger_tbl
  ADD COLUMN title character varying(20);
ALTER TABLE log.passenger_tbl
  ADD COLUMN email character varying(50);
ALTER TABLE log.passenger_tbl
  ADD COLUMN mobile character varying(15);
ALTER TABLE log.passenger_tbl
  ADD COLUMN "country_id" character varying(3);

-- Alter Log.flight_tbl to store additional flight data
ALTER TABLE log.flight_tbl
  ADD COLUMN tag character varying(2);
ALTER TABLE log.flight_tbl
  ADD COLUMN "trip_count" character varying(2);
ALTER TABLE log.flight_tbl
  ADD COLUMN "service_level" character varying(2);
-- 2c2p alc Airline data improvement -- end --

-- To execute the above query first need to truncate the session_tbl data.
-- Run the "TRUNCATE TABLE log.session_tbl CASCADE;" before executing below query.
ALTER TABLE log.session_tbl ADD CONSTRAINT constraint_name UNIQUE (orderid);

ALTER TABLE system.ProductType_Tbl ADD COLUMN created timestamp without time zone DEFAULT now();
ALTER TABLE system.ProductType_Tbl ADD COLUMN  modified timestamp without time zone DEFAULT now();
ALTER TABLE system.ProductType_Tbl ADD COLUMN  enabled boolean DEFAULT true ; 




/*  ===========  START : Adding new product types in System.ProductType_Tbl ==================  */
INSERT INTO system.producttype_tbl (id, name) VALUES (100, 'Ticket');
INSERT INTO system.producttype_tbl (id, name) VALUES (200, 'Ancillary');
INSERT INTO system.producttype_tbl (id, name) VALUES (210, 'Insurance');
/*  ===========  END : Adding new product types in System.ProductType_Tbl ==================  */
INSERT INTO system.paymenttype_tbl (id, name) VALUES (6, 'Virtual');

INSERT INTO system.processortype_tbl (id, name) VALUES (5, 'Virtual');


/*  ===========  START : Adding New Processor Type  ==================  */
INSERT INTO system.processortype_tbl (id, name) VALUES (6, 'Merchant Plug-in');
/*  ===========  END : Adding New Processor Type  ==================  */

INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (42, 'NETS MPI',6);
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (840,42,'USD');
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 42, 'NETS MPI', '', '');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 42, '-1');
INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position) VALUES (10007, 8, true, 42, 200, 1, null);

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('NETS_3DVERIFICATION', 'true', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('sessiontype', '2', 10018, 'client', false); /* value 1- Normal Payment, 2 - Split Payment */

/* ========== Global Configuration for AliPay Chinese - Payment Method : START========== */
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (40, 'AliPay Chinese', 23, -1, -1, -1,4);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (40, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 156;


INSERT INTO System.PSP_Tbl (id, name, system_type) VALUES (43, 'AliPay - Chinese',4);
INSERT INTO System.PSPCurrency_Tbl (pspid, name,currencyid) VALUES (43,'CNY',156);


INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (40, 43);

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 43, 'AliPay Chinese', '', '');

INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 43, '-1');

-- Route AliPay Card to AliPay with country CHN
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, countryid) VALUES (10001, 40, 43, true, 609);

-- PSP config for SDK integration
--app id
INSERT INTO client.merchantsubaccount_tbl(
  accountid, pspid, name, enabled)
VALUES (100071, 43, '2016080600179506', true);


INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) SELECT 'key.app', 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDAtoXgD2JA0COk6Aa18Bhg5+tdKlq4Fs/5QJ4SrGXkElNIuHok1lKeIGfUc2eJK7IfVz5Lrl3GEAm4HyX+tytegvUjH68Cxria8ZKu9SzBVMpUc7RDKsfVNr9YmITWfMM59QWp4BaJAGPi9u4GXnlv8hiiJYaENPkTZFTCY7FX2ylCjhkfB7OLp8DVU6dqZm8DpeAuScpLCHfT2q7XCG5pd78GDYDUxXG8kQEWvSXqYMYHlSxRQUrD13rtgLdJQL2TfP1rROoR0GO6tw5683idx+l9jDvVNWbQ34mKWW33Vz4R0qDwQn0gkql96yAN9XuREUpiXsaddL5HT6PWUxd7AgMBAAECggEAP5X55kwtJyWGHUtRq4ZlBNSBHGR1OniMdrmTbqXjmLVTNZNo+e6do/8dQ0QwzVnVk/G9ZEtMNaXlDxN3/euCK9UZ/VTe8hOPpdA/jernsYLAn8ztlZvwA7HkwN7SNdNEt0LZc4u04891JdZEA2X4u68t4ZJwJ/8yj+ty7BDo2wuqKMJs4SMsqtW8mhUdto9C4AROul3GebHTC5/4dVfciCQ5ATZNkNqU8pLHCfQxNyM2P9gawQaycNrqhx5MJ2pSG0GpYxieo3xxN5k4L99DkON3WjExJerUJ9MQbYHmDM0ceh/5niLKxUOTttxYaeEsSRX7Sc+0owIoyQV/Neq2AQKBgQDxB+yjUKvZBhc2rlA/KENjO2VvPw+MJbNJqFlbTLmSoqsVnid0CP3ZpqICvVEzXNoMga+T2CWW3mfnIoisPSwoeiASRgTBrL3iiXTxCXmHGrTbJ61aw2/EKAxjCK1IFou+8wjNbkya8zj4G6BrXn6OoVNVgq2RIRry1y84U8h5GwKBgQDMrmf/2bkrJKXzYHtP3906Y00NaRC//7q/Y9JZca4E+aZKcUh3u61itAHSWRlq++e8/blV4PzJTk+GgmlfLSbwJMc48MH77WroYMYMSbRu59tdKVnFzbk0Mlo0CxGKMOG8+YPKoJgcO8QKSZJo2BOAj958Hxy5AVZXGNMiWo0hIQKBgAkme91XWq7KhGcXBwTeynAh+R/YDQcNB1lsgrfsmb7vXf9cGbNWBA0XPl9MQKDqjXycD8ZVFlg76UXlEbs4N0zyFfWbouKXZD4NadscuPhgEy2eu/4OHVgdDRtVYP6znGqLX3ItFctsIGWK5vQsijFv/nHonB4+W3+Mm8ZPp/SxAoGAOzSTtqk266jdK+oToUYjCvmgVym2A6OoVCY+uUqtyJiiJlRgXun1vGBPSpYlSRH2tW87BgFffadeT403h6Va5wnsaqcRpZrGWtNrVjCXtaDxjiAg7JuWX+fUucsd1rhPA8e0/I65kSkkisk/RX6DHaP/+i1RtJ4TaHwwznYc7qECgYEA43aTdt0OIlwfb/VsuIIOQxabAmUq4wro7DTkmlIN1HHB1U4qU3qL2ScRViTpHlkIycOaknB27NrzNTd4JM3+ahdHzYCrRyVrd1vLEKMecM0cgWWr8RQz4RK2jO+tUG4v6V4Q5sJJHJfFxNGyaSMM9G4Tk8BYAcFs5ZwjhNYkMh8=', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10007 AND pspid=43 ;


INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'key.html', 'MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAIBQnVnYfs/oHSZJZhE01Z9ukKRmEU1OrDFGggD9F9YLYQP+kPLSg2DcVVC1Xl4Yyjp2RhfSODXmQD+io2Pt+HUZ+3CMlkI0e1qiQhnfLNbNEIjq+RVsIFZNqPlo3Lg/hBqlPhqk4YfqOuoagMthyuSBZJZ3UwXsRHgdzfBAzyI/AgMBAAECgYB/uL6HefnwVOj+/Tx9kAu7YMDVA0vhmZfIjJhHB6Y8RqNQ6Im7SlO/jFHXvlCqdR6GxsfKWlPdQs1dCjR8+Zi+/jEPaGDmvYa7p4kNXgJ+6zY4rSMt1MC0Py5fVZ4J+75HdfSwmbcm3u8LkREidRBn0EKbwQ0SwQOZqb/T6scr8QJBALlJxw7Xh0Mx6Gs+L55j2iO+m5mhvrBZGt0nmCcz+HFoSo3oO5rHeBGUJF1eGrbbValC0j2wL0n1Wa7fVK0xKrsCQQCxSK4Lrdvrw3T3/t1kjVFXgkZ3JhWpFKPBroX/AsBhANcRVKMG5oh43dw5jMJFgQmWQ6QsKh5q52dnqoueL1hNAkBDjoHUiILZ3h2G1IKaNn/3nmyvREj5lVN1JRWV3Z4NA2CDgxQQaAAAMMpdfI0y9J+z+hgbw9xKE/niB62hBBc3AkACDDnebqqspXxTZQE/qRY4cYvI0orLgi6GDTMFCA4a0LyrOZQMf1syMjXaAFM6JExtDOj3jaD+UR/zpZepQxi9AkEAimPiAgmD9DQVSWXLyi2DLvJ8flOV6PFx3Fq0hx9P0VbScKdJy2ETSH4gTnm+CIfE/5VJP16+jfSVV3BlODhdug==', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10007 AND pspid=43 ;

-- Alipay Chinese PIDs --
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) SELECT 'pid.html', '2088102135220161', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10007 AND pspid=43 ;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) SELECT 'pid.app', '2088102170185364', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10007 AND pspid=43 ;

/* ========== Global Configuration for AliPay Chinese ENDS ========== */



/* ========== Product Type ============ */

INSERT INTO system.producttype_tbl( id, name, description, code )  VALUES (110, 'Airline Ticket', 'Flight Tickets', 'AIRTCKT');
INSERT INTO system.producttype_tbl( id, name, description, code )  VALUES (210, 'Airline Insurance', 'Insurance products purchased', 'INSRNC');

/* ========= Gateway trigger system data ========== */

INSERT INTO system.triggerunit_tbl( id, name, description) VALUES (1, 'time', 'Time based triggers counted in seconds');
INSERT INTO system.triggerunit_tbl( id, name, description) VALUES (2, 'volume', 'Transaction based triggers counted in number of txns');

/* ========= Gateway trigger system data ========== */

/* ========== Global Configuration for Citcon - WeChat Pay - Payment Method : START========== */
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (39, 'WeChat Pay', 23, -1, -1, -1,6);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (39, 0, 0);
INSERT INTO system.cardpricing_tbl ( pricepointid, cardid) VALUES ( -840, 39);

INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (41, 'Citcon',5);
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (840,41,'USD');
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (39, 41);
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 41, 'Citcon', '', '');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 41, '-1');
INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position,psp_type) VALUES (10007, 39, true, 41, 200, 1, null,5);

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('MERCHANT_API_TOKEN', '71D149972DDC436694922B912104C5A5', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10007 and pspid = 41), 'merchant');

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('MERCHANT_VENDOR', 'wechatpay', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10007 and pspid = 41), 'merchant');

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('ALLOW_DUPLICATES', 'no', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10007 and pspid = 41), 'merchant');

--QR Code timeout value in seconds
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('QR_CODE_TIMEOUT', '180', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10007 and pspid = 41), 'merchant');

--Virtual payment page timer value in mm:ss, this should be less than or equal to the QR code timeout property
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('VIRTUAL_PAYMENT_TIMER', '02:00', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10007 and pspid = 41), 'merchant');

--url to link wechat icon
INSERT INTO client.url_tbl(urltypeid, clientid, url)
VALUES (14, 10007, "https://s3-ap-southeast-1.amazonaws.com/cpmassets/payment/icons");

--Redirect URL for HPP integration - can be blank or accept url or as required for client configuration
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('WECHAT_CALLBACK_URL', '', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10007 and pspid = 41), 'merchant');

/*=========================End===================================== */




/* ================= Gateway Stat =============== */

INSERT INTO system.statisticstype_tbl(  id, name, description)    VALUES (1,'Txn Volume', 'Volume of Transactions thourgh a particular gateway for a specific client');
INSERT INTO system.statisticstype_tbl(  id, name, description)    VALUES (2,'Success Ratio', 'Succes vs. failure transactions using a gateway for a time period');
INSERT INTO system.statisticstype_tbl(  id, name, description)    VALUES (3,'Response Time', 'Avg response time of a gateway during txn authorization');






/* ========== CONFIGURATION FOR GOOGLE PAY - START ========== */
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (41, 'Google Pay', 19, -1, -1, -1,3);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (41, -1, -1);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 41, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 840;


INSERT INTO System.PSP_Tbl (id, name, system_type) VALUES (44, 'Google Pay',3);
INSERT INTO System.PSPCurrency_Tbl (pspid, name,currencyid) VALUES (44,'USD',840);

-- Enable Google Pay Wallet for WorldPay
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (4, 41);
-- Enable Google Pay Wallet for Google Pay PSP
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (44, 41);


INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10052, 44, 'BARJB0sDNz5hR1S7/3OdMlHoslZuiQ+uLDfVudq3p7HFbPZAX7yK0HUjeUnAxF6w9iplh0wONq7s4g7QbmOZVTo=', NULL, NULL);
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100071, 44, 'Google Pay');


--Enable WireCard for GPay
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid, psp_type) VALUES (10052, 41, 18,200,1);
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (18, 41);

/* ========== CONFIGURATION FOR GOOGLE PAY - END ========== */




-- 2c2p alc Airline data improvement -- start --
-- Alter Log.Passenger Tbl to store additional passenger data

ALTER TABLE log.passenger_tbl
  ADD COLUMN title character varying(20);
ALTER TABLE log.passenger_tbl
  ADD COLUMN email character varying(50);
ALTER TABLE log.passenger_tbl
  ADD COLUMN mobile character varying(15);
ALTER TABLE log.passenger_tbl
  ADD COLUMN "country_id" character varying(3);

-- Alter Log.flight_tbl to store additional flight data
ALTER TABLE log.flight_tbl
  ADD COLUMN tag character varying(2);
ALTER TABLE log.flight_tbl
  ADD COLUMN "trip_count" character varying(2);
ALTER TABLE log.flight_tbl
  ADD COLUMN "service_level" character varying(2);
-- 2c2p alc Airline data improvement -- end --

/*=========================End===================================== */


/* ========== CONFIGURE PPRO START ========== */

/* START: Adding CARD Configuration Entries */

INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (42, 'PPRO', 23, -1, -1, -1,4);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (42, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 42, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 840;


/* END: Adding CARD Configuration Entries */

/*START: Adding PSP entries to the PSP_Tbl table for PPRO */

INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (46, 'PPRO',4);

/*END: Adding PSP entries to the PSP_Tbl table for PPRO*/

/*START: Adding Currency entries to the PSPCurrency_Tbl table for PPRO*/

INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (840,46,'DKK');

/*END: Adding Currency entries to the PSPCurrency_Tbl table for PPRO*/

/* ========== CONFIGURE DEMO ACCOUNT FOR PPRO START ========== */
-- Wire-Card
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 46, '', '', '');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 46, '-1');

/* ========== CONFIGURE DEMO ACCOUNT FOR PPRO END ====== */


/*======= ADD NEW PROCESSOR TYPE FOR GATEWAY ======== */

INSERT INTO system.processortype_tbl (id, name) VALUES (7, 'Gateway');

/*======= END NEW PROCESSOR TYPE FOR GATEWAY ======== */

/* ========== CONFIGURE PPRO START ========== */

/* START: Adding CARD Configuration Entries FOR testing purpose only*/

INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (42, 'PPRO', 23, -1, -1, -1,4);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (42, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 42, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 840;


/* END: Adding CARD Configuration Entries */

/*START: Adding PSP entries to the PSP_Tbl table for PPRO */

INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (46, 'PPRO',7);

/*END: Adding PSP entries to the PSP_Tbl table for PPRO*/

/*START: Adding Currency entries to the PSPCurrency_Tbl table for PPRO*/

INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (840,46,'DKK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (458,46,'MYR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (978,46,'EUR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (608,46,'PHP');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (702,46,'SGD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (752,46,'SEK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (764,46,'THB');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (985,46,'PLN');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (203,46,'CZK');

/*END: Adding Currency entries to the PSPCurrency_Tbl table for PPRO*/

/* ========== CONFIGURE DEMO ACCOUNT FOR PPRO START ========== */
-- Wire-Card
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 46, 'CELLPOINTMOBILETESTCONTRACT', 'CELLPOINTTEST', '8eX67I13el8Q3LBF');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 46, '-1');

/* ========== CONFIGURE DEMO ACCOUNT FOR PPRO END ====== */

/* START : Additional Properties */

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) SELECT 'ppro-shared-secret', '', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10007 AND pspid=46 ;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) SELECT 'ppro-notification-secret', '', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10007 AND pspid=46 ;

/* END : Additional Properties */

/* ========== CONFIGURATION FOR GOOGLE PAY - END ========== */


/*-----------------Introducing new states for capturing 3DS approved/ rejected transactions: START ------------------*/
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (2006, '3d verification successful', 'Payment', '');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (2016, '3d verification failed', 'Payment', '');
/*-----------------Introducing new states for capturing 3DS approved/ rejected transactions: END ------------------*/

/* ----------------Adding Configurations for Modirum MPI - START ------------------------------ */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (47, 'MODIRUM MPI',6);
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (840,47,'USD');
  INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 47, 'MODIRUM MPI', '9449005362', '-----BEGIN PRIVATE KEY-----
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
bNwLDrkEV6VmYvJIHNDGkkY=
-----END PRIVATE KEY-----
');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 47, '-1');
INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position, psp_type) VALUES (10007, 1, true, 47, 200, 1, null, 6);
/* ----------------Adding Configurations for Modirum MPI - END ------------------------------ */


/*-----------------START : Enabling 3DS for merchant-------------------------*/
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('3DVERIFICATION', 'true', 10007, 'client');
/*-----------------END ; Enabling 3DS for merchant-------------------------*/
/* END: Adding CARD Configuration Entries */
/* ========== CONFIGURE AMEX START ========== */
/*START: Adding PSP entries to the PSP_Tbl table for AMEX*/

INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (45, 'Amex',2);

/*END: Adding PSP entries to the PSP_Tbl table for AMEX*/

/*START: Adding Currency entries to the PSPCurrency_Tbl table for AMEX*/

INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (208,45,'DKK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (752,45,'SEK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (578,45,'NOK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (826,45,'USD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (978,45,'EUR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (208,45,'DKK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (352,45,'ISK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (840,45,'USD');

/*END: Adding Currency entries to the PSPCurrency_Tbl table for AMEX*/

INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (1, 45, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, stateid, enabled) VALUES (10007, 1, 45, 200, 1, true);

/* ========== CONFIGURE DEMO ACCOUNT FOR AMEX START ========== */
-- Wire-Card
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 45, '9105bb4f-ae68-4768-9c3b-3eda968f57ea', '70000-APILUHN-CARD', '8mhwavKVb91T');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 45, '-1');

/* ========== CONFIGURE DEMO ACCOUNT FOR AMEX END ====== *//* END: Adding CARD Configuration Entries */


---Datacash MID
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, password) VALUES (10007, 17, 'SGBSABB01', 'merchant.SGBSABB01', 'bebd68b2fa491f807e40462a6f85617e');

/* ----------------Adding Configurations for CHUBB PSP - START ------------------------------ */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (48, 'CHUBB', 1);
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (702,48,'SGD');
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 48, 'CHUBB', '', '');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 48, '-1');
INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position, psp_type) VALUES (10007, 8, true, 48, 642, 1, null, 1);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (8, 48, true);
/* ----------------Adding Configurations for CHUBB PSP - END ------------------------------ */