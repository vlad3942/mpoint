-- Table: system.routefeature_tbl

CREATE TABLE system.routefeature_tbl
(
  id serial NOT NULL,
  featurename character varying(150),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  CONSTRAINT routefeature_pk PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE system.routefeature_tbl
  OWNER TO mpoint;

-- Index: system.routefeature_tbl_id_idx

-- DROP INDEX system.routefeature_tbl_id_idx;

CREATE INDEX routefeature_tbl_id_idx
  ON system.routefeature_tbl
  USING btree
  (id);


-- Table: client.routefeature_tbl

CREATE TABLE client.routefeature_tbl
(
  id serial NOT NULL,
  clientid integer NOT NULL,
  routeconfigid integer NOT NULL,
  featureid integer NOT NULL,
  enabled boolean DEFAULT true,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  CONSTRAINT routefeature_pk PRIMARY KEY (id),
  CONSTRAINT routefeature_tbl_clientid_fkey FOREIGN KEY (clientid)
      REFERENCES client.client_tbl (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT routefeature_tbl_featureid_fkey FOREIGN KEY (featureid)
      REFERENCES system.routefeature_tbl (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
ALTER TABLE client.routefeature_tbl
  OWNER TO mpoint;

-- Index: client.routefeature_tbl_clientid_idx

-- DROP INDEX client.routefeature_tbl_clientid_idx;

CREATE INDEX routefeature_tbl_clientid_idx
  ON client.routefeature_tbl
  USING btree
  (clientid);

-- Index: client.routefeature_tbl_featureid_idx

-- DROP INDEX client.routefeature_tbl_featureid_idx;

CREATE INDEX routefeature_tbl_featureid_idx
  ON client.routefeature_tbl
  USING btree
  (featureid);

-- Index: client.routefeature_tbl_routeconfigid_idx

-- DROP INDEX client.routefeature_tbl_routeconfigid_idx;

CREATE INDEX routefeature_tbl_routeconfigid_idx
  ON client.routefeature_tbl
  USING btree
  (routeconfigid);



-- Table: client.routeconfig_tbl

CREATE TABLE client.routeconfig_tbl
(
  id serial NOT NULL,
  routeid integer NOT NULL,
  name character varying(100),
  capturetype smallint,
  mid character varying(150),
  countryid integer,
  currencyid integer,
  username character varying(50),
  password character varying(4000),
  enabled boolean DEFAULT true,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  CONSTRAINT routeconfig_tbl_pkey PRIMARY KEY (id),
  CONSTRAINT routeconfig_tbl_countryid_fkey FOREIGN KEY (countryid)
      REFERENCES system.country_tbl (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT routeconfig_tbl_currencyid_fkey FOREIGN KEY (currencyid)
      REFERENCES system.currency_tbl (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT routeconfig_tbl_routeid_fkey FOREIGN KEY (routeid)
      REFERENCES client.route_tbl (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
ALTER TABLE client.routeconfig_tbl
  OWNER TO mpoint;

-- Index: client.routeconfig_tbl_countryid_idx

-- DROP INDEX client.routeconfig_tbl_countryid_idx;

CREATE INDEX routeconfig_tbl_countryid_idx
  ON client.routeconfig_tbl
  USING btree
  (countryid);

-- Index: client.routeconfig_tbl_currencyid_idx

-- DROP INDEX client.routeconfig_tbl_currencyid_idx;

CREATE INDEX routeconfig_tbl_currencyid_idx
  ON client.routeconfig_tbl
  USING btree
  (currencyid);

-- Index: client.routeconfig_tbl_routeid_idx

-- DROP INDEX client.routeconfig_tbl_routeid_idx;

CREATE INDEX routeconfig_tbl_routeid_idx
  ON client.routeconfig_tbl
  USING btree
  (routeid);




-- Table: client.route_tbl

CREATE TABLE client.route_tbl
(
  id serial NOT NULL,
  clientid integer NOT NULL,
  providerid integer NOT NULL,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  CONSTRAINT route_tbl_pkey PRIMARY KEY (id),
  CONSTRAINT route_tbl_clientid_fkey FOREIGN KEY (clientid)
      REFERENCES client.client_tbl (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT route_tbl_providerid_fkey FOREIGN KEY (providerid)
      REFERENCES system.psp_tbl (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
ALTER TABLE client.route_tbl
  OWNER TO postgres;

-- Index: client.route_tbl_clientid_idx

-- DROP INDEX client.route_tbl_clientid_idx;

CREATE INDEX route_tbl_clientid_idx
  ON client.route_tbl
  USING btree
  (clientid);

-- Index: client.route_tbl_providerid_idx

-- DROP INDEX client.route_tbl_providerid_idx;

CREATE INDEX route_tbl_providerid_idx
  ON client.route_tbl
  USING btree
  (providerid);

