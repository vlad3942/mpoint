/*=================== Web session timeout in minutes : START =======================*/
UPDATE client.additionalproperty_tbl SET value = 10 WHERE key ='webSessionTimeout' AND externalid = 10007
/*=================== Web session timeout in minutes : END =======================*/

DROP INDEX client.cardaccess_card_country_uq RESTRICT;

CREATE UNIQUE INDEX cardaccess_card_country_uq ON client.cardaccess_tbl (clientid, cardid, countryid, psp_type) WHERE enabled='true';


