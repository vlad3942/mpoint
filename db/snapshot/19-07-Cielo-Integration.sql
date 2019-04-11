--  consolidate Cielo sqls -- execute if Cielo config is not present in the env.
/* ========== CONFIGURE Cielo START ========== */
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

-- Merchant MID configuration --
--Sandbox env details.
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (<clientid>, 54, 'Ceilo', '03a2a081-0243-4d69-a649-266bb20eb204', 'PYBRZYLNZEMFJAXRUFYRDGWJXDWRCQTJSRTYUEZI');

--production sql
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (<clientid>, 54, <name>, <mid username>, <pwd>);

INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<accountid>, 54, '-1');

--Sample sandbox sql
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10007, 8, true, 54, 403, 1, null, false, 1);

/* ========== CONFIGURE Cielo END ========== */
