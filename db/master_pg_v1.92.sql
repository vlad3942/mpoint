/* ==================== Client SCHEMA START ==================== */
ALTER TABLE Client.CardAccess_tbl ADD position integer default NULL;
/* ==================== Client SCHEMA END ==================== */

CREATE INDEX CONCURRENTLY externalid_transaction_idx ON Log.Transaction_Tbl (extid, pspid);
/* ==================== LOG.ORDER_TBL SCHEMA START ==================== */
CREATE TABLE log.order_tbl
(
  id serial NOT NULL,
  txnid integer NOT NULL,
  countryid integer NOT NULL,
  amount integer,
  productsku character varying(40),
  productname character varying(40),
  productdescription text,
  productimageurl character varying(255),
  points integer,
  reward integer,
  quantity integer,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  CONSTRAINT order_pk PRIMARY KEY (id),
  CONSTRAINT order2country_fk FOREIGN KEY (countryid)
      REFERENCES system.country_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT order2txn_fk FOREIGN KEY (txnid)
      REFERENCES log.transaction_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT
)
WITH (
  OIDS=FALSE
);
ALTER TABLE log.order_tbl
  OWNER TO mpoint;

CREATE INDEX order_created_idx
  ON log.order_tbl
  USING btree
  (created);

CREATE INDEX order_transaction_idx
  ON log.order_tbl
  USING btree
  (id, txnid);
  
/* ==================== LOG.ORDER_TBL SCHEMA START ==================== */
