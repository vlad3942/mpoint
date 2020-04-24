/* ========== Global Configuration for DragonPay Online = STARTS ========== */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (61, 'DragonPay',7);

/* ==========  Global Configuration for DragonPay Online = STARTS ========== */


INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (608,61,'PHP');

/*
* Dragon pay cad with Dragon Pay aggregator 
*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (47, 61);


INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (<clientid>, 61, 'DragonPay', <DragonPay_merchatid>, <DragonPay_MerchatAuthKey>);
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10018, 61, 'DragonPay', 'CPM', '3GJ8LubyWVUMgqY');

INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (<ClientID>, <countryid>, <CurrencyID>,true)
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10018,640,608,true)


INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<accountid>, 61, '-1');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100094, 61, '-1');


INSERT INTO Client.CardAccess_Tbl(clientid,cardid,enabled,pspid,countryid,stateid,psp_type) values (<clientid>,47,true,61,<countryid>,1,7);
-- Route DragonPay Card to DragonPayAggregator with country Japan
INSERT INTO Client.CardAccess_Tbl(clientid,cardid,enabled,pspid,countryid,stateid,psp_type) values (10018,47,true,61,640,1,7);



/* ========== Global Configuration for DragonPay = ENDS ========== */
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope ) VALUES (<KeyName>, <Value>, <ClientID>, 'client', 2 );



/* ========== Alter address field size  ========== */
ALTER TABLE enduser.address_tbl ALTER COLUMN street TYPE character varying(100)


-- UATP batch cut-off-time for CMP-3527 --
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES ('BATCH-CUT-OFF-TIME', '02:00', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = <ClientID> and pspid = 50), 'merchant',1);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES ('TICKET-START-RANGE', '526016', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = <ClientID> and pspid = 50), 'merchant',1);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES ('TICKET-END-RANGE', '526019', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = <ClientID> and pspid = 50), 'merchant',1);


-- FileExpiry for UATP and Chase
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES ('FILE_EXPIRY', '1', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10069 and pspid = 50), 'merchant',1);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES ('FILE_EXPIRY', '4', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10069 and pspid = 52), 'merchant',1);



---DCC--
INSERT INTO system.externalreferencetype_tbl (id, "name") VALUES(0, 'System');
INSERT INTO system.externalreferencetype_tbl (id, "name") VALUES(50, 'UATP');
INSERT INTO system.externalreferencetype_tbl (id, "name") VALUES(1, 'CellPoint Foreign Exchange');

INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(1980, 'Foreign Exchange  Ack Accepted', 'Callback', 'send');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(1981, 'Foreign Exchange  Ack Constructed', 'Callback', 'send');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(1983, 'Foreign Exchange  Ack Connection Failed', 'Callback', 'send');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(1984, 'Foreign Exchange  Ack Transmission Failed', 'Callback', 'send');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(1985, 'Foreign Exchange  Ack Rejected', 'Callback', 'send');
--DCC--ALTER TABLE CLIENT.SUREPAY_TBL ADD MAX INT4 DEFAULT 1;

-- Fraud Integration --

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



/* ========== CONFIGURE Cyber Fraud GateWay START========== */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (62, 'CyberSource Fraud Gateway',9);
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (<cardid>, 62); /*With Apple-Pay*/

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (<clientid>, 62, 'Cyber Source', 'cellpoint_mobile', 'i+85bPV1v3AVY6MMwNq98EvWOxmfyLxYtkaENHS+b3zAc5RRCCzYGKNKw0w76m87hfT6dAtMPSr+LS4wyZVlgZEH4FiqzdVZ5FP00saqTGitlzhidR1Il1nSkmK1Yqht0xKTuFRYNhzTDwSt7TLfmFzom6xWmS4YHjT4kp1yOCe2h2xYszSKPPrrGKjpD2GWzhNEVj3UcmglJnQwa4pbVi4Omn2q6tTFNbqqkdxRRVeMbk7tnSTMkW5iTReq4VDpUa4gXjxUZST3GqzfVNwPfe1C7I78POYb6FeaEL4xKGKyag01chtNBKEHLs9Jx8/TZmb947/w6/5MmsfNuDji8w==');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<accountid>>, 62, '-1');

INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (1, 62, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (5, 62, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (7, 62, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (8, 62, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (22, 62, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (0, 62, true);

INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (840,62,'USD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (124,62,'CAD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (36,56,'AUD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (344,62,'HKD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (392,62,'JPY');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (710,62,'ZAR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (826,62,'GBP');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (978,62,'EUR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (554,62,'NZD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (752,62,'SEK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (901,62,'TWD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (643,62,'RUB');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (356,62,'INR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (360,62,'IDR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (608,62,'PHP');

insert into system.cardpricing_tbl (pricepointid ,cardid ) select pricepointid,0 from system.cardpricing_tbl where cardid = 8
 ON conflict ON CONSTRAINT cardpricing_uq DO NOTHING;
 UPDATE SYSTEM.CARD_TBL set enabled=true where id=0;





---Wallet based routing  CPM-3484
-- Note - Required to create extra routes for wallet and stored card
INSERT INTO client.cardaccess_tbl ( clientid, cardid, pspid, countryid, , psp_type, installment, capture_method, capture_type, walletid, dccenabled)
VALUES( <clientid>, <cardid>,  <pspid>, <countryid>, 1, 0, 0, 1, <walletid>, false);