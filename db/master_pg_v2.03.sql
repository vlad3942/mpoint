-- Adding Virtual Token for Saving SUVTP in mPoint schema
ALTER TABLE Log.Transaction_Tbl ADD COLUMN virtualtoken character varying(512);

ALTER TABLE log.settlement_tbl ALTER COLUMN status TYPE varchar(100) USING status::varchar(100);

ALTER TYPE LOG.ADDITIONAL_DATA_REF ADD VALUE 'Transaction';
DROP INDEX client.cardaccess_card_country_uq RESTRICT;
CREATE UNIQUE INDEX cardaccess_card_country_uq ON client.cardaccess_tbl (clientid, cardid, countryid, psp_type) WHERE enabled='true';

-- Drop orderId unique constraint --
ALTER TABLE log.session_tbl DROP CONSTRAINT constraint_name;

-- country calling code
ALTER TABLE system.country_tbl ADD country_calling_code INTEGER NULL;

-- URL where the customer may be redirected if txn fails.
ALTER TABLE Log.Transaction_Tbl ADD declineurl VARCHAR(255);
ALTER TABLE Log.Transaction_Tbl ADD declineurl VARCHAR(255);

alter table log.additional_data_tbl alter column value type varchar(50) using value::varchar(50);

