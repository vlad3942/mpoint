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