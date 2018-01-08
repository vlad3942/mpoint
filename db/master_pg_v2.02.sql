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

/*  ===========  START : Adding communicationchannels to Client.Client_Tbl  ==================  */
ALTER TABLE client.client_tbl ADD COLUMN communicationchannels integer DEFAULT 0;
/*  ===========  END : Adding communicationchannels to Client.Client_Tbl  ==================  */
