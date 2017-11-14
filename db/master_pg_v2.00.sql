-------------  Airline Data ----------------



-- Type: log.additional_data_ref

-- DROP TYPE log.additional_data_ref;

CREATE TYPE log.additional_data_ref AS ENUM
   ('Flight',
    'Passenger');


-- Table: log.flight_tbl

-- DROP TABLE log.flight_tbl;

CREATE TABLE log.flight_tbl
(
 id serial NOT NULL,
  service_class character varying(10) NOT NULL,
  departure_airport character varying(10) NOT NULL,
  arrival_airport character varying(10) NOT NULL,
  airline_code character varying(10) NOT NULL,
  order_id integer NOT NULL,
  arrival_date timestamp without time zone NOT NULL,
  departure_date timestamp without time zone NOT NULL,
  created timestamp without time zone NOT NULL DEFAULT now(),
  modified timestamp without time zone NOT NULL DEFAULT now(),
  flight_number character varying(20),
  CONSTRAINT flight_pk PRIMARY KEY (id),
  CONSTRAINT order_fk FOREIGN KEY (order_id)
      REFERENCES log.order_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITHOUT OIDS;

  ALTER TABLE log.flight_tbl
  OWNER TO mpoint;
  
  
  
-- Table: log.passenger_tbl

-- DROP TABLE log.passenger_tbl;

CREATE TABLE log.passenger_tbl
(
   id serial NOT NULL,
  first_name character varying(20) NOT NULL,
  last_name character varying(20) NOT NULL,
  type character varying(10) NOT NULL,
  order_id integer NOT NULL,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  CONSTRAINT passenger_pk PRIMARY KEY (id),
  CONSTRAINT order_fk FOREIGN KEY (order_id)
      REFERENCES log.order_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITHOUT OIDS;

  ALTER TABLE log.passenger_tbl
  OWNER TO mpoint;
    
  
-- Table: log.additional_data_tbl

-- DROP TABLE log.additional_data_tbl;

CREATE TABLE log.additional_data_tbl
(
  id serial NOT NULL,
  name character varying(20),
  value character varying(20),
  type log.additional_data_ref,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  externalid integer,
  CONSTRAINT additional_data_pk PRIMARY KEY (id)
)
WITHOUT OIDS;

  ALTER TABLE log.additional_data_tbl
  OWNER TO mpoint;

-- Table: create type
  CREATE TYPE log.address_tbl_ref AS ENUM
   ('order',
    'transaction');
    
-- Table: log.address_tbl

CREATE TABLE log.address_tbl
(
  id serial NOT NULL,
  name character varying(200),
  street text,
  street2 text,
  city character varying(200),
  state character varying(200),
  country character varying(200),
  zip character varying(200),
  reference_id integer,
  reference_type log.address_tbl_ref,
  CONSTRAINT address_pk PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE log.address_tbl
  OWNER TO mpoint;

ALTER TABLE log.order_tbl
  OWNER TO mpoint;
  
  -- Table: system.processortype_tbl

-- DROP TABLE system.processortype_tbl;

CREATE TABLE system.processortype_tbl
(
  id serial NOT NULL,
  name character varying(50),
  CONSTRAINT id_pk PRIMARY KEY (id),
  CONSTRAINT iduk UNIQUE (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE system.processortype_tbl
  OWNER TO mpoint;

-- Column: system_type

-- ALTER TABLE system.psp_tbl DROP COLUMN system_type;

ALTER TABLE system.psp_tbl ADD COLUMN system_type integer;
ALTER TABLE system.psp_tbl ALTER COLUMN system_type SET NOT NULL;

   -- Foreign Key: system.psptoproccessingtype_fk

-- ALTER TABLE system.psp_tbl DROP CONSTRAINT psptoproccessingtype_fk;

ALTER TABLE system.psp_tbl
  ADD CONSTRAINT psptoproccessingtype_fk FOREIGN KEY (system_type)
      REFERENCES system.processortype_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;
	  
--Add coloumn device-id in log.Transaction_tbl
ALTER TABLE log.transaction_tbl ADD COLUMN deviceid character varying(50);

 /*
 *
 * Created a new Table in the client schema {Client.GoMobileConfiguration_Tbl} to retain gomobile configuration
 * for every channel - CMP-1820
 *
 */
-- Table: client.gomobileconfiguration_tbl

-- DROP TABLE client.gomobileconfiguration_tbl;

CREATE TABLE client.gomobileconfiguration_tbl
(
  id serial NOT NULL,
  clientid integer NOT NULL,
  name character varying(100),
  value character varying(100),
  channel character varying(5),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  CONSTRAINT gomobileconfiguration_pk PRIMARY KEY (id),
  CONSTRAINT gomobileconfiguration2client_fk FOREIGN KEY (clientid)
      REFERENCES client.client_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE client.gomobileconfiguration_tbl
  OWNER TO mpoint;

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


/* ==================== SYSTEM PAYMENT MODE START ==================== */
-- Table: system.paymentmode_tbl

-- DROP TABLE system.paymentmode_tbl;

CREATE TABLE system.paymentmode_tbl
(
  id serial NOT NULL,
  name character(10),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  CONSTRAINT paymentmode_pk PRIMARY KEY (id)
);
ALTER TABLE system.paymentmode_tbl
  OWNER TO postgres;
/* ==================== SYSTEM PAYMENT MODE END ==================== */

/* ==================== CLIENT COUNTRY CONFIG START ==================== */
-- Table: client.currencyconfig_tbl

-- DROP TABLE client.currencyconfig_tbl

CREATE TABLE client.currencyconfig_tbl
(
  id serial NOT NULL,
  clientid integer NOT NULL,
  countryid integer,
  currency character(3),
  decimals integer,
  mode integer,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  CONSTRAINT currencyconfig_pk PRIMARY KEY (id),
  CONSTRAINT currencyconfig2client_fk FOREIGN KEY (clientid)
      REFERENCES client.client_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT currencyconfig2countryid_fk FOREIGN KEY (countryid)
      REFERENCES system.country_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT currencyconfig2mode_fk FOREIGN KEY (mode)
      REFERENCES system.paymentmode_tbl (id) MATCH SIMPLE
);

ALTER TABLE client.currencyconfig_tbl
  OWNER TO postgres;

/* ==================== CLIENT COUNTRY CONFIG END ==================== */

/* ==================== ALTER SYSTEM CARD START ==================== */
ALTER TABLE system.Card_tbl ADD COLUMN mode integer;
ALTER TABLE system.Card_tbl ADD CONSTRAINT Card2PaymentMode_FK FOREIGN KEY (mode)
REFERENCES System.paymentmode_tbl (id)
ON UPDATE CASCADE ON DELETE RESTRICT;
/* ==================== ALTER SYSTEM CARD END ==================== */


/* ==================== ALTER TRANSACTION LOG START ==================== */
ALTER TABLE Log.Transaction_Tbl ADD COLUMN currencyid integer;
ALTER TABLE Log.Transaction_Tbl ADD CONSTRAINT Txn2Currency_FK FOREIGN KEY (currencyid)
REFERENCES System.currency_tbl (id)
ON UPDATE CASCADE ON DELETE RESTRICT;
/* ==================== ALTER TRANSACTION LOG END ==================== */

ALTER TABLE system.pspcurrency_tbl ADD COLUMN currencyid integer;
/* Run Alter Scripts to update currency Id before deleting country id column */
ALTER TABLE system.pspcurrency_tbl DROP COLUMN countryid integer;


/* ================ Update pricepoint table  ===================*/

ALTER TABLE system.pricepoint_tbl ADD COLUMN currencyid integer;

ALTER TABLE system.pricepoint_tbl  ADD CONSTRAINT Price2Currency_FK FOREIGN KEY (currencyid)
REFERENCES System.currency_tbl (id);



ALTER TABLE system.pricepoint_tbl DROP COLUMN countryid;

ALTER TABLE system.cardpricing_tbl ADD COLUMN currencyid integer;

ALTER TABLE system.cardpricing_tbl  ADD CONSTRAINT Card2Currency_FK FOREIGN KEY (currencyid)
REFERENCES System.currency_tbl (id);

 /*
 *
 * Created a new Table in the client schema {Client.AdditionalProperty_tbl} to retain additional client and merchant configuration
 * for every channel - CMP-1862
 *
 */
-- Table: client.additionalproperty_tbl

-- DROP TABLE client.additionalproperty_tbl;

CREATE TABLE client.additionalproperty_tbl
(
  id serial NOT NULL,
  key character varying(200) NOT NULL,
  value character varying(4000) NOT NULL,
  modified timestamp without time zone DEFAULT now(),
  created timestamp without time zone DEFAULT now(),
  enabled boolean NOT NULL DEFAULT true,
  externalid integer NOT NULL,
  type VARCHAR(20) NOT NULL,
  CONSTRAINT additionalprop_pk PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE client.additionalproperty_tbl
  OWNER TO mpoint;


ALTER TABLE log.transaction_tbl ADD mask VARCHAR(20) NULL;
ALTER TABLE log.transaction_tbl ADD expiry VARCHAR(5) NULL;
ALTER TABLE log.transaction_tbl ADD token CHARACTER VARYING(512) COLLATE pg_catalog."default" NULL;
ALTER TABLE log.transaction_tbl ADD authOriginalData CHARACTER VARYING(512) NULL;


ALTER TABLE enduser.address_tbl DROP CONSTRAINT address2state_fk;
ALTER TABLE enduser.address_tbl DROP stateid;
ALTER TABLE enduser.address_tbl ADD state VARCHAR(200);


INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (2004, 'Payment approved for partial amount', 'Payment', '');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (2005, '3d verification required for Authorization', 'Payment', '');
