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
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type,scope) SELECT 'mid.<CUR>','<CUR>NMA','t', id, 'merchant',2 FROM client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=40;

--PAL sample queries, please add as many currencies are to be supported for PAL --

--delete any old entries if present for PAL
delete from client.additionalproperty_tbl where key like 'mid.%' and externalid = (select id from client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=40);

INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type,scope) SELECT 'mid.THB','THBNMA','t', id, 'merchant',2 FROM client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type,scope) SELECT 'mid.PHP','PHPNMA','t', id, 'merchant',2 FROM client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type,scope) SELECT 'mid.USD','USDNMA','t', id, 'merchant',2 FROM client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=40;


-- CEBU 2C2P-ALC MID's-- [Please change clientid and Currency as per your environment and merchant requirement]
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type,scope) SELECT 'mid.<CUR>','CebuPacific_<CUR>','t', id, 'merchant',2 FROM client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=40;

--CEBU Phase 1 queries--

INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type,scope) SELECT 'mid.PHP','CebuPacific_MCC','t', id, 'merchant',2 FROM client.merchantaccount_tbl WHERE clientid=10077 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type,scope) SELECT 'mid.USD','CebuPacific_USD','t', id, 'merchant',2 FROM client.merchantaccount_tbl WHERE clientid=10077 AND pspid=40;

--- 2c2p ALC end ---

-- currency improvement --
UPDATE system.currency_tbl AS cur SET symbol = con.symbol FROM system.country_tbl AS con WHERE cur.id = con.currencyid;


---- Globalpayments -- start--
--Sandbox env details. Google Pay and Apple Pay MID config
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.merchantaccountrule', 'merchantaccount ::= (property[@name=''<mid>''])
 mid ::= (transaction/authorized-amount/@currency)=="PHP"="GlobalPayment.Wallet.MID."(transaction/@wallet-id)".PHP":"GlobalPayment.Wallet.MID."(transaction/@wallet-id)', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.MID.44', 'PAL-IPG GOOGLEPAY', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.MID.44.PHP', 'PAL-IPG PHP GOOGLEPAY', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.MID.14', 'PAL-IPG APPLEPAY', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.MID.14.PHP', 'PAL-IPG PHP APPLEPAY', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.usernamerule', 'username ::= (property[@name=''<uname>''])
 uname ::= (transaction/authorized-amount/@currency)=="PHP"="GlobalPayment.Wallet.USERNAME."(transaction/@wallet-id)".PHP":"GlobalPayment.Wallet.USERNAME."(transaction/@wallet-id)"', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.44', 'gpmnl042772772760', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.44.PHP', 'gpmnl045623832732', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.14', 'gpmnl042772772761', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.14.PHP', 'gpmnl045623832731', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.pwdrule', 'password ::= (property[@name=''<passwd>''])
 passwd ::= (transaction.authorized-amount.@currency)=="PHP"=<keywithcurrency>:<keywithoutcurrency>
keywithcurrency ::= "GlobalPayment.Wallet.PASSWORD."(transaction.@wallet-id)".PHP"
keywithoutcurrency ::= "GlobalPayment.Wallet.PASSWORD."(transaction.@wallet-id)', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.44', 'a37Va81z32Q+qMNhRKG/2l9J1yvw1tYvw+atWXuW3jPHOFKAwcvGY9dPfuyDiST4ruhe9gDAg36lm+zdeiSUnmd3kN99Io/88daPtzVtnyzqEpT/QzgDoI8Y6rbD7rnINzVLEBWAB7xdWfw5NVRFMQrFuvOUE3VqyM3F8uQdEvCBFoPCxxIbsEo3Dn6hjevgzO7nzZPdrmqDrMO8CE9CUqleJhRlC6MfJNA4M87ZtYt03q0XpAXM/zYd8tYJJu29UelxadoeZxBdQzuoU89XXcZtYs8rUJXd5GRqtEuLXMS0tgNrnf2en7+Xe+Zl3tNSOwoJSkwdFj07yjnPF8nvdg==', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.44.PHP', 'xrELE5yHDUR+jMpa8pcpIccQ2zQGjZMI45piyCzWR15AL9eLgFV5xhPciSHHUQtW1NpFpwip46oV1G2Oy9SQtBjEuszTVVPF3tOQVCaBhO6J3Tfjv8VBNLY2GEUPmpFwEKW+p79eJR0iEpMqdwy/necg2O0FfmDIcQ1ZlGh5G+asjIcgeWyZYjf+8UAy4qH/94TzNf2ku93W1xtobJXaQ5IcyC9dKxoAl3m4cqVTRDj1jKKjRdcsdt6IAopm4yorRlNy3pbZpdDq7OT2Jhb3uAe1O7fUWZye1hnTd4bzZpIxV2k/L81xaMnv9wLVsG/RiML0HqfWRzwcQNi+qpqayA==', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.14', 'AFYDa+MdhNIANeSrpQORmKhqpOMAnMNF8xadipFE1LhLhfngyjowKj71fvDjoaBwUQZgMTN4GTofztmHbYkWNzuD/y8Qz/9QjSKs63P+o+G67fbYFwk8Q+Hcm5vS69TCi5wygsFiCLvoQsH3R0PTRdz2xoFvbFvJpjBU6gviJICMZUYQv9ZYqtVO7uEI/Ue5DSV3QIh4gSg/Rl6Yz9KfXcsm04j0D21IIu/hjxnnP+zeAhBUjxTOu1SggM9UAF+ryeOXXJc1HTDVFblotsZRFTn1mmmDrUKcgxcv1+JfVc3kqfyv0WFExHbKX9fmWZYpvJeQ8ie/3ePWSU+EZKdVhA==', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.14.PHP', 'ov/87C1W9ahpp4MroX/Xray0pc/T2q0fE9R6pyAdVXrlw1+I9vU++oIul9AkHMa7H6Emb3msTyB91y34ST6Tysyi4Xvu/hYbB3KoFxairs1xOpXds3siOkNACVtFIhraIPhWi1TXbbMDRKkbe1U/zokXmdxsRRVjw6SJLevPLUSGVDXnjkbjIZM5rJ4PbwFHXfr3UQ5LW/PWgksxjMh34Yco+xT+4/gKO4r5cbr6GxlBmWcGtY//GGIq+lByAhDEiJvFsFLdBg6EyqyiSt4pf74NiN0XCKbljoQZ3U507P8PWi2tjsmeBp81kgpUmsPfE9MkucCDEey71KCpGbAmKA==', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;


---------------------------Added config changes for PHP -- SIT --
-- NOTE:: when running SQL please Replace with Sandbox test key for GPay and Apple Pay MIDs
-- delete all existing rules and properties setup against merchant acc id (14 and 44)
delete from client.additionalproperty_tbl where key like 'GlobalPayment.%';

--rules
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.merchantaccountrule', 'merchantaccount ::= (property[@name=''<mid>''])
 mid ::= (transaction/authorized-amount/@currency)=="PHP"="GlobalPayment.Wallet.MID."(transaction/@wallet-id)".PHP":"GlobalPayment.Wallet.MID."(transaction/@wallet-id)', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.usernamerule', 'username ::= (property[@name=''<uname>''])
 uname ::= (transaction/authorized-amount/@currency)=="PHP"="GlobalPayment.Wallet.USERNAME."(transaction/@wallet-id)".PHP":"GlobalPayment.Wallet.USERNAME."(transaction/@wallet-id)"', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.pwdrule', 'password ::= (property[@name=''<passwd>''])
 passwd ::= (transaction.authorized-amount.@currency)=="PHP"=<keywithcurrency>:<keywithoutcurrency>
keywithcurrency ::= "GlobalPayment.Wallet.PASSWORD."(transaction.@wallet-id)".PHP"
keywithoutcurrency ::= "GlobalPayment.Wallet.PASSWORD."(transaction.@wallet-id)', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;


---Google pay -- PHP config

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.MID.44.PHP', 'PAL-IPG PHP GOOGLEPAY', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.44.PHP', 'gpmnl045623832732', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.44.PHP', 'xrELE5yHDUR+jMpa8pcpIccQ2zQGjZMI45piyCzWR15AL9eLgFV5xhPciSHHUQtW1NpFpwip46oV1G2Oy9SQtBjEuszTVVPF3tOQVCaBhO6J3Tfjv8VBNLY2GEUPmpFwEKW+p79eJR0iEpMqdwy/necg2O0FfmDIcQ1ZlGh5G+asjIcgeWyZYjf+8UAy4qH/94TzNf2ku93W1xtobJXaQ5IcyC9dKxoAl3m4cqVTRDj1jKKjRdcsdt6IAopm4yorRlNy3pbZpdDq7OT2Jhb3uAe1O7fUWZye1hnTd4bzZpIxV2k/L81xaMnv9wLVsG/RiML0HqfWRzwcQNi+qpqayA==', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;
-- Google pay -- rest of the currencies config
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.MID.44', 'PAL-IPG GOOGLEPAY', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.44', 'gpmnl042772772760', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.44', 'a37Va81z32Q+qMNhRKG/2l9J1yvw1tYvw+atWXuW3jPHOFKAwcvGY9dPfuyDiST4ruhe9gDAg36lm+zdeiSUnmd3kN99Io/88daPtzVtnyzqEpT/QzgDoI8Y6rbD7rnINzVLEBWAB7xdWfw5NVRFMQrFuvOUE3VqyM3F8uQdEvCBFoPCxxIbsEo3Dn6hjevgzO7nzZPdrmqDrMO8CE9CUqleJhRlC6MfJNA4M87ZtYt03q0XpAXM/zYd8tYJJu29UelxadoeZxBdQzuoU89XXcZtYs8rUJXd5GRqtEuLXMS0tgNrnf2en7+Xe+Zl3tNSOwoJSkwdFj07yjnPF8nvdg==', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;

---Apple pay -- PHP config


INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.MID.14.PHP', 'PAL-IPG PHP APPLEPAY', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.14.PHP', 'gpmnl045623832731', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.14.PHP', 'ov/87C1W9ahpp4MroX/Xray0pc/T2q0fE9R6pyAdVXrlw1+I9vU++oIul9AkHMa7H6Emb3msTyB91y34ST6Tysyi4Xvu/hYbB3KoFxairs1xOpXds3siOkNACVtFIhraIPhWi1TXbbMDRKkbe1U/zokXmdxsRRVjw6SJLevPLUSGVDXnjkbjIZM5rJ4PbwFHXfr3UQ5LW/PWgksxjMh34Yco+xT+4/gKO4r5cbr6GxlBmWcGtY//GGIq+lByAhDEiJvFsFLdBg6EyqyiSt4pf74NiN0XCKbljoQZ3U507P8PWi2tjsmeBp81kgpUmsPfE9MkucCDEey71KCpGbAmKA==', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;

-- Apple pay --rest of the currencies config
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.MID.14', 'PAL-IPG APPLEPAY', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.14', 'gpmnl042772772761', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.14', 'AFYDa+MdhNIANeSrpQORmKhqpOMAnMNF8xadipFE1LhLhfngyjowKj71fvDjoaBwUQZgMTN4GTofztmHbYkWNzuD/y8Qz/9QjSKs63P+o+G67fbYFwk8Q+Hcm5vS69TCi5wygsFiCLvoQsH3R0PTRdz2xoFvbFvJpjBU6gviJICMZUYQv9ZYqtVO7uEI/Ue5DSV3QIh4gSg/Rl6Yz9KfXcsm04j0D21IIu/hjxnnP+zeAhBUjxTOu1SggM9UAF+ryeOXXJc1HTDVFblotsZRFTn1mmmDrUKcgxcv1+JfVc3kqfyv0WFExHbKX9fmWZYpvJeQ8ie/3ePWSU+EZKdVhA==', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;

-- Globalpayments --end --

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

insert into system.cardpricing_tbl (pricepointid ,cardid ) select pricepointid,0 from system.cardpricing_tbl where cardid = 8
 ON conflict ON CONSTRAINT cardpricing_uq DO NOTHING;
 UPDATE SYSTEM.CARD_TBL set enabled=true where id=0;




