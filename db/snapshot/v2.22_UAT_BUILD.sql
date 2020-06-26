-- UAT Script for 2.22 Release Date 16 June  

-- 19-09-Global-Payments-Integration.sql Update 
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.44', 'gpmnl042772772760', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.44.PHP', 'gpmnl045623832732', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.14', 'gpmnl042772772761', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.14.PHP', 'gpmnl045623832731', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.44', 'a37Va81z32Q+qMNhRKG/2l9J1yvw1tYvw+atWXuW3jPHOFKAwcvGY9dPfuyDiST4ruhe9gDAg36lm+zdeiSUnmd3kN99Io/88daPtzVtnyzqEpT/QzgDoI8Y6rbD7rnINzVLEBWAB7xdWfw5NVRFMQrFuvOUE3VqyM3F8uQdEvCBFoPCxxIbsEo3Dn6hjevgzO7nzZPdrmqDrMO8CE9CUqleJhRlC6MfJNA4M87ZtYt03q0XpAXM/zYd8tYJJu29UelxadoeZxBdQzuoU89XXcZtYs8rUJXd5GRqtEuLXMS0tgNrnf2en7+Xe+Zl3tNSOwoJSkwdFj07yjnPF8nvdg==', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.44.PHP', 'xrELE5yHDUR+jMpa8pcpIccQ2zQGjZMI45piyCzWR15AL9eLgFV5xhPciSHHUQtW1NpFpwip46oV1G2Oy9SQtBjEuszTVVPF3tOQVCaBhO6J3Tfjv8VBNLY2GEUPmpFwEKW+p79eJR0iEpMqdwy/necg2O0FfmDIcQ1ZlGh5G+asjIcgeWyZYjf+8UAy4qH/94TzNf2ku93W1xtobJXaQ5IcyC9dKxoAl3m4cqVTRDj1jKKjRdcsdt6IAopm4yorRlNy3pbZpdDq7OT2Jhb3uAe1O7fUWZye1hnTd4bzZpIxV2k/L81xaMnv9wLVsG/RiML0HqfWRzwcQNi+qpqayA==', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.14', 'AFYDa+MdhNIANeSrpQORmKhqpOMAnMNF8xadipFE1LhLhfngyjowKj71fvDjoaBwUQZgMTN4GTofztmHbYkWNzuD/y8Qz/9QjSKs63P+o+G67fbYFwk8Q+Hcm5vS69TCi5wygsFiCLvoQsH3R0PTRdz2xoFvbFvJpjBU6gviJICMZUYQv9ZYqtVO7uEI/Ue5DSV3QIh4gSg/Rl6Yz9KfXcsm04j0D21IIu/hjxnnP+zeAhBUjxTOu1SggM9UAF+ryeOXXJc1HTDVFblotsZRFTn1mmmDrUKcgxcv1+JfVc3kqfyv0WFExHbKX9fmWZYpvJeQ8ie/3ePWSU+EZKdVhA==', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.14.PHP', 'ov/87C1W9ahpp4MroX/Xray0pc/T2q0fE9R6pyAdVXrlw1+I9vU++oIul9AkHMa7H6Emb3msTyB91y34ST6Tysyi4Xvu/hYbB3KoFxairs1xOpXds3siOkNACVtFIhraIPhWi1TXbbMDRKkbe1U/zokXmdxsRRVjw6SJLevPLUSGVDXnjkbjIZM5rJ4PbwFHXfr3UQ5LW/PWgksxjMh34Yco+xT+4/gKO4r5cbr6GxlBmWcGtY//GGIq+lByAhDEiJvFsFLdBg6EyqyiSt4pf74NiN0XCKbljoQZ3U507P8PWi2tjsmeBp81kgpUmsPfE9MkucCDEey71KCpGbAmKA==', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;

delete from client.additionalproperty_tbl where key like 'GlobalPayment.merchantaccountrule%';
delete from client.additionalproperty_tbl where key like 'GlobalPayment.usernamerule%';
delete from client.additionalproperty_tbl where key like 'GlobalPayment.pwdrule%';
delete from client.additionalproperty_tbl where key like 'GlobalPayment.rule%';
delete from client.additionalproperty_tbl where key like 'GlobalPayment.Wallet.MID%';
delete from client.additionalproperty_tbl where key like 'GlobalPayment.Wallet.USERNAME%';
delete from client.additionalproperty_tbl where key like 'GlobalPayment.Wallet.PASSWORD%';

---Google pay -- PHP config

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.44.PHP', 'gpmnl045623832732', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.44.PHP', 'xrELE5yHDUR+jMpa8pcpIccQ2zQGjZMI45piyCzWR15AL9eLgFV5xhPciSHHUQtW1NpFpwip46oV1G2Oy9SQtBjEuszTVVPF3tOQVCaBhO6J3Tfjv8VBNLY2GEUPmpFwEKW+p79eJR0iEpMqdwy/necg2O0FfmDIcQ1ZlGh5G+asjIcgeWyZYjf+8UAy4qH/94TzNf2ku93W1xtobJXaQ5IcyC9dKxoAl3m4cqVTRDj1jKKjRdcsdt6IAopm4yorRlNy3pbZpdDq7OT2Jhb3uAe1O7fUWZye1hnTd4bzZpIxV2k/L81xaMnv9wLVsG/RiML0HqfWRzwcQNi+qpqayA==', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.44', 'gpmnl042772772760', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.44', 'a37Va81z32Q+qMNhRKG/2l9J1yvw1tYvw+atWXuW3jPHOFKAwcvGY9dPfuyDiST4ruhe9gDAg36lm+zdeiSUnmd3kN99Io/88daPtzVtnyzqEpT/QzgDoI8Y6rbD7rnINzVLEBWAB7xdWfw5NVRFMQrFuvOUE3VqyM3F8uQdEvCBFoPCxxIbsEo3Dn6hjevgzO7nzZPdrmqDrMO8CE9CUqleJhRlC6MfJNA4M87ZtYt03q0XpAXM/zYd8tYJJu29UelxadoeZxBdQzuoU89XXcZtYs8rUJXd5GRqtEuLXMS0tgNrnf2en7+Xe+Zl3tNSOwoJSkwdFj07yjnPF8nvdg==', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;


---Apple pay -- PHP config
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.14.PHP', 'gpmnl045623832731', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.14.PHP', 'ov/87C1W9ahpp4MroX/Xray0pc/T2q0fE9R6pyAdVXrlw1+I9vU++oIul9AkHMa7H6Emb3msTyB91y34ST6Tysyi4Xvu/hYbB3KoFxairs1xOpXds3siOkNACVtFIhraIPhWi1TXbbMDRKkbe1U/zokXmdxsRRVjw6SJLevPLUSGVDXnjkbjIZM5rJ4PbwFHXfr3UQ5LW/PWgksxjMh34Yco+xT+4/gKO4r5cbr6GxlBmWcGtY//GGIq+lByAhDEiJvFsFLdBg6EyqyiSt4pf74NiN0XCKbljoQZ3U507P8PWi2tjsmeBp81kgpUmsPfE9MkucCDEey71KCpGbAmKA==', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.14', 'gpmnl042772772761', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.14', 'AFYDa+MdhNIANeSrpQORmKhqpOMAnMNF8xadipFE1LhLhfngyjowKj71fvDjoaBwUQZgMTN4GTofztmHbYkWNzuD/y8Qz/9QjSKs63P+o+G67fbYFwk8Q+Hcm5vS69TCi5wygsFiCLvoQsH3R0PTRdz2xoFvbFvJpjBU6gviJICMZUYQv9ZYqtVO7uEI/Ue5DSV3QIh4gSg/Rl6Yz9KfXcsm04j0D21IIu/hjxnnP+zeAhBUjxTOu1SggM9UAF+ryeOXXJc1HTDVFblotsZRFTn1mmmDrUKcgxcv1+JfVc3kqfyv0WFExHbKX9fmWZYpvJeQ8ie/3ePWSU+EZKdVhA==', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=56;



-- 19-14-Paypal-Integration.sql
--------- CEBU Paypal configuration to setup multiple MIDs as per currency

--PHP and all other currencies not configured in additionalproperty_tbl will go the the default merchant account entry for PHP.
-- that is JPY, AED, AUD, IDR, BND, CNY, KRW,THB,TWD and MOP as defined in CEBU-6
-- default merchantaccount_tbl onboarding sql
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd, enabled, stored_card) VALUES (10077, 24,'ACCu12.jik2Wb3kzHJgFE1palQEsAHD7wsynoTYqRcOZAX7RzupgW4sQ', 'sb-sahh431638744_api1.business.example.com', '7W56K2VQBRYF8FLX', true, null);

---additional MIDs for SGD, HKD, MYR, USD
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'PAYPAL_USERNAME_SGD', 'sb-mohn91867880_api1.business.example.com', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=24;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'PAYPAL_PASSWORD_SGD', 'B9WX2HPY9DPD6284', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=24;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'PAYPAL_MID_SGD', 'ATG2YLmUZFFk6n5kHVGwCC2A2dsDAEAdhFEakpPDRN5lxmGq3zimTG6A', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=24;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'PAYPAL_USERNAME_HKD', 'sb-ph1ko1832308_api1.business.example.com', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=24;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'PAYPAL_PASSWORD_HKD', '5QBM4GMSFPV8AHN', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=24;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'PAYPAL_MID_HKD', 'A78isDTCcuwhNZKyvzOsXXSzxdUPA25dTmhkEQxH7G1T4iQSksAr-SK0', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=24;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'PAYPAL_USERNAME_MYR', 'sb-ivizq1858258_api1.business.example.com', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=24;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'PAYPAL_PASSWORD_MYR', 'VMXEJAT9DCLCR7LQ', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=24;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'PAYPAL_MID_MYR', 'AJXtiLishvWwk7Jm0EnHXPmvJv1xAqMsrFcJMX8Exsbl9aQ5NJRKPyem', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=24;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'PAYPAL_USERNAME_USD', 'sb-43kvng1868465_api1.business.example.com', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=24;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'PAYPAL_PASSWORD_USD', '37JT6WGJFFUJFRM3', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=24;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'PAYPAL_MID_USD', 'Awzp6NMJcMOsM6SlpR13Cez-7vPFAMODxiW5ZT0qx6EbatLFSrMoBKtc', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=24;

UPDATE client.additionalproperty_tbl SET  value = '5QBM4GMSFPV8AHNK' where key = 'PAYPAL_PASSWORD_HKD' and externalid in (select id from Client.MerchantAccount_Tbl where clientid=10077 and pspid=24);
/*=========================End===================================== */


-- 20-07-2c2p-ALC-Integration.sql

INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type,scope) SELECT 'mid.USD','CebuPacific_USD','t', id, 'merchant',2 FROM client.merchantaccount_tbl WHERE clientid=10077 AND pspid=40;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('TIMEZONE', 'Asia/Kuala_Lumpur', true, 10077, 'client', 2);



-- 20-07-CEBU-RMFSS-Integration.sql

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


-- 20-07-CyberSource-Integration.sql
-- Nothing

-- 20-09-Klarna-Integration.sql
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (36, 0, 0, true);


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

--- 20-09-SIT-Build.sql

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




----Master File's 

---- master_pg_v2.21.sql

ALTER TABLE system.country_tbl DROP COLUMN symbol;
----Increase length of additional_data_tbl's name name
ALTER TABLE log.additional_data_tbl ALTER COLUMN name TYPE varchar(30);



---- setup_pg_CRS_v2.22.sql

-- Alter Log.flight_tbl to store additional flight data
ALTER TABLE log.flight_tbl
  ADD COLUMN departure_countryid integer;

ALTER TABLE log.flight_tbl
  ADD COLUMN arrival_countryid integer;

ALTER TABLE log.flight_tbl
  ADD CONSTRAINT departure_countryid_country_tbl_id_fk
FOREIGN KEY (departure_countryid) REFERENCES system.country_tbl (id);

ALTER TABLE log.flight_tbl
  ADD CONSTRAINT arrival_countryid_country_tbl_id_fk
FOREIGN KEY (arrival_countryid) REFERENCES system.country_tbl (id);

-- Table: log.txnroute_tbl
CREATE TABLE log.paymentroute_tbl
(
  id serial NOT NULL,
  sessionid integer NOT NULL,
  pspid integer NOT NULL,
  preference integer NOT NULL,
  enabled boolean DEFAULT true,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  CONSTRAINT paymentroute_pk PRIMARY KEY (id),
  CONSTRAINT pspid FOREIGN KEY (pspid)
      REFERENCES system.psp_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE log.paymentroute_tbl OWNER TO postgres;



--  Revert Changes
ALTER TABLE log.additional_data_tbl ALTER COLUMN value TYPE varchar(50);


--- setup_pg_v2.21.sql

/* ========== CONFIGURE Cyber Fraud GateWay START========== */

insert into system.cardpricing_tbl (pricepointid ,cardid ) select pricepointid,0 from system.cardpricing_tbl where cardid = 8
 ON conflict ON CONSTRAINT cardpricing_uq DO NOTHING;
 UPDATE SYSTEM.CARD_TBL set enabled=true where id=0;







