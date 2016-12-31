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
  service_class character varying(10) NOT NULL,
  departure_airport character varying(10) NOT NULL,
  arrival_airport character varying(10) NOT NULL,
  airline_code character varying(10) NOT NULL,
  id serial NOT NULL,
  order_id integer NOT NULL,
  additional_data_id integer,
  arrival_date time without time zone NOT NULL,
  departure_date time without time zone NOT NULL,
  created timestamp without time zone NOT NULL DEFAULT now(),
  modified time without time zone NOT NULL DEFAULT now(),
  CONSTRAINT flight_pk PRIMARY KEY (id),
  CONSTRAINT additional_data_fk FOREIGN KEY (additional_data_id)
      REFERENCES log.additional_data_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT order_fk FOREIGN KEY (order_id)
      REFERENCES log.order_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITHOUT OIDS;

  
  
  
-- Table: log.passenger_tbl

-- DROP TABLE log.passenger_tbl;

CREATE TABLE log.passenger_tbl
(
  id serial NOT NULL,
  first_name character varying(20) NOT NULL,
  last_name character varying(20) NOT NULL,
  type character varying(10) NOT NULL,
  order_id integer NOT NULL,
  additional_data_id integer,
  created time without time zone NOT NULL DEFAULT now(),
  modified time without time zone NOT NULL DEFAULT now(),
  CONSTRAINT passenger_pk PRIMARY KEY (id),
  CONSTRAINT additional_fk FOREIGN KEY (additional_data_id)
      REFERENCES log.additional_data_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT order_fk FOREIGN KEY (order_id)
      REFERENCES log.order_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITHOUT OIDS;

  
  
  
  
-- Table: log.additional_data_tbl

-- DROP TABLE log.additional_data_tbl;

CREATE TABLE log.additional_data_tbl
(
  id serial NOT NULL,
  reference_id integer,
  name character varying(20),
  value character varying(20),
  type log.additional_data_ref,
  CONSTRAINT additional_data_pk PRIMARY KEY (id),
  CONSTRAINT ref_flight_fk FOREIGN KEY (reference_id)
      REFERENCES log.flight_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT ref_pax_fk FOREIGN KEY (reference_id)
      REFERENCES log.passenger_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT additional_data_uk UNIQUE (reference_id, name, type)
)
WITHOUT OIDS;