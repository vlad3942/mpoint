ALTER TABLE system.psp_tbl DROP installment;

ALTER TABLE  client.cardaccess_tbl ADD installment INT DEFAULT 0 NOT NULL;

COMMENT ON COLUMN  client.cardaccess_tbl.installment
IS
'Default 0 - No installment option
1 - Offline Installment';


INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (53, 'PayU',1);
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (53, 1);	-- American Express
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (53, 3);	-- Diners Club
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (53, 7);	-- MasterCard
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (53, 8);	-- VISA

--Add currency support as required for client
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (840,53,'USD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (986,53,'BRL');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (32,53,'ARS');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (152,53,'CLP');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (170,53,'COP');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (604,53,'PEN');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (484,53,'MXN');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (604,53,'PEN');


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