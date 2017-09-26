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

/*---------END : ADDED CHANGE FOR SUPPORTING CURRENCY SCHEMA-------------*/



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
	  