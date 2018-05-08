DELETE FROM 
  Client.CardAccess_Tbl 
where 
  id in (
    select 
      MAX(id) as ID 
    from 
      client.cardaccess_tbl as CA 
      inner join (
        select 
          clientid, 
          cardid, 
          count(1) as duplicates 
        from 
          Client.CardAccess_Tbl 
        where 
          countryid is NULL 
        group by 
          clientid, 
          cardid 
        HAVING 
          COUNT(*) > 1
      ) as P on CA.clientid = P.clientid 
      and CA.cardid = P.cardid 
    GROUP BY 
      CA.clientid, 
      CA.cardid
  );
  
  Delete from 
  Client.CardAccess_Tbl 
where 
  id in (
    select 
      MAX(id) as ID 
    from 
      client.cardaccess_tbl as CA 
      inner join (
        select 
          clientid, 
          cardid, 
          countryid, 
          count(1) as duplicates 
        from 
          Client.CardAccess_Tbl 
        group by 
          clientid, 
          cardid, 
          countryid 
        HAVING 
          COUNT(*) > 1
      ) as P on CA.clientid = P.clientid 
      and CA.cardid = P.cardid 
      and CA.countryid = P.countryid 
    GROUP BY 
      CA.clientid, 
      CA.cardid, 
      CA.countryid
  );


ALTER TYPE LOG.ADDITIONAL_DATA_REF ADD VALUE 'Order';

/*  ===========  START : Adding column attempts to Log.Transaction_Tbl  ==================  */
ALTER TABLE Log.Transaction_Tbl ADD COLUMN attempt integer DEFAULT 1;
/*  ===========  END : Adding column attempts to Log.Transaction_Tbl  ==================  */

/*  ===========  START : Adding column preferred to Client.CardAccess_Tbl  ==================  */
ALTER TABLE Client.CardAccess_Tbl ADD COLUMN preferred boolean DEFAULT false;
/*  ===========  END : Adding column preferred to Client.CardAccess_Tbl  ==================  */


INSERT INTO log.state_tbl (id, name, module) VALUES (4001, 'Session Created', 'Payment');
INSERT INTO log.state_tbl (id, name, module) VALUES (4010, 'Session Expire', 'Payment');
INSERT INTO log.state_tbl (id, name, module) VALUES (4020, 'Session Decline (fail)', 'Payment');
INSERT INTO log.state_tbl (id, name, module) VALUES (4030, 'Session Complete', 'Payment');


CREATE TABLE system.SessionType_tbl
(
    id SERIAL PRIMARY KEY,
    name VARCHAR(50),
    enable BOOLEAN DEFAULT TRUE
);
COMMENT ON TABLE system.SessionType_tbl IS 'Contains all session type like full payment session, split payment session and etc';

INSERT INTO system.sessiontype_tbl (id, name) VALUES (1, 'Full Payment Session');
INSERT INTO system.sessiontype_tbl (id, name) VALUES (2, 'Split Payment Session');

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


INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('sessiontype', '2', 10018, 'client', false); /* value 1- Normal Payment, 2 - Split Payment */

/*  ===========  START : Adding Default value to   ==================  */
UPDATE Client.Client_Tbl SET communicationchannels = 5;
UPDATE client.merchantsubaccount_tbl SET name='Default' where pspid=13


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

 
  

 /*
  *  Set up Scripts
  */ 
  
  
  /*
 * Static master data for defining conditions supported by Rules to define dynamic routing
 * 
 */

 INSERT INTO System.condition_tbl (id,name,description,type) values (1,'Amount','Amount value ','s');
 INSERT INTO System.condition_tbl (id,name,description,type) values (2,'Currency','Currency numeric ISO 4217 code ','s');
 INSERT INTO System.condition_tbl (id,name,description,type) values (3,'Binrange','Card bin range','s');
 INSERT INTO System.condition_tbl (id,name,description,type) values (4,'Card Scheme','Card Network','s');
 INSERT INTO System.condition_tbl (id,name,description,type) values (5,'Volume','Transaction volume','d');
 INSERT INTO System.condition_tbl (id,name,description,type) values (6,'Product','Type of product e.g Anciliary, Insurance etc','s');

 
 /*
  * Static master data for defining relation between conditions and values
  */

INSERT INTO system.operator_tbl (id,name,symbol) values (1,'Greater than','gt&amp;');
INSERT INTO system.operator_tbl (id,name,symbol) values (2,'Less than','lt&amp;');
INSERT INTO system.operator_tbl (id,name,symbol) values (3,'Greater Than Equals To','gt&amp;=');
INSERT INTO system.operator_tbl (id,name,symbol) values (4,'Less Than Equals To','lt&amp;=');
INSERT INTO system.operator_tbl (id,name,symbol) values (5,'Equals','==');
INSERT INTO system.operator_tbl (id,name,symbol) values (6,'AND','&amp;&amp;');
INSERT INTO system.operator_tbl (id,name,symbol) values (7,'OR','||');



-------- Update operator table ----------

UPDATE system.operator_tbl SET symbol='&gt;' where id=1 ;
UPDATE system.operator_tbl SET symbol='&lt;' where id=2 ;
UPDATE system.operator_tbl SET symbol='&gt;=' where id=3 ;
UPDATE system.operator_tbl SET symbol='&lt;=' where id=4 ;


INSERT INTO system.operator_tbl (id,name,symbol) values (8,'Not Equals','!=');
