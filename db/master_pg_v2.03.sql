-- Adding Virtual Token for Saving SUVTP in mPoint schema
ALTER TABLE Log.Transaction_Tbl ADD COLUMN virtualtoken character varying(512);

ALTER TABLE log.settlement_tbl ALTER COLUMN status TYPE varchar(100) USING status::varchar(100);

ALTER TYPE LOG.ADDITIONAL_DATA_REF ADD VALUE 'Transaction';