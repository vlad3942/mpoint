CREATE TABLE Log.paymentsecureinfo_tbl
(
   id       SERIAL,
   txnid    INT4 NOT NULL,
   mdStatus TEXT,   
   mdErrorMsg TEXT,
   veresEnrolledStatus TEXT,   
   paresTxStatus TEXT,
   eci TEXT,
   cavv TEXT,
   cavvAlgorithm TEXT,
   md TEXT,
   PAResVerified TEXT,
   PAResSyntaxOK TEXT,
   protocol TEXT,
   cardType TEXT,
   CONSTRAINT payment_secure_pk PRIMARY KEY (id),
   CONSTRAINT payment_secure2transaction_FK FOREIGN KEY (txnid) REFERENCES log.transaction_tbl (id) ON UPDATE CASCADE ON DELETE CASCADE
) WITHOUT OIDS;
ALTER TABLE Log.paymentsecureinfo_tbl OWNER TO mpoint;


ALTER TABLE log.address_tbl add mobile_country_id varchar(4) null;
ALTER TABLE log.address_tbl add mobile varchar(15) null;
ALTER TABLE log.address_tbl add email varchar(50) null;

DROP INDEX log.externalreference_transaction_idx;
CREATE INDEX CONCURRENTLY externalreference_transaction_idx ON log.externalreference_tbl (txnid, externalid, pspid, type);
CREATE INDEX CONCURRENTLY passeneger_tbl_orderid_index ON log.passenger_tbl USING btree (order_id)

