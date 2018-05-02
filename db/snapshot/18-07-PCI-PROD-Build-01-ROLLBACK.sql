DROP INDEX client.cardaccess_card_country_uq RESTRICT;
CREATE UNIQUE INDEX cardaccess_card_country_uq ON client.cardaccess_tbl (clientid, cardid, countryid);

ALTER TABLE log.transaction_tbl DROP CONSTRAINT transaction_tbl_producttype_tbl_id_fk;
ALTER TABLE log.transaction_tbl DROP COLUMN producttype;