INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (56, 'Global Payments',1);
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (56, 1);	-- American Express
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (56, 3);	-- Diners Club
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (56, 7);	-- MasterCard
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (56, 8);	-- VISA
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (56, 5);	-- JCB
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (56, 6);	-- Maestro
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (56, 15);	-- Apple Pay
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (56, 23);	-- MasterPass
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (56, 16);	-- VCO
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (56, 41);	-- Google Pay
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (56, 81);	-- Samsung Pay


--Add currency support as required for client - TODO review
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (208,56,'DKK');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (156,56,'CNY');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (124,56,'CAD');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (36,56,'AUD');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (446,56,'MOP');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (414,56,'KWD');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (410,56,'KRW');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (392,56,'JPY');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (360,56,'IDR');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (356,56,'INR');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (344,56,'HKD');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (458,56,'MYR');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (554,56,'NZD');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (598,56,'PGK');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (608,56,'PHP');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (840,56,'USD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (634,56,'QAR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (682,56,'SAR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (702,56,'SGD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (764,56,'THB');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (784,56,'AED');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (826,56,'GBP');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (901,56,'TWD');


-- Merchant MID configuration --
--Sandbox env details.
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10055, 56, 'Global Payments', 'gpmnl042772772760', 'a37Va81z32Q+qMNhRKG/2l9J1yvw1tYvw+atWXuW3jPHOFKAwcvGY9dPfuyDiST4ruhe9gDAg36lm+zdeiSUnmd3kN99Io/88daPtzVtnyzqEpT/QzgDoI8Y6rbD7rnINzVLEBWAB7xdWfw5NVRFMQrFuvOUE3VqyM3F8uQdEvCBFoPCxxIbsEo3Dn6hjevgzO7nzZPdrmqDrMO8CE9CUqleJhRlC6MfJNA4M87ZtYt03q0XpAXM/zYd8tYJJu29UelxadoeZxBdQzuoU89XXcZtYs8rUJXd5GRqtEuLXMS0tgNrnf2en7+Xe+Zl3tNSOwoJSkwdFj07yjnPF8nvdg==');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100120, 56, '-1');

INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment) VALUES (10055, 8, true, 56, 200, 1, null, false, 1,0);

--Setup CARD on file only option for Global payments client
insert into client.additionalproperty_tbl (key,value,enabled,externalid,type,scope)
select 'ALLOWEDPAYMENTMETHODS','CARD',true, id, 'merchant', 1 from client.merchantaccount_tbl WHERE clientid=10055 AND pspid=44;

--production sql
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (<clientid>, 56, <merchant name>, <username-mid>, <pwd>);
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<accountid>, 56, '-1');
--edit if installment is to be enabled for specific SR, 0 means no installment option. 1 means installment is enabled.
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment) VALUES (<clientid>, <cardid>, true, 56, <countryid>, 1, null, false, 1,0);

--Setup CARD on file only option for Global payments client
insert into client.additionalproperty_tbl (key,value,enabled,externalid,type,scope)
select 'ALLOWEDPAYMENTMETHODS','CARD',true, id, 'merchant', 1 from client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=44;

