/*---------START : ADDED CHANGE FOR SUPPORTING CURRENCY SCHEMA-------------*/
-- Table: system.currency_tbl

-- DROP TABLE system.currency_tbl;

CREATE TABLE system.currency_tbl
(
  id serial NOT NULL,
  name character varying(100),
  code character(3),
  decimals integer,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  CONSTRAINT currency_pk PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE system.currency_tbl
  OWNER TO mpoint;


ALTER TABLE system.country_tbl ADD COLUMN alpha2code character(2);
ALTER TABLE system.country_tbl ADD COLUMN alpha3code character(3);
ALTER TABLE system.country_tbl ADD COLUMN code integer;
ALTER TABLE system.country_tbl ADD COLUMN currencyid integer;
ALTER TABLE system.country_tbl ADD CONSTRAINT Country2Currency_FK FOREIGN KEY (currencyid) REFERENCES System.Currency_Tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE system.country_tbl DROP COLUMN currency;

/*---------END : ADDED CHANGE FOR SUPPORTING CURRENCY SCHEMA-------------*/

/* ==================== ALTER TRANSACTION LOG START ==================== */
ALTER TABLE Log.Transaction_Tbl ADD COLUMN currencyid integer;
ALTER TABLE Log.Transaction_Tbl ADD CONSTRAINT Txn2Currency_FK FOREIGN KEY (currencyid)
REFERENCES System.currency_tbl (id)
ON UPDATE CASCADE ON DELETE RESTRICT;
/* ==================== ALTER TRANSACTION LOG END ==================== */

ALTER TABLE system.pspcurrency_tbl ADD COLUMN currencyid integer;
ALTER TABLE system.pspcurrency_tbl  ADD CONSTRAINT Psp2Currency_FK FOREIGN KEY (currencyid)
REFERENCES System.currency_tbl (id);

/* Run Alter Scripts to update currency Id before deleting country id column */
ALTER TABLE system.pspcurrency_tbl DROP COLUMN countryid ;


/* ================ Update pricepoint table  ===================*/

ALTER TABLE system.pricepoint_tbl ADD COLUMN currencyid integer;
ALTER TABLE system.pricepoint_tbl  ADD CONSTRAINT Price2Currency_FK FOREIGN KEY (currencyid)
REFERENCES System.currency_tbl (id);
ALTER TABLE system.pricepoint_tbl DROP COLUMN countryid;


/* ========= Create client.countrycurrency_tbl =============== */

-- Table: client.countrycurrency_tbl

-- DROP TABLE client.countrycurrency_tbl;

CREATE TABLE client.countrycurrency_tbl
(
  id serial NOT NULL,
  clientid integer NOT NULL,
  countryid integer NOT NULL,
  currencyid integer NOT NULL,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean,
  CONSTRAINT countrycurrency_pk PRIMARY KEY (id),
  CONSTRAINT client_fk FOREIGN KEY (clientid)
      REFERENCES client.client_tbl (id) MATCH SIMPLE
     ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT country_fk FOREIGN KEY (countryid)
      REFERENCES system.country_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT currency_fk FOREIGN KEY (currencyid)
      REFERENCES system.currency_tbl (id) MATCH SIMPLE
     ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE client.countrycurrency_tbl
  OWNER TO mpoint;

  /*  ===========  START : Adding column attempts to Log.Transaction_Tbl  ==================  */
ALTER TABLE Log.Transaction_Tbl ADD COLUMN attempt integer DEFAULT 1;
/*  ===========  END : Adding column attempts to Log.Transaction_Tbl  ==================  */

/*  ===========  START : Adding column preferred to Client.CardAccess_Tbl  ==================  */
ALTER TABLE Client.CardAccess_Tbl ADD COLUMN preferred boolean DEFAULT false;
/*  ===========  END : Adding column preferred to Client.CardAccess_Tbl  ==================  */


CREATE TABLE system.SessionType_tbl
(
  id SERIAL PRIMARY KEY,
  name VARCHAR(50),
  enable BOOLEAN DEFAULT TRUE
);
COMMENT ON TABLE system.SessionType_tbl IS 'Contains all session type like full payment session, split payment session and etc';


CREATE TABLE log.Session_tbl
(
  id SERIAL PRIMARY KEY,
  clientid INTEGER,
  accountid INTEGER,
  currencyid INTEGER,
  countryid INTEGER,
  stateid INTEGER,
  orderid VARCHAR(128) NOT NULL,
  amount DECIMAL NOT NULL,
  mobile NUMERIC NOT NULL,
  deviceid VARCHAR(128),
  ipaddress VARCHAR(15),
  externalid INTEGER,
  sessiontypeid INTEGER,
  expire TIMESTAMP(6) DEFAULT current_timestamp,
  created TIMESTAMP(6) DEFAULT current_timestamp,
  modified TIMESTAMP(6) DEFAULT current_timestamp,
  CONSTRAINT Session_tbl_client_tbl_id_fk FOREIGN KEY (clientid) REFERENCES client.client_tbl (id),
  CONSTRAINT Session_tbl_account_tbl_id_fk FOREIGN KEY (accountid) REFERENCES client.account_tbl (id),
  CONSTRAINT Session_tbl_currency_tbl_id_fk FOREIGN KEY (currencyid) REFERENCES system.currency_tbl (id),
  CONSTRAINT Session_tbl_country_tbl_id_fk FOREIGN KEY (countryid) REFERENCES system.country_tbl (id),
  CONSTRAINT Session_tbl_state_tbl_id_fk FOREIGN KEY (stateid) REFERENCES log.state_tbl (id),
  CONSTRAINT Session_tbl_sessiontype_tbl_id_fk FOREIGN KEY (sessiontypeid) REFERENCES system.SessionType_tbl (id)
);
COMMENT ON COLUMN log.Session_tbl.clientid IS 'Merchant Id';
COMMENT ON COLUMN log.Session_tbl.accountid IS 'Storefront Id';
COMMENT ON COLUMN log.Session_tbl.currencyid IS 'Currency of transaction';
COMMENT ON COLUMN log.Session_tbl.countryid IS 'Country of transaction';
COMMENT ON COLUMN log.Session_tbl.stateid IS 'State of session';
COMMENT ON COLUMN log.Session_tbl.amount IS 'Total amount for payment';
COMMENT ON COLUMN log.Session_tbl.externalid IS 'Profile id';
COMMENT ON COLUMN log.Session_tbl.sessiontypeid IS 'Session Type id';
COMMENT ON TABLE log.Session_tbl IS 'Session table act as master table for transaction. Split transactions will track by Session id';

ALTER TABLE log.transaction_tbl ADD sessionid INTEGER NULL;
ALTER TABLE log.transaction_tbl
  ADD CONSTRAINT transaction_tbl_session_tbl_id_fk
FOREIGN KEY (sessionid) REFERENCES log.session_tbl (id);
/*  ===========  START : Adding communicationchannels to Client.Client_Tbl  ==================  */
ALTER TABLE client.client_tbl ADD COLUMN communicationchannels integer DEFAULT 0;
/*  ===========  END : Adding communicationchannels to Client.Client_Tbl  ==================  */

ALTER TABLE system.SessionType_tbl  OWNER TO mpoint;
ALTER TABLE log.Session_tbl  OWNER TO mpoint;


/* =============== Added product tables ============ */

-- Table: system.producttype_tbl

-- DROP TABLE system.producttype_tbl;

CREATE TABLE system.producttype_tbl
(
  id serial NOT NULL,
  name character varying(100),
  code character varying(100),
  description character varying(255),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  CONSTRAINT producttype_pk PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE system.producttype_tbl
  OWNER TO mpoint;


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
      REFERENCES system.producttype_tbl(id) MATCH SIMPLE
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
 
