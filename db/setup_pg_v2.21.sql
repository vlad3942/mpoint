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


---Wallet based routing  CPM-3484
-- Note - Required to create extra routes for wallet and stored card
INSERT INTO client.cardaccess_tbl ( clientid, cardid, pspid, countryid, , psp_type, installment, capture_method, capture_type, walletid, dccenabled)
VALUES( <clientid>, <cardid>,  <pspid>, <countryid>, 1, 0, 0, 1, <walletid>, false);

----CYBS (PSP) start ---
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (63, 'CyberSource',1);
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (63, 1);	-- American Express
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (63, 3);	-- Diners Club
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (63, 7);	-- MasterCard
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (63, 8);	-- VISA
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (63, 5);	-- JCB
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (63, 6);	-- Maestro
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (63, 15);	-- Apple Pay
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (63, 23);	-- MasterPass
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (63, 16);	-- VCO
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (63, 41);	-- Google Pay


--Add currency support as required for client
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (208,63,'DKK');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (156,63,'CNY');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (124,63,'CAD');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (36,63,'AUD');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (446,63,'MOP');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (414,63,'KWD');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (410,63,'KRW');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (392,63,'JPY');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (360,63,'IDR');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (356,63,'INR');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (344,63,'HKD');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (458,63,'MYR');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (554,63,'NZD');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (598,63,'PGK');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (608,63,'PHP');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (840,63,'USD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (634,63,'QAR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (682,63,'SAR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (702,63,'SGD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (764,63,'THB');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (784,63,'AED');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (826,63,'GBP');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (901,63,'TWD');


-- Merchant MID configuration --
--Sandbox env details. default merchant config
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10077, 63, 'CyberSource', 'cellpoint_mobile', 'i+85bPV1v3AVY6MMwNq98EvWOxmfyLxYtkaENHS+b3zAc5RRCCzYGKNKw0w76m87hfT6dAtMPSr+LS4wyZVlgZEH4FiqzdVZ5FP00saqTGitlzhidR1Il1nSkmK1Yqht0xKTuFRYNhzTDwSt7TLfmFzom6xWmS4YHjT4kp1yOCe2h2xYszSKPPrrGKjpD2GWzhNEVj3UcmglJnQwa4pbVi4Omn2q6tTFNbqqkdxRRVeMbk7tnSTMkW5iTReq4VDpUa4gXjxUZST3GqzfVNwPfe1C7I78POYb6FeaEL4xKGKyag01chtNBKEHLs9Jx8/TZmb947/w6/5MmsfNuDji8w==');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100770, 63, '-1');
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10077, 1, true, 63, <countryid>, 1, null, false, 1);

--Sandbox env details. Google Pay and Apple Pay MID config
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.merchantaccountrule', 'merchantaccount ::= (property[@name=''<mid>''])
 mid ::= "GlobalPayment.Wallet.MID."(transaction/@wallet-id)', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=<pspid>;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.MID.44', 'PAL-IPG GOOGLEPAY', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=44;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.MID.14', 'PAL-IPG APPLEPAY', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=14;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.usernamerule', 'username ::= (property[@name=''<uname>''])
 uname ::= "GlobalPayment.Wallet.USERNAME."(transaction/@wallet-id)', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=<pspid>;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.44', 'gpmnl042772772760', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=44;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.14', 'gpmnl042772772761', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=14;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.pwdrule', 'password ::= (property[@name=''<passwd>''])
 passwd ::= "GlobalPayment.Wallet.PASSWORD."(transaction/@wallet-id)', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=<pspid>;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.44', 'a37Va81z32Q+qMNhRKG/2l9J1yvw1tYvw+atWXuW3jPHOFKAwcvGY9dPfuyDiST4ruhe9gDAg36lm+zdeiSUnmd3kN99Io/88daPtzVtnyzqEpT/QzgDoI8Y6rbD7rnINzVLEBWAB7xdWfw5NVRFMQrFuvOUE3VqyM3F8uQdEvCBFoPCxxIbsEo3Dn6hjevgzO7nzZPdrmqDrMO8CE9CUqleJhRlC6MfJNA4M87ZtYt03q0XpAXM/zYd8tYJJu29UelxadoeZxBdQzuoU89XXcZtYs8rUJXd5GRqtEuLXMS0tgNrnf2en7+Xe+Zl3tNSOwoJSkwdFj07yjnPF8nvdg==', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=44;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.14', 'AFYDa+MdhNIANeSrpQORmKhqpOMAnMNF8xadipFE1LhLhfngyjowKj71fvDjoaBwUQZgMTN4GTofztmHbYkWNzuD/y8Qz/9QjSKs63P+o+G67fbYFwk8Q+Hcm5vS69TCi5wygsFiCLvoQsH3R0PTRdz2xoFvbFvJpjBU6gviJICMZUYQv9ZYqtVO7uEI/Ue5DSV3QIh4gSg/Rl6Yz9KfXcsm04j0D21IIu/hjxnnP+zeAhBUjxTOu1SggM9UAF+ryeOXXJc1HTDVFblotsZRFTn1mmmDrUKcgxcv1+JfVc3kqfyv0WFExHbKX9fmWZYpvJeQ8ie/3ePWSU+EZKdVhA==', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=14;


-- wallet based routing for Apple pay and GPay via CYBS
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment,walletid) VALUES (10077, 8, true, 63, <countryid>, 1, null, false, 1,0,44);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment,walletid) VALUES (10077, 7, true, 63, <countryid>, 1, null, false, 1,0,44);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment,walletid) VALUES (10077, 8, true, 63, <countryid>, 1, null, false, 1,0,14);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment,walletid) VALUES (10077, 8, true, 63, <countryid>, 1, null, false, 1,0,14);

--Setup CARD on file only option for Global payments client
insert into client.additionalproperty_tbl (key,value,enabled,externalid,type,scope)
select 'ALLOWEDPAYMENTMETHODS','CARD',true, id, 'merchant', 1 from client.merchantaccount_tbl WHERE clientid=10077 AND pspid=44;

--production sql
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (<clientid>, 63, <merchant name>, <username-mid>, <pwd>);

INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<accountid>, 63, '-1');

--edit if installment is to be enabled for specific SR, 0 means no installment option. 1 means installment is enabled.
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment) VALUES (<clientid>, <cardid>, true, 63, <countryid>, 1, null, false, 1,0);

--Setup CARD on file only option for Global payments client - Google Pay
insert into client.additionalproperty_tbl (key,value,enabled,externalid,type,scope)
select 'ALLOWEDPAYMENTMETHODS','CARD',true, id, 'merchant', 1 from client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=44;


INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.merchantaccountrule', 'merchantaccount ::= (property[@name=''<mid>''])
 mid ::= "GlobalPayment.Wallet.MID."(transaction/@wallet-id)', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=44;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.merchantaccountrule', 'merchantaccount ::= (property[@name=''<mid>''])
 mid ::= "GlobalPayment.Wallet.MID."(transaction/@wallet-id)', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=14;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.MID.44', 'PAL-IPG GOOGLEPAY', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=44;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.MID.14', 'PAL-IPG APPLEPAY', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=14;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.usernamerule', 'username ::= (property[@name=''<uname>''])
 uname ::= "GlobalPayment.Wallet.USERNAME."(transaction/@wallet-id)', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=44;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.usernamerule', 'username ::= (property[@name=''<uname>''])
 uname ::= "GlobalPayment.Wallet.USERNAME."(transaction/@wallet-id)', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=14;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.44', 'gpmnl042772772760', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=44;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.14', 'gpmnl042772772761', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=14;


INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.pwdrule', 'password ::= (property[@name=''<passwd>''])
 passwd ::= "GlobalPayment.Wallet.PASSWORD."(transaction/@wallet-id)', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=44;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.pwdrule', 'password ::= (property[@name=''<passwd>''])
 passwd ::= "GlobalPayment.Wallet.PASSWORD."(transaction/@wallet-id)', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=14;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.44', 'a37Va81z32Q+qMNhRKG/2l9J1yvw1tYvw+atWXuW3PHOFKAwcvGY9dPfuyDiST4ruhe9gDAg36lm+zdeiSUnmd3kN99Io/88daPtzVtnyzqEpT/QzgDoI8Y6rbD7rnINzVLEBWAB7xdWfw5NVRFMQrFuvOUE3VqyM3F8uQdEvCBFoPCxxIbsEo3Dn6hjevgzO7nzZPdrmqDrMO8CE9CUqleJhRlC6MfJNA4M87ZtYt03q0XpAXM/zYd8tYJJu29UelxadoeZxBdQzuoU89XXcZtYs8rUJXd5GRqtEuLXMS0tgNrnf2en7+Xe+Zl3tNSOwoJSkwdFj07yjnPF8nvdg==', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=44;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.14', 'AFYDa+MdhNIANeSrpQORmKhqpOMAnMNF8xadipFE1LhLhfngyjowKj71fvDjoaBwUQZgMTN4GTofztmHbYkWNzuD/y8Qz/9QjSKs63P+o+G67fbYFwk8Q+Hcm5vS69TCi5wygsFiCLvoQsH3R0PTRdz2xoFvbFvJpjBU6gviJICMZUYQv9ZYqtVO7uEI/Ue5DSV3QIh4gSg/Rl6Yz9KfXcsm04j0D21IIu/hjxnnP+zeAhBUjxTOu1SggM9UAF+ryeOXXJc1HTDVFblotsZRFTn1mmmDrUKcgxcv1+JfVc3kqfyv0WFExHbKX9fmWZYpvJeQ8ie/3ePWSU+EZKdVhA==', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=14;

-- google pay api version 2.0 support payment method 'PAN_ONLY','CRYPTOGRAM_3DS'. previously PAN_ONLY is CARD and CRYPTOGRAM_3DS is TOKENIZED_CARD
UPDATE client.additionalproperty_tbl SET VALUES = 'PAN_ONLY' WHERE  key = 'ALLOWEDPAYMENTMETHODS' AND VALUES = 'CARD';

---CYBS(PSP) end---

--- 2c2p ALC start --

-- PAL 2C2P-ALC MID's-- [Please change clientid and Currency as per your environment and merchant requirement]
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.<CUR>','<CUR>NMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=40;

--PAL sample queries, please add as many currencies are to be supported for PAL --

INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.THB','THBNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.PHP','PHPNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.USD','USDNMA','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=40;


-- CEBU 2C2P-ALC MID's-- [Please change clientid and Currency as per your environment and merchant requirement]
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.<CUR>','CebuPacific_<CUR>','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=40;

--CEBU Phase 1 queries--

INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.PHP','CebuPacific_PHP','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10077 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type) SELECT 'mid.USD','CebuPacific_USD','t', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10077 AND pspid=40;
--- 2c2p ALC end ---

-- currency improvement --
UPDATE system.currency_tbl AS cur SET symbol = con.symbol FROM system.country_tbl AS con WHERE cur.id = con.currencyid;