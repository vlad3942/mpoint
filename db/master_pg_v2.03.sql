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

--== CONFIGURE Chase Payment Acquirer ==--

-- PSP tbl config --
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (52, 'Chase Payment',2);
-- END PSP tbl config --
-- Currencys for Chase Payment --
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (840,52,'USD');
-- END Currencys for Chase Payment --

INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (1, 52, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (5, 52, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (7, 52, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (8, 52, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (22, 52, true);

INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, stateid, enabled) VALUES (10007, 1, 52, 200, 1, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, stateid, enabled) VALUES (10007, 5, 52, 200, 1, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, stateid, enabled) VALUES (10007, 7, 52, 200, 1, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, stateid, enabled) VALUES (10007, 8, 52, 200, 1, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, stateid, enabled) VALUES (10007, 22, 52, 200, 1, true);

-- Merchant MID configuration --
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 52, 'nconline1', '', '');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 52, '-1');
-- End Merchant MID configuration --

--== END CONFIGURE Chase Payment Acquirer ==--