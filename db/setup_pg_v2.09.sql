--Setup this additional property if 3DS is to be requested with every request to Adyen, the rules configured in Adyen will override
--Do not add this if 3DS is not required
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'MANUALTHREED', 'true', id, 'merchant' from client.merchantaccount_tbl where clientid=<clientid> and pspid=12;

--Setup this additional property if 3DS is to be requested to Adyen based on dynamic rules configured.
--Do not add this if 3DS is not required
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'DYNAMICTHREED', 'true', id, 'merchant' from client.merchantaccount_tbl where clientid=<clientid> and pspid=12;

--enable Offline Installment option for a PSP - Adyen
UPDATE system.psp_tbl SET installment = 1 WHERE id = 12;

--  CMP-2810 Add Paypal STC related credentials to additional properties table linked to merchant config --
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type )
    SELECT 'PAYPAL_STC', 'true', id, 'merchant' FROM client.merchantaccount_tbl WHERE pspid=24;
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type )
    SELECT 'PAYPAL_REST_ACC_ID', '897383MMQSC9W', id, 'merchant' FROM client.merchantaccount_tbl WHERE pspid=24;
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type )
    SELECT 'PAYPAL_REST_CLIENT_ID', 'AejFqzw9vADty0xlc9oAgI0Rz0LQXYaoZyGPo0rlNiMx7taGI5C1VxqrGpT9zVjg1LMiPwfzkftO0W3U', id, 'merchant' FROM client.merchantaccount_tbl WHERE pspid=24;
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type )
    SELECT 'PAYPAL_REST_SECRET', 'EEmWU-1Bcmfuhe0xheaAlrArpEx2uzrBcB-HVkm125max3hgtVJc4d26bWe0TuDmks-kOl7WlqoRn4-G', id, 'merchant' FROM client.merchantaccount_tbl WHERE pspid=24;
-- CMP-2810 --



/* ========== Consolidated script for CONFIGURING GOOGLE PAY - START ========== */
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (41, 'Google Pay', 19, -1, -1, -1,3);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (41, -1, -1);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 41, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 840;


INSERT INTO System.PSP_Tbl (id, name, system_type) VALUES (44, 'Google Pay',3);
INSERT INTO System.PSPCurrency_Tbl (pspid, name,currencyid) VALUES (44,'USD',840);

-- Enable Google Pay Wallet for Google Pay PSP
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (44, 41);

--Note: public need not be added to merchant account tbl.
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (<clientid>, 44, '<merchantid provided by google to merchant>', NULL, NULL);
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<account id>, 44, 'Google Pay');

--static route
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid, psp_type) VALUES (<clientid>, 41, <pspid>, <countryid>,1);
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (<pspid>, 41);

--Enable WireCard for GPay and USD - Sample
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid, psp_type) VALUES (<clientid>, 41, 18,200,1);
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (18, 41);

/* ========== CONFIGURATION FOR GOOGLE PAY - END ========== */

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