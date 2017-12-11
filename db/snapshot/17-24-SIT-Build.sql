
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

  
  
  

/* =============== Set up of client-currency for Malindo (Sample) ============ */

/* Bangladesh - USD */
INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid) VALUES (10007,302,840);
/* Nepal - USD */
INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid) VALUES (10007,639,840);
/* Pakistan - USD */
INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid) VALUES (10007,610,840);
  
  