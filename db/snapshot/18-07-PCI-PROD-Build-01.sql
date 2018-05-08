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
/* ========== Product Type ============ */

INSERT INTO system.producttype_tbl( id, name )  VALUES (100, 'Ticket');
INSERT INTO system.producttype_tbl( id, name )  VALUES (200, 'Ancillary');
INSERT INTO system.producttype_tbl( id, name )  VALUES (210, 'Insurance');


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


ALTER TABLE system.ProductType_Tbl ADD COLUMN created timestamp without time zone DEFAULT now();
ALTER TABLE system.ProductType_Tbl ADD COLUMN  modified timestamp without time zone DEFAULT now();
ALTER TABLE system.ProductType_Tbl ADD COLUMN  enabled boolean DEFAULT true ; 


INSERT INTO system.paymenttype_tbl (id, name) VALUES (6, 'Virtual');
INSERT INTO system.processortype_tbl (id, name) VALUES (5, 'Virtual');

/*  ===========  START : Adding New Processor Type  ==================  */
INSERT INTO system.processortype_tbl (id, name) VALUES (6, 'Merchant Plug-in');

/* ========= Gateway trigger system data ========== */
INSERT INTO system.triggerunit_tbl( id, name, description) VALUES (1, 'time', 'Time based triggers counted in seconds');
INSERT INTO system.triggerunit_tbl( id, name, description) VALUES (2, 'volume', 'Transaction based triggers counted in number of txns');
/* ========= Gateway trigger system data ========== */


/* ================= Gateway Stat =============== */
INSERT INTO system.statisticstype_tbl(  id, name, description)    VALUES (1,'Txn Volume', 'Volume of Transactions thourgh a particular gateway for a specific client');
INSERT INTO system.statisticstype_tbl(  id, name, description)    VALUES (2,'Success Ratio', 'Succes vs. failure transactions using a gateway for a time period');
INSERT INTO system.statisticstype_tbl(  id, name, description)    VALUES (3,'Response Time', 'Avg response time of a gateway during txn authorization');
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

/*======= ADD NEW PROCESSOR TYPE FOR GATEWAY ======== */

INSERT INTO system.processortype_tbl (id, name) VALUES (7, 'Gateway');

/*======= END NEW PROCESSOR TYPE FOR GATEWAY ======== */

/*-----------------Introducing new states for capturing 3DS approved/ rejected transactions: START ------------------*/
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (2006, '3d verification successful', 'Payment', '');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (2016, '3d verification failed', 'Payment', '');
/*-----------------Introducing new states for capturing 3DS approved/ rejected transactions: END ------------------*/

/*-----------------START : Enabling DR for merchant : To be executed only after the Malindo client is created -------------------------*/
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('DR_SERVICE', 'true', 10018, 'client');
