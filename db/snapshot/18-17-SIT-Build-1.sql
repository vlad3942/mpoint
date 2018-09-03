DROP INDEX client.cardaccess_card_country_uq RESTRICT;
CREATE UNIQUE INDEX cardaccess_card_country_uq ON client.cardaccess_tbl (clientid, cardid, pspid, countryid, psp_type) WHERE enabled='true';
