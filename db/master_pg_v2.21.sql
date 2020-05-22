-- CMP-3484 Wallet Based Routing --
ALTER TABLE client.cardaccess_tbl ADD walletid int4;
drop index cardaccess_card_country_uq;
CREATE UNIQUE INDEX cardaccess_card_country_uq ON client.cardaccess_tbl USING btree (clientid, cardid, pspid, countryid, psp_type,walletid) WHERE (enabled = true);


--DCC---
CREATE TABLE system.externalreferencetype_tbl (
	id serial NOT NULL,
	"name" text NOT NULL,
	created timestamp NULL DEFAULT now(),
	modified timestamp NULL DEFAULT now(),
	enabled bool NULL DEFAULT true,
	CONSTRAINT externalreferencetype_pk PRIMARY KEY (id)
);
ALTER TABLE system.externalreferencetype_tbl OWNER TO mpoint;

ALTER TABLE log.externalreference_tbl ADD type int4 CONSTRAINT externalreferencetype_fk REFERENCES system.externalreferencetype_tbl(id);
ALTER TABLE log.transaction_tbl ADD convetredcurrencyid int4 NULL CONSTRAINT convertedcurrency_fk REFERENCES system.currency_tbl(id);
ALTER TABLE log.transaction_tbl ADD convertedamount int8 NULL;
ALTER TABLE log.transaction_tbl ADD conversionrate decimal DEFAULT 1;
ALTER TABLE client.cardaccess_tbl ADD dccenabled bool NULL DEFAULT false;
---DCC---
DROP TABLE IF EXISTS CLIENT.RETRIAL_TBL;

DROP TABLE IF EXISTS SYSTEM.RETRIALTYPE_TBL;

--pspcurrency UNIQUE CONSTRAINT
CREATE UNIQUE INDEX pspcurrency_psp_currency_uq ON system.pspcurrency_tbl USING btree (pspid, currencyid) WHERE (enabled = true);



-- passenger tbl --
ALTER TABLE log.passenger_tbl alter column first_name type varchar(50);
ALTER TABLE log.passenger_tbl alter column last_name type varchar(50);

-- currency improvement --
ALTER TABLE system.currency_tbl ADD COLUMN symbol VARCHAR(5);
ALTER TABLE system.country_tbl DROP COLUMN symbol;
----Increase length of additional_data_tbl's name name
ALTER TABLE log.additional_data_tbl ALTER COLUMN name TYPE varchar(30);

--Support All currency for all or one country
ALTER TABLE client.countrycurrency_tbl ALTER COLUMN countryid DROP NOT NULL;
ALTER TABLE client.countrycurrency_tbl ALTER COLUMN currencyid DROP NOT NULL;
