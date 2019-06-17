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
--Sandbox env details. default merchant config
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10055, 56, 'Global Payments', 'empty', 'empty');
--or
UPDATE client.merchantaccount_tbl SET username = 'empty', passwd = 'empty' WHERE clientid=10055 and pspid=56;

INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100120, 56, '-1');

--Sandbox env details. Google Pay and Apple Pay MID config
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.merchantaccountrule', 'merchantaccount ::= (property[@name=''<mid>''])
 mid ::= "GlobalPayment.Wallet.MID."(transaction/@wallet-id)', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10055 and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.MID.44', 'PAL-IPG GOOGLEPAY', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10055 and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.MID.14', 'PAL-IPG APPLEPAY', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10055 and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.usernamerule', 'username ::= (property[@name=''<uname>''])
 uname ::= "GlobalPayment.Wallet.USERNAME."(transaction/@wallet-id)', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10055 and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.44', 'gpmnl042772772760', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10055 and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.14', 'gpmnl042772772761', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10055 and pspid=56;


INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.pwdrule', 'password ::= (property[@name=''<passwd>''])
 passwd ::= "GlobalPayment.Wallet.PASSWORD."(transaction/@wallet-id)', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10055 and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.44', 'a37Va81z32Q+qMNhRKG/2l9J1yvw1tYvw+atWXuW3jPHOFKAwcvGY9dPfuyDiST4ruhe9gDAg36lm+zdeiSUnmd3kN99Io/88daPtzVtnyzqEpT/QzgDoI8Y6rbD7rnINzVLEBWAB7xdWfw5NVRFMQrFuvOUE3VqyM3F8uQdEvCBFoPCxxIbsEo3Dn6hjevgzO7nzZPdrmqDrMO8CE9CUqleJhRlC6MfJNA4M87ZtYt03q0XpAXM/zYd8tYJJu29UelxadoeZxBdQzuoU89XXcZtYs8rUJXd5GRqtEuLXMS0tgNrnf2en7+Xe+Zl3tNSOwoJSkwdFj07yjnPF8nvdg==', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10055 and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.14', 'AFYDa+MdhNIANeSrpQORmKhqpOMAnMNF8xadipFE1LhLhfngyjowKj71fvDjoaBwUQZgMTN4GTofztmHbYkWNzuD/y8Qz/9QjSKs63P+o+G67fbYFwk8Q+Hcm5vS69TCi5wygsFiCLvoQsH3R0PTRdz2xoFvbFvJpjBU6gviJICMZUYQv9ZYqtVO7uEI/Ue5DSV3QIh4gSg/Rl6Yz9KfXcsm04j0D21IIu/hjxnnP+zeAhBUjxTOu1SggM9UAF+ryeOXXJc1HTDVFblotsZRFTn1mmmDrUKcgxcv1+JfVc3kqfyv0WFExHbKX9fmWZYpvJeQ8ie/3ePWSU+EZKdVhA==', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10055 and pspid=56;



INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment) VALUES (10055, 8, true, 56, 200, 1, null, false, 1,0);

--Setup CARD on file only option for Global payments client
insert into client.additionalproperty_tbl (key,value,enabled,externalid,type,scope)
select 'ALLOWEDPAYMENTMETHODS','CARD',true, id, 'merchant', 1 from client.merchantaccount_tbl WHERE clientid=10055 AND pspid=44;

--production sql
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (<clientid>, 56, <merchant name>, <username-mid>, <pwd>);

INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<accountid>, 56, '-1');

--edit if installment is to be enabled for specific SR, 0 means no installment option. 1 means installment is enabled.
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment) VALUES (<clientid>, <cardid>, true, 56, <countryid>, 1, null, false, 1,0);

--Setup CARD on file only option for Global payments client - Google Pay
insert into client.additionalproperty_tbl (key,value,enabled,externalid,type,scope)
select 'ALLOWEDPAYMENTMETHODS','CARD',true, id, 'merchant', 1 from client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=44;


INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.merchantaccountrule', 'merchantaccount ::= (property[@name=''<mid>''])
 mid ::= "GlobalPayment.Wallet.MID."(transaction/@wallet-id)', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.MID.44', 'PAL-IPG GOOGLEPAY', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.MID.14', 'PAL-IPG APPLEPAY', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.usernamerule', 'username ::= (property[@name=''<uname>''])
 uname ::= "GlobalPayment.Wallet.USERNAME."(transaction/@wallet-id)', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.44', 'gpmnl042772772760', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.14', 'gpmnl042772772761', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;


INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.pwdrule', 'password ::= (property[@name=''<passwd>''])
 passwd ::= "GlobalPayment.Wallet.PASSWORD."(transaction/@wallet-id)', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.44', 'a37Va81z32Q+qMNhRKG/2l9J1yvw1tYvw+atWXuW3jPHOFKAwcvGY9dPfuyDiST4ruhe9gDAg36lm+zdeiSUnmd3kN99Io/88daPtzVtnyzqEpT/QzgDoI8Y6rbD7rnINzVLEBWAB7xdWfw5NVRFMQrFuvOUE3VqyM3F8uQdEvCBFoPCxxIbsEo3Dn6hjevgzO7nzZPdrmqDrMO8CE9CUqleJhRlC6MfJNA4M87ZtYt03q0XpAXM/zYd8tYJJu29UelxadoeZxBdQzuoU89XXcZtYs8rUJXd5GRqtEuLXMS0tgNrnf2en7+Xe+Zl3tNSOwoJSkwdFj07yjnPF8nvdg==', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.14', 'AFYDa+MdhNIANeSrpQORmKhqpOMAnMNF8xadipFE1LhLhfngyjowKj71fvDjoaBwUQZgMTN4GTofztmHbYkWNzuD/y8Qz/9QjSKs63P+o+G67fbYFwk8Q+Hcm5vS69TCi5wygsFiCLvoQsH3R0PTRdz2xoFvbFvJpjBU6gviJICMZUYQv9ZYqtVO7uEI/Ue5DSV3QIh4gSg/Rl6Yz9KfXcsm04j0D21IIu/hjxnnP+zeAhBUjxTOu1SggM9UAF+ryeOXXJc1HTDVFblotsZRFTn1mmmDrUKcgxcv1+JfVc3kqfyv0WFExHbKX9fmWZYpvJeQ8ie/3ePWSU+EZKdVhA==', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=56;


-- Get status Additional properties
-- Retry count value should be integer
insert into client.additionalproperty_tbl (key,value,enabled,externalid,type,scope) values ('status_retry_count',<value>,true,<clientid>,'client',2);
-- Retry count value should be integer and it will be seconds
insert into client.additionalproperty_tbl (key,value,enabled,externalid,type,scope) values ('status_retry_interval',<value>,true,<clientid>,'client',2);


--********MADA-MPGS*******************--

--**********system.psp_tbl************--
INSERT INTO system.psp_tbl (id, name, enabled, system_type, capture_method, installment) VALUES (57, 'MADA MPGS', true, 1, 0, 0);

--**********system.pspcard_tbl************--
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (8, 57, true);

INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (7, 57, true);

--**********system.pspcurrency_tbl************--
INSERT INTO system.pspcurrency_tbl (pspid, name, enabled, currencyid) VALUES (57, 'SAR', true, 682);

--**********client.merchantaccount_tbl************--
INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, enabled, username, passwd, stored_card) VALUES (<clientid>, 57, 'TEST603001002',  true, 'merchant.TEST603001002', 'd92028b344ea6d1df4f89d1bc9fa0b78', null);

--**********client.merchantsubaccount_tbl************--
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, created, modified, enabled) VALUES (<accountid>, 57, '-1', '2016-03-31 08:59:59.941696', '2016-09-19 12:23:07.805804', true);

--**********client.cardaccess_tbl************--
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment) VALUES ( <clientid>, 7, true, 57, 608, 1, null, false, 1, 0);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment) VALUES ( <clientid>, 8, true, 57, 608, 1, null, false, 1, 0);

--**********client.additionalproperty_tbl************--
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'username.SAR', 'merchant.TEST603001002', true, <merchantaccount-ID>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'password.SAR', 'd92028b344ea6d1df4f89d1bc9fa0b78', true, <merchantaccount-ID>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'Notification-Secret.SAR', '561da90d33a04f990e1b28d7486db58f', true, <merchantaccount-ID>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'mid.SAR', 'TEST603001002', true, <merchantaccount-ID>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'HOST', 'ap-gateway.mastercard.com', true, <merchantaccount-ID>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('mvault', 'true', true, <ClientId>, 'client', 2);

--********END OF MADA-MPGS*******************--

--  CMP-3020 Configure Merchant choice of PayPal Payment Action - START --
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type )
    SELECT 'PAYPAL_AUTH_CAPTURE', 'false', id, 'merchant' FROM client.merchantaccount_tbl WHERE pspid=24;
--  CMP-3020 Configure Merchant choice of PayPal Payment Action - END --