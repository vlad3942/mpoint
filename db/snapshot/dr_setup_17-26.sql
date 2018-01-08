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
  
  
  
  
-- Tables in Client Schema -

  
  -- Table: client.rule_tbl

-- DROP TABLE client.rule_tbl;

CREATE TABLE client.rule_tbl
(
  id serial NOT NULL,
  clientid integer NOT NULL,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  operatorid integer,
  name character varying(255),
  priority integer,
  CONSTRAINT gatewayrule_pk PRIMARY KEY (id),
  CONSTRAINT operator_fk FOREIGN KEY (operatorid)
      REFERENCES system.operator_tbl (id) MATCH SIMPLE
       ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT ruleclient_fk FOREIGN KEY (clientid)
      REFERENCES client.client_tbl (id) MATCH SIMPLE
       ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE client.rule_tbl
  OWNER TO mpoint;

  
-- Table: client.rulecondition_tbl

-- DROP TABLE client.rulecondition_tbl;

CREATE TABLE client.rulecondition_tbl
(
  id serial NOT NULL,
  conditionid integer NOT NULL,
  conditionvalue character varying(255),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  clientid integer,
  operatorid integer,
  ruleid integer,
  CONSTRAINT rulefactor_pk PRIMARY KEY (id),
  CONSTRAINT client_fk FOREIGN KEY (clientid)
      REFERENCES client.client_tbl (id) MATCH SIMPLE
        ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT condition_fk FOREIGN KEY (conditionid)
      REFERENCES system.condition_tbl (id) MATCH SIMPLE
       ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT relation_fk FOREIGN KEY (operatorid)
      REFERENCES system.operator_tbl (id) MATCH SIMPLE
        ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT rule_fk FOREIGN KEY (ruleid)
      REFERENCES client.rule_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE client.rulecondition_tbl
  OWNER TO mpoint;

 
 -- Table: client.routing_tbl

-- DROP TABLE client.routing_tbl;

CREATE TABLE client.routing_tbl
(
  id serial NOT NULL,
  ruleid integer NOT NULL,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  clientid integer,
  gatewayid integer,
  preference integer,
  CONSTRAINT routing_pk PRIMARY KEY (id),
  CONSTRAINT client_fk FOREIGN KEY (clientid)
      REFERENCES client.client_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT gateway_fk FOREIGN KEY (gatewayid)
      REFERENCES system.psp_tbl (id) MATCH SIMPLE
       ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT rulegateway_uk UNIQUE (ruleid, gatewayid, preference)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE client.routing_tbl
  OWNER TO mpoint;

 
  

 /**
  *  Set up Scripts
  */ 
  
  
  /** 
 * Static master data for defining conditions supported by Rules to define dynamic routing
 * 
 */

 INSERT INTO System.condition_tbl (id,name,description,type) values (1,'Amount','Amount value ','s');
 INSERT INTO System.condition_tbl (id,name,description,type) values (2,'Currency','Currency numeric ISO 4217 code ','s');
 INSERT INTO System.condition_tbl (id,name,description,type) values (3,'Binrange','Card bin range','s');
 INSERT INTO System.condition_tbl (id,name,description,type) values (4,'Card Scheme','Card Network','s');
 INSERT INTO System.condition_tbl (id,name,description,type) values (5,'Volume','Transaction volume','d');
 INSERT INTO System.condition_tbl (id,name,description,type) values (6,'Product','Type of product e.g Anciliary, Insurance etc','s');

 
 /**
  * Static master data for defining relation between conditions and values
  */

INSERT INTO system.operator_tbl (id,name,symbol) values (1,'Greater than','gt&amp;');
INSERT INTO system.operator_tbl (id,name,symbol) values (2,'Less than','lt&amp;');
INSERT INTO system.operator_tbl (id,name,symbol) values (3,'Greater Than Equals To','gt&amp;=');
INSERT INTO system.operator_tbl (id,name,symbol) values (4,'Less Than Equals To','lt&amp;=');
INSERT INTO system.operator_tbl (id,name,symbol) values (5,'Equals','==');
INSERT INTO system.operator_tbl (id,name,symbol) values (6,'AND','&amp;&amp;');
INSERT INTO system.operator_tbl (id,name,symbol) values (7,'OR','||');


/**
 * Enable DR service for a client 
 */

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('DR_SERVICE', 'true', 10007, 'client');

