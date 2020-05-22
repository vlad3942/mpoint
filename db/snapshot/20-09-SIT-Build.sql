-- HPP get transaction status polling timeout flag --
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('getTxnStatusPollingTimeOut', '60', true, <clientid>, 'client', 2);

	/* ========== CONFIGURE RMFSS START========== */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (65, 'CEBU-RMFSS',9);
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (<cardid>, 65);

INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, enabled, username, passwd) VALUES (<clientid>, 65, 'CEBU-RMFSS', true, 'By9AjPV6j14jgb3DXRIpW0mInOfMEafS', 'E9NBawrSH6UAtw1v');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<accountid>, 65, '-1');

INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (840,65,'USD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (608,65,'PHP');


/*=================== Create a new static route for Fraud check : START =======================*/
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, psp_type, stateid) VALUES (<clientid>, <cardid>, true, 65, 200, 9, 1);
/*=================== Create a new static route for Fraud check : END =======================*/


----Increase length of additional_data_tbl's name name
ALTER TABLE log.additional_data_tbl ALTER COLUMN name TYPE varchar(30);


----Fraud Integration
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3010, 'Pre Fraud Check Initiated', 'Fraud', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3011, 'Pre-screening Result - Accepted', 'Fraud', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3012, 'Pre-screening Fraud Service Unavailable', 'Fraud', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3013, 'Pre-screening Result - Unknown', 'Fraud', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3014, 'Pre-screening Result - Review', 'Fraud', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3015, 'Pre-screening Result - Rejected', 'Fraud', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3016, 'Pre-screening Connection Failed - Rejected', 'Fraud', '');

INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3110, 'Post Fraud Check Initiated', 'Fraud', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3111, 'Post-screening Result - Accepted', 'Fraud', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3112, 'Post-screening Fraud Service Unavailable', 'Fraud', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3113, 'Post-screening Result - Unknown', 'Fraud', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3114, 'Post-screening Result - Review', 'Fraud', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3115, 'Post-screening Result - Rejected', 'Fraud', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3116, 'Post-screening Connection Failed', 'Fraud', '');

INSERT INTO "system".processortype_tbl (id, "name") VALUES(10, 'Post Auth Fraud Gateway');

INSERT INTO client.additionalproperty_tbl (key, value, externalid, "type","scope" ) VALUES('ISROLLBACK_ON_FRAUD_FAIL', 'true', <ClientID>, 'client', 0);
----Fraud Integration  END


/* ========== CONFIGURE Cyber Fraud GateWay START========== */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (64, 'CyberSource Fraud Gateway',9);
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (<cardid>, 64);

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (<clientid>, 64, 'Cyber Source FSP', 'cellpoint_mobile', 'i+85bPV1v3AVY6MMwNq98EvWOxmfyLxYtkaENHS+b3zAc5RRCCzYGKNKw0w76m87hfT6dAtMPSr+LS4wyZVlgZEH4FiqzdVZ5FP00saqTGitlzhidR1Il1nSkmK1Yqht0xKTuFRYNhzTDwSt7TLfmFzom6xWmS4YHjT4kp1yOCe2h2xYszSKPPrrGKjpD2GWzhNEVj3UcmglJnQwa4pbVi4Omn2q6tTFNbqqkdxRRVeMbk7tnSTMkW5iTReq4VDpUa4gXjxUZST3GqzfVNwPfe1C7I78POYb6FeaEL4xKGKyag01chtNBKEHLs9Jx8/TZmb947/w6/5MmsfNuDji8w==');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<accountid>>, 64, '-1');

INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (1, 64, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (5, 64, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (7, 64, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (8, 64, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (22, 64, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (0, 64, true);

INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (840,64,'USD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (124,64,'CAD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (36,64,'AUD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (344,64,'HKD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (392,64,'JPY');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (710,64,'ZAR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (826,64,'GBP');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (978,64,'EUR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (554,64,'NZD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (752,64,'SEK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (901,64,'TWD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (643,64,'RUB');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (356,64,'INR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (360,64,'IDR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (608,64,'PHP');
/* ========== END CONFIGURE Cyber Fraud GateWay START========== */

