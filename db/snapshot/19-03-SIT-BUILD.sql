/* ==================== ADDING WALLET ID IN THE LOG.TRANSACTION_TBL START ==================== */
ALTER TABLE Log.Transaction_Tbl ADD COLUMN walletid integer DEFAULT NULL;
/* ==================== ADDING WALLET ID IN THE LOG.TRANSACTION_TBL END ==================== */


/* ==================== ADDING A NEW SCHEMA FOR LOG.EXTERNALREFERENCE_TBL ======================== */
CREATE TABLE log.externalreference_tbl
(
  id serial NOT NULL,
  txnid integer NOT NULL,
  externalid bigint NOT NULL,
  pspid integer NOT NULL,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  CONSTRAINT externalreference_pk PRIMARY KEY (id),
  CONSTRAINT externalref2txn_fk FOREIGN KEY (txnid)
      REFERENCES log.transaction_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT externalref2psp_fk FOREIGN KEY (pspid)
      REFERENCES system.psp_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE log.message_tbl
  OWNER TO mpoint;


CREATE INDEX external_reference_index
  ON log.externalreference_tbl
  USING btree
  (externalid);
/* ==================== ADDING A NEW SCHEMA FOR LOG.EXTERNALREFERENCE_TBL ======================== */
