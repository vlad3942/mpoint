---- Installments Feature

ALTER TABLE system.psp_tbl ADD installment INT DEFAULT 0 NOT NULL;

COMMENT ON COLUMN system.psp_tbl.installment
IS
'Default 0 - No installment option
1 - Offline Installment';

ALTER TABLE client.client_tbl ADD installment INT DEFAULT 0 NULL;
COMMENT ON COLUMN client.client_tbl.installment IS 'Default to 0 installment not enabled
1 - offline Installments';

ALTER TABLE client.client_tbl ADD max_installments INT DEFAULT 0 NULL;
COMMENT ON COLUMN client.client_tbl.max_installments IS 'Max number of installments allowed,
Usually set by Acq';
ALTER TABLE client.client_tbl ADD installment_frequency INT DEFAULT 0 NULL;
COMMENT ON COLUMN client.client_tbl.installment_frequency IS 'defines the time frame for installment,
like 1- monthly, 3 - quarterly, 6 - semiannual.
For merchant financed is usually monthly ';

ALTER TABLE log.transaction_tbl ADD installment_value INT DEFAULT 0 NULL;
COMMENT ON COLUMN log.transaction_tbl.installment_value IS 'Installment value is the number of installments selected by the user';

---- CMP-2807
alter table client.additionalproperty_tbl
	add scope int default 0;

comment on column client.additionalproperty_tbl.scope is 'Scope of properties
0 - Internal
1 - Private
2 - Public';

update client.additionalproperty_tbl set scope = 2;


---- ADDING WALLET ID IN THE LOG.TRANSACTION_TBL START ----
ALTER TABLE Log.Transaction_Tbl ADD COLUMN walletid integer DEFAULT NULL;
---- ADDING WALLET ID IN THE LOG.TRANSACTION_TBL END ----


---- ADDING A NEW SCHEMA FOR LOG.EXTERNALREFERENCE_TBL ----
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

---- Consolidated script for CONFIGURING GOOGLE PAY - START
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (41, 'Google Pay', 19, -1, -1, -1,3);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (41, -1, -1);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 41, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 840;
INSERT INTO System.PSP_Tbl (id, name, system_type) VALUES (44, 'Google Pay',3);
INSERT INTO System.PSPCurrency_Tbl (pspid, name,currencyid) VALUES (44,'USD',840);

-- Enable Google Pay Wallet for Google Pay PSP
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (44, 41);

---- CMP-2836
UPDATE system.cardprefix_tbl SET enabled=false where cardid=3 and min = 60110000 and max = 60110999;
UPDATE system.cardprefix_tbl SET enabled=false where cardid=3 and min = 60112000 and max = 60114999;
UPDATE system.cardprefix_tbl SET enabled=false where cardid=3 and min = 60117400 and max = 60117499;
UPDATE system.cardprefix_tbl SET enabled=false where cardid=3 and min = 60117700 and max = 60117999;
UPDATE system.cardprefix_tbl SET enabled=false where cardid=3 and min = 60118600 and max = 60119999;
UPDATE system.cardprefix_tbl SET enabled=false where cardid=3 and min = 64400000 and max = 65999999;

---- Global Configuration for Citcon - WeChat Pay -----
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (39, 'WeChat Pay', 23, -1, -1, -1,6);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (39, 0, 0);
INSERT INTO system.cardpricing_tbl ( pricepointid, cardid) VALUES ( -840, 39);
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (41, 'Citcon',5);
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (840,41,'USD');
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (39, 41);