-- Adding Virtual Token for Saving SUVTP in mPoint schema
ALTER TABLE Log.Transaction_Tbl ADD COLUMN virtualtoken character varying(512);

ALTER TABLE log.settlement_tbl ALTER COLUMN status TYPE varchar(100) USING status::varchar(100);

DROP INDEX client.cardaccess_card_country_uq RESTRICT;
CREATE UNIQUE INDEX cardaccess_card_country_uq ON client.cardaccess_tbl (clientid, cardid, countryid, psp_type) WHERE enabled='true';