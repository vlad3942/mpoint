--Setup this additional property if 3DS is to be requested with every request to Adyen, the rules configured in Adyen will override
--Do not add this if 3DS is not required
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'MANUALTHREED', 'true', id, 'merchant' from client.merchantaccount_tbl where clientid=<clientid> and pspid=12;

--Setup this additional property if 3DS is to be requested to Adyen based on dynamic rules configured.
--Do not add this if 3DS is not required
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'DYNAMICTHREED', 'true', id, 'merchant' from client.merchantaccount_tbl where clientid=<clientid> and pspid=12;

--  consolidate Adyen sqls -- execute if Adyen config is not present in the env.
/* ========== CONFIGURE ADYEN START ========== */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (12, 'Adyen',1);
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (12, 1);	-- American Express
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (12, 2);	-- Dankort
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (12, 3);	-- Diners Club
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (12, 4);	-- EuroCard
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (12, 5);	-- JCB
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (12, 6);	-- Maestro
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (12, 7);	-- MasterCard
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (12, 8);	-- VISA
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (12, 9);	-- VISA Electron
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (12, 15);	-- Apple Pay
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (12, 21);	-- UATP
--Add currency support as required for client
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (840,12,'USD');

-- Merchant MID configuration --
--Sandbox env details.
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (<clientid>, 12, 'CellpointDev', 'ws_200045@Company.CellpointMobile', 'jepf9sawnFpbCPMDEMODEMO');

--production sql
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (<clientid>, 12, <name>, <mid username>, <pwd>);

INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<accountid>, 12, '-1');

--enable Offline Installment option for a PSP - Adyen if applicable for the client and route.
--Sample sandbox sql
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment) VALUES (10007, 8, true, 12, 403, 1, null, false, 1,1);
--or --Sample sandbox sql if SR is present, then update
UPDATE client.cardaccess_tbl SET installment = 1 WHERE clientid = 10007 and pspid=12 and cardid = 8 and countryid=403 and enabled=true;

/* ========== CONFIGURE ADYEN END ========== */
