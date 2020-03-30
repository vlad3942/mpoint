
/* ========== Order AID - Billing summary:: CMP-3459 ========== */

---
CREATE TABLE log.billing_summary_tbl
(
  id serial NOT NULL,
  order_id integer NOT NULL,
  journey_ref character varying(50),
  bill_type character varying(25) NOT NULL,
  type_id integer NOT NULL,
  description character varying(50) NOT NULL,
  amount character varying(20),
  currency character varying(10) NOT NULL,
  created timestamp without time zone NOT NULL DEFAULT now(),
  modified timestamp without time zone NOT NULL DEFAULT now(),
  CONSTRAINT billing_summary_pk PRIMARY KEY (id),
  CONSTRAINT order_fk FOREIGN KEY (order_id)
      REFERENCES log.order_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITHOUT OIDS;

  ALTER TABLE log.billing_summary_tbl OWNER TO mpoint;
 