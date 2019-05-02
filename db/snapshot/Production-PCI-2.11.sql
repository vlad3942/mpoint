
--  consolidate Cielo sqls -- execute if Cielo config is not present in the env.
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (54, 'Cielo',1);
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (54, 1);	-- American Express
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (54, 2);	-- Dankort
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (54, 3);	-- Diners Club
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (54, 4);	-- EuroCard
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (54, 5);	-- JCB
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (54, 6);	-- Maestro
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (54, 7);	-- MasterCard
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (54, 8);	-- VISA
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (54, 9);	-- VISA Electron
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (54, 15);	-- Apple Pay
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (54, 21);	-- UATP

--Add currency support as required for client
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (986,54,'BRL');


/* ========== UATP ================================= */
ALTER TABLE Log.ExternalReference_Tbl OWNER TO mpoint;

/* ========== Time Zone property for all live customers ========== */
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('TIMEZONE', 'Asia/Kuala_Lumpur', true, 10018, 'client', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('TIMEZONE', 'Africa/Addis_Ababa', true, 10067, 'client', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('TIMEZONE', 'Canada/Eastern', true, 10065, 'client', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('TIMEZONE', 'EST5EDT', true, 10061, 'client', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('TIMEZONE', 'Europe/London', true, 10060, 'client', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('TIMEZONE', 'Africa/Addis_Ababa', true, 10022, 'client', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('TIMEZONE', 'America/Aruba', true, 10021, 'client', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('TIMEZONE', 'Asia/Kuala_Lumpur', true, 10020, 'client', 2);





