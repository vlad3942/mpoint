

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