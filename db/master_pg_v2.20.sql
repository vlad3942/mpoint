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