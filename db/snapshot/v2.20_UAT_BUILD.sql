--Aquirer level auto-capture--
create table system.capturetype_tbl
(
    id serial not null
        constraint capturetype_pk
            primary key,
    name varchar(50),
    created timestamp default now(),
    modified timestamp default now(),
    enabled boolean default true
);

alter table system.capturetype_tbl owner to postgres;

INSERT INTO system.capturetype_tbl (id, name, enabled) VALUES (1, 'Manual Capture', true);
INSERT INTO system.capturetype_tbl (id, name, enabled) VALUES (2, 'PSP Level Auto Capture ', true);
INSERT INTO system.capturetype_tbl (id, name, enabled) VALUES (3, 'Merchant Level Auto Capture', true);
INSERT INTO system.capturetype_tbl (id, name, enabled) VALUES (4, 'Batch Capture', true);

ALTER TABLE client.cardaccess_tbl
    ADD COLUMN capture_type int2
        CONSTRAINT cardaccess2capturetype_fk
            REFERENCES system.capturetype_tbl DEFAULT (1) ;

ALTER TABLE log.transaction_tbl ALTER COLUMN auto_capture DROP DEFAULT;
ALTER TABLE log.transaction_tbl ALTER COLUMN auto_capture TYPE int2 USING CASE WHEN auto_capture=TRUE THEN 3 ELSE 1 END;
ALTER TABLE log.transaction_tbl ALTER COLUMN auto_capture SET DEFAULT 1;

ALTER TABLE client.client_tbl DROP COLUMN auto_capture;
---End---

/* ========== batch-size for the chase connector:: CMP-3457 ========== */

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES ('MVAULT_BATCH_SIZE', '1', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = <client id> and pspid =  <pspid>), 'merchant',1);

 ALTER TABLE Log.Transaction_Tbl ALTER COLUMN attempt SET DEFAULT 1;


CREATE TABLE CLIENT.STATICROUTELEVELCONFIGURATION
(
    ID           SERIAL
        CONSTRAINT STATICROUTELEVELCONFIGURATION_PK
            PRIMARY KEY,
    CARDACCESSID INT                     NOT NULL,
    CVCMANDATORY BOOLEAN   DEFAULT TRUE  NOT NULL,
    ENABLED      BOOLEAN   DEFAULT TRUE  NOT NULL,
    CREATED      TIMESTAMP DEFAULT now() NOT NULL,
    MODIFIED     TIMESTAMP DEFAULT now() NOT NULL
);

COMMENT ON TABLE CLIENT.STATICROUTELEVELCONFIGURATION IS 'This table will contain the configuration based on '
    'card schema, Provider and Country';

COMMENT ON COLUMN CLIENT.STATICROUTELEVELCONFIGURATION.CARDACCESSID IS 'Primary key of client.cardaccess_tbl';

create unique index staticroutelevelconfiguration_cardaccessid_uindex
	on client.staticroutelevelconfiguration (cardaccessid);

ALTER TABLE client.StaticRouteLevelConfiguration OWNER TO mpoint;

CREATE TRIGGER UPDATE_STATICROUTELEVELCONFIGURATION
    BEFORE UPDATE
    ON CLIENT.STATICROUTELEVELCONFIGURATION
    FOR EACH ROW
EXECUTE PROCEDURE PUBLIC.Update_Table_Proc();



/* PASSBOOK IMPROVEMENT - START*/

alter table log.txnpassbook_tbl
	add clientid int;

alter table log.txnpassbook_tbl
	add constraint txnpassbook_tbl_client_tbl_id_fk
		foreign key (clientid) references client.client_tbl;

/* Run migrate script before adding not null constraint */
alter table log.txnpassbook_tbl alter column clientid set not null;

/* PASSBOOK IMPROVEMENT - END */


/* PASSBOOK IMPROVEMENT - START*/

/* If required add range check to avoid high peak in RDS CPU */
UPDATE LOG.TXNPASSBOOK_TBL PASSBOOK
SET CLIENTID = TRANSACTION.CLIENTID
FROM LOG.TRANSACTION_TBL TRANSACTION
WHERE PASSBOOK.TRANSACTIONID = TRANSACTION.ID;

/* PASSBOOK IMPROVEMENT - END */
/* ========== batch-size for the chase connector:: CMP-3457 ========== */


INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES ('MVAULT_BATCH_SIZE', '1', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = <client id> and pspid =  <pspid>), 'merchant',1);


INSERT INTO CLIENT.STATICROUTELEVELCONFIGURATION (CARDACCESSID) SELECT id FROM CLIENT.CARDACCESS_TBL WHERE PSP_TYPE = 1;






