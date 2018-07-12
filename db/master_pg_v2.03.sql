-- Adding Virtual Token for Saving SUVTP in mPoint schema
ALTER TABLE Log.Transaction_Tbl ADD COLUMN virtualtoken character varying(512);