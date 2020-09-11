ALTER TABLE log.address_tbl add mobile_country_id varchar(4) null;
ALTER TABLE log.address_tbl add mobile varchar(15) null;
ALTER TABLE log.address_tbl add email varchar(50) null;

-- CMP-4323
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('PAYPAL_ORDER_NUMBER_PREFIX', 'Cebu Pacific Air - ', true, 10077, 'client', 2);
DELETE FROM client.additionalproperty_tbl where key = 'post_fraud_rule';
DELETE FROM client.additionalproperty_tbl where key = 'mpi_rule';
---2c2p-alc Rule---
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'post_fraud_rule', 'isPostFraudAttemp::=<pspid>=="40"
pspid::=(psp-config.@id)', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10077 AND pspid=40;

---First Data Rule---
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'post_fraud_rule', 'isPostFraudAttemp::=<status>=="1"OR<status>=="2"OR<status>=="4"
status::=(card.info-3d-secure.additional-data.param[@name=''status''])', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10077 AND pspid=62;
---WorldPay Rule for MPI---
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'mpi_rule', 'isSkippAuth::=<status>!=="1"AND<status>!=="2"AND<status>!=="4"AND<status>!=="5"AND<status>!=="6"
status::=(card.info-3d-secure.additional-data.param[@name=''status''])', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10077 AND pspid=4;
---WorldPay Rule for FRAUD---
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'post_fraud_rule', 'isPostFraudAttemp::=<status>=="1"OR<status>=="4"
status::=(card.info-3d-secure.additional-data.param[@name=''status''])', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10077 AND pspid=4;

update client.cardaccess_tbl set enabled = false where psp_type in (9,10) and cardid not in (7,8) and clientid = 10077;

DROP table Log.paymentsecureinfo_tbl;
CREATE TABLE Log.paymentsecureinfo_tbl
(
   id     SERIAL,
   txnid  INT4 NOT NULL,
   pspid  INT4 NOT NULL,
   status INT4,
   msg TEXT,
   veresEnrolledStatus TEXT,
   paresTxStatus TEXT,
   eci INT4,
   cavv TEXT,
   cavvAlgorithm INT4,
   protocol TEXT,
   CONSTRAINT payment_secure_pk PRIMARY KEY (id),
   CONSTRAINT payment_secure2transaction_FK FOREIGN KEY (txnid) REFERENCES log.transaction_tbl (id) ON UPDATE CASCADE ON DELETE CASCADE,
   CONSTRAINT payment_secure2psp_FK FOREIGN KEY (pspid) REFERENCES system.psp_tbl (id) ON UPDATE CASCADE ON DELETE CASCADE

) WITHOUT OIDS;
ALTER TABLE Log.paymentsecureinfo_tbl OWNER TO mpoint;
CREATE INDEX CONCURRENTLY paymentsecure_txn_idx ON log.paymentsecureinfo_tbl (txnid);