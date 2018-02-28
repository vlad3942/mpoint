

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
  CONSTRAINT product_pk PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE system.producttype_tbl
  OWNER TO mpoint;

-- Table: client.producttype_tbl

-- DROP TABLE client.producttype_tbl;

CREATE TABLE client.producttype_tbl
(
  id serial NOT NULL,
  productid integer NOT NULL,
  clientid integer NOT NULL,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  CONSTRAINT clientproduct_pk PRIMARY KEY (id),
  CONSTRAINT client_fk FOREIGN KEY (clientid)
      REFERENCES client.client_tbl (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT product_fk FOREIGN KEY (productid)
      REFERENCES system.product_tbl (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
ALTER TABLE client.producttype_tbl
  OWNER TO mpoint;


/* ========== Product Type ============ */

INSERT INTO system.producttype_tbl( id, name, description, code )  VALUES (110, 'Airline Ticket', 'Flight Tickets', 'AIRTCKT');
INSERT INTO system.producttype_tbl( id, name, description, code )  VALUES (210, 'Airline Insurance', 'Insurance products purchased', 'INSRNC');

 
 /* ========== Removing as Moved to BRE =============*/
  
DROP TABLE client.rulecondition_tbl;
DROP TABLE client.routing_tbl;
DROP TABLE client.rule_tbl;
DROP TABLE system.condition_tbl;
DROP TABLE system.operator_tbl;