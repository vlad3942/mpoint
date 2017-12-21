-- Table: system.condition_tbl

-- DROP TABLE system.condition_tbl;

CREATE TABLE system.condition_tbl
(
  id serial NOT NULL,
  name character varying(100),
  description character varying(255),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  type character(1),
  CONSTRAINT rulefactor_pk PRIMARY KEY (id),
  CONSTRAINT type_check CHECK (type = 's'::bpchar OR type = 'd'::bpchar)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE system.condition_tbl
  OWNER TO mpoint;

-- Table: system.operator_tbl

-- DROP TABLE system.operator_tbl;

CREATE TABLE system.operator_tbl
(
  id serial NOT NULL,
  name character varying(100),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  symbol character varying(100),
  CONSTRAINT operator_pk PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE system.operator_tbl
  OWNER TO mpoint;
  
  
 -- Table: client.rulecondition_tbl

-- DROP TABLE client.rulecondition_tbl;

CREATE TABLE client.rulecondition_tbl
(
  id serial NOT NULL,
  conditionid integer NOT NULL,
  conditionvalue character varying(255),
  relation character varying(100),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  clientid integer,
  CONSTRAINT rulefactor_pk PRIMARY KEY (id),
  CONSTRAINT client_fk FOREIGN KEY (clientid)
      REFERENCES client.client_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT condition_fk FOREIGN KEY (conditionid)
      REFERENCES system.condition_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE client.rulecondition_tbl
  OWNER TO mpoint;
 
  
-- Table: client.rule_tbl

-- DROP TABLE client.rule_tbl;

CREATE TABLE client.rule_tbl
(
  id serial NOT NULL,
  ruleid integer NOT NULL,
  clientid integer NOT NULL,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  ruleconditionid integer NOT NULL,
  CONSTRAINT gatewayrule_pk PRIMARY KEY (id),
  CONSTRAINT ruleclient_fk FOREIGN KEY (clientid)
      REFERENCES client.client_tbl (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT rulecondition_fk FOREIGN KEY (ruleconditionid)
      REFERENCES client.rulecondition_tbl (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
ALTER TABLE client.rule_tbl
  OWNER TO mpoint;
  
  
  -- Table: client.routing_tbl

-- DROP TABLE client.routing_tbl;

CREATE TABLE client.routing_tbl
(
  id serial NOT NULL,
  ruleid integer NOT NULL,
  operator character varying(100),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  gateway1 integer,
  gateway2 integer,
  gateway3 integer,
  gateway4 integer,
  priority integer,
  clientid integer,
  CONSTRAINT routing_pk PRIMARY KEY (id),
  CONSTRAINT client_fk FOREIGN KEY (clientid)
      REFERENCES client.client_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT gateway1topsp_fk FOREIGN KEY (gateway1)
      REFERENCES system.psp_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT gateway2topsp_fk FOREIGN KEY (gateway2)
      REFERENCES system.psp_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT gateway3topsp_fk FOREIGN KEY (gateway3)
      REFERENCES system.psp_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT gateway4topsp_fk FOREIGN KEY (gateway4)
      REFERENCES system.psp_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT routing_tbl_ruleid_gateway1_gateway2_gateway3_gateway4_prio_key UNIQUE (ruleid, gateway1, gateway2, gateway3, gateway4, priority)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE client.routing_tbl
  OWNER TO mpoint;
 
  
  
  
  