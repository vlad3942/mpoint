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
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (<clientid>, 56, 'Global Payments', 'empty', 'empty');
--or
UPDATE client.merchantaccount_tbl SET username = 'empty', passwd = 'empty' WHERE clientid=<clientid> and pspid=56;

INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100120, 56, '-1');

--Sandbox env details. Google Pay and Apple Pay MID config
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.merchantaccountrule', 'merchantaccount ::= (property[@name=''<mid>''])
 mid ::= (transaction/authorized-amount/@currency)=="PHP"="GlobalPayment.Wallet.MID."(transaction/@wallet-id)".PHP":"GlobalPayment.Wallet.MID."(transaction/@wallet-id)', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=<pspid>;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.MID.44', 'PAL-IPG GOOGLEPAY', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=44;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.MID.44.PHP', 'PAL-IPG PHP GOOGLEPAY', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=44;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.MID.14', 'PAL-IPG APPLEPAY', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=14;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.MID.14.PHP', 'PAL-IPG PHP APPLEPAY', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=14;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.usernamerule', 'username ::= (property[@name=''<uname>''])
 uname ::= (transaction/authorized-amount/@currency)=="PHP"="GlobalPayment.Wallet.USERNAME."(transaction/@wallet-id)".PHP":"GlobalPayment.Wallet.USERNAME."(transaction/@wallet-id)"', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=<pspid>;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.44', 'gpmnl042772772760', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=44;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.44.PHP', 'gpmnl045623832732', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=44;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.14', 'gpmnl042772772761', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=14;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.14.PHP', 'gpmnl045623832731', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=14;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.pwdrule', 'password ::= (property[@name=''<passwd>''])
 passwd ::= (transaction/authorized-amount/@currency)=="PHP"="GlobalPayment.Wallet.PASSWORD."(transaction/@wallet-id)".PHP":"GlobalPayment.Wallet.PASSWORD."(transaction/@wallet-id)', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=<pspid>;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.44', 'a37Va81z32Q+qMNhRKG/2l9J1yvw1tYvw+atWXuW3jPHOFKAwcvGY9dPfuyDiST4ruhe9gDAg36lm+zdeiSUnmd3kN99Io/88daPtzVtnyzqEpT/QzgDoI8Y6rbD7rnINzVLEBWAB7xdWfw5NVRFMQrFuvOUE3VqyM3F8uQdEvCBFoPCxxIbsEo3Dn6hjevgzO7nzZPdrmqDrMO8CE9CUqleJhRlC6MfJNA4M87ZtYt03q0XpAXM/zYd8tYJJu29UelxadoeZxBdQzuoU89XXcZtYs8rUJXd5GRqtEuLXMS0tgNrnf2en7+Xe+Zl3tNSOwoJSkwdFj07yjnPF8nvdg==', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=44;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.44.PHP', <testpwd-changethis>, true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=44;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.14', 'AFYDa+MdhNIANeSrpQORmKhqpOMAnMNF8xadipFE1LhLhfngyjowKj71fvDjoaBwUQZgMTN4GTofztmHbYkWNzuD/y8Qz/9QjSKs63P+o+G67fbYFwk8Q+Hcm5vS69TCi5wygsFiCLvoQsH3R0PTRdz2xoFvbFvJpjBU6gviJICMZUYQv9ZYqtVO7uEI/Ue5DSV3QIh4gSg/Rl6Yz9KfXcsm04j0D21IIu/hjxnnP+zeAhBUjxTOu1SggM9UAF+ryeOXXJc1HTDVFblotsZRFTn1mmmDrUKcgxcv1+JfVc3kqfyv0WFExHbKX9fmWZYpvJeQ8ie/3ePWSU+EZKdVhA==', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=14;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.14.PHP', <testpwd-changethis>, true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=<clientid> and pspid=14;


INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment) VALUES (<clientid>, 8, true, 56, 200, 1, null, false, 1,0);

--Setup CARD on file only option for Global payments client
insert into client.additionalproperty_tbl (key,value,enabled,externalid,type,scope)
select 'ALLOWEDPAYMENTMETHODS','CARD',true, id, 'merchant', 1 from client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=44;

--production sql
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (<clientid>, 56, <merchant name>, <username-mid>, <pwd>);

INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<accountid>, 56, '-1');

--edit if installment is to be enabled for specific SR, 0 means no installment option. 1 means installment is enabled.
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment) VALUES (<clientid>, <cardid>, true, 56, <countryid>, 1, null, false, 1,0);

--Setup CARD on file only option for Global payments client - Google Pay
insert into client.additionalproperty_tbl (key,value,enabled,externalid,type,scope)
select 'ALLOWEDPAYMENTMETHODS','CARD',true, id, 'merchant', 1 from client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=44;

-- google pay api version 2.0 support payment method 'PAN_ONLY','CRYPTOGRAM_3DS'. previously PAN_ONLY is CARD and CRYPTOGRAM_3DS is TOKENIZED_CARD
UPDATE client.additionalproperty_tbl SET VALUES = 'PAN_ONLY' WHERE  key = 'ALLOWEDPAYMENTMETHODS' AND VALUES = 'CARD';

---------------------------Added config changes for PHP -- SIT --
-- NOTE:: when running SQL please Replace with Sandbox test key for GPay and Apple Pay MIDs
---Google pay -- PHP config
update client.additionalproperty_tbl set value='merchantaccount ::= (property[@name=''<mid>''])   
 mid ::= (transaction/authorized-amount/@currency)=="PHP"="GlobalPayment.Wallet.MID."(transaction/@wallet-id)".PHP":"GlobalPayment.Wallet.MID."(transaction/@wallet-id)'
where key='GlobalPayment.mechantaccountrule' and externalid=(select id from Client.MerchantAccount_Tbl where clientid=10020 and pspid=44);

update client.additionalproperty_tbl set value='username ::= (property[@name=''<uname>''])
 uname ::= (transaction/authorized-amount/@currency)=="PHP"="GlobalPayment.Wallet.USERNAME."(transaction/@wallet-id)".PHP":"GlobalPayment.Wallet.USERNAME."(transaction/@wallet-id)"'
where key='GlobalPayment.usernamerule' and externalid=(select id from Client.MerchantAccount_Tbl where clientid=10020 and pspid=44);

update client.additionalproperty_tbl set value='password ::= (property[@name=''<passwd>''])
 passwd ::= (transaction/authorized-amount/@currency)=="PHP"="GlobalPayment.Wallet.PASSWORD."(transaction/@wallet-id)".PHP":"GlobalPayment.Wallet.PASSWORD."(transaction/@wallet-id)'
where key='GlobalPayment.pwdrule' and externalid =(select id from Client.MerchantAccount_Tbl where clientid=10020 and pspid=44);



INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.MID.44.PHP', 'PAL-IPG PHP GOOGLEPAY', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=44;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.44.PHP', 'gpmnl045623832732', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=44;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.44.PHP', <testpwd--changethis>, true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=44;

---Apple pay -- PHP config

update client.additionalproperty_tbl set value='merchantaccount ::= (property[@name=''<mid>''])
 mid ::= (transaction/authorized-amount/@currency)=="PHP"="GlobalPayment.Wallet.MID."(transaction/@wallet-id)".PHP":"GlobalPayment.Wallet.MID."(transaction/@wallet-id)'
where key='GlobalPayment.mechantaccountrule' and externalid=(select id from Client.MerchantAccount_Tbl where clientid=10020 and pspid=14);

update client.additionalproperty_tbl set value='username ::= (property[@name=''<uname>''])
 uname ::= (transaction/authorized-amount/@currency)=="PHP"="GlobalPayment.Wallet.USERNAME."(transaction/@wallet-id)".PHP":"GlobalPayment.Wallet.USERNAME."(transaction/@wallet-id)"'
where key='GlobalPayment.usernamerule' and externalid=(select id from Client.MerchantAccount_Tbl where clientid=10020 and pspid=14);

update client.additionalproperty_tbl set value='password ::= (property[@name=''<passwd>''])
 passwd ::= (transaction/authorized-amount/@currency)=="PHP"="GlobalPayment.Wallet.PASSWORD."(transaction/@wallet-id)".PHP":"GlobalPayment.Wallet.PASSWORD."(transaction/@wallet-id)'
where key='GlobalPayment.pwdrule' and externalid=(select id from Client.MerchantAccount_Tbl where clientid=10020 and pspid=14);


INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.MID.14.PHP', 'PAL-IPG PHP APPLEPAY', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=14;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.14.PHP', 'gpmnl045623832731', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=14;
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.14.PHP', <testpwd-changethis>, true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10020 and pspid=14;


