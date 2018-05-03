
--PPro PSPCard

INSERT INTO system.pspcard_tbl (pspid, cardid, enabled) VALUES (46, 39, true);
INSERT INTO system.pspcard_tbl (pspid, cardid, enabled) VALUES (46, 34, true);

--PPro PSPCurrency

INSERT INTO system.pspcurrency_tbl (pspid, currencyid, name, enabled) VALUES (46, 840, 'USD', true);
INSERT INTO system.pspcurrency_tbl (pspid, currencyid, name, enabled) VALUES (46, 458, 'MYR', true);
INSERT INTO system.pspcurrency_tbl (pspid, currencyid, name, enabled) VALUES (46, 978, 'EUR', true);
INSERT INTO system.pspcurrency_tbl (pspid, currencyid, name, enabled) VALUES (46, 608, 'PHP', true);
INSERT INTO system.pspcurrency_tbl (pspid, currencyid, name, enabled) VALUES (46, 702, 'SGD', true);
INSERT INTO system.pspcurrency_tbl (pspid, currencyid, name, enabled) VALUES (46, 752, 'SEK', true);
INSERT INTO system.pspcurrency_tbl (pspid, currencyid, name, enabled) VALUES (46, 764, 'THB', true);
INSERT INTO system.pspcurrency_tbl (pspid, currencyid, name, enabled) VALUES (46, 985, 'PLN', true);
INSERT INTO system.pspcurrency_tbl (pspid, currencyid, name, enabled) VALUES (46, 203, 'CZK', true);
INSERT INTO system.pspcurrency_tbl (pspid, currencyid, name, enabled) VALUES (46, 36, 'AUD', true);

-- PPro SR

INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, psp_type, stateid) VALUES (10047, 39, true, 46, 609, 1, 4);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, psp_type, stateid) VALUES (10047, 34, true, 46, 500, 1, 4);
