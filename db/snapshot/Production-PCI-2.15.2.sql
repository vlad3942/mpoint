/* ========== Global Configuration for Citcon - WeChat Pay - Payment Method : START========== */

INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (39, 'WeChat Pay', 23, -1, -1, -1,6);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (39, 0, 0);
INSERT INTO system.cardpricing_tbl ( pricepointid, cardid) VALUES ( -840, 39);
INSERT INTO system.cardpricing_tbl ( pricepointid, cardid) VALUES ( -156, 39);


INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (41, 'Citcon',5);
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (840,41,'USD');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (156,56,'CNY');

INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (39, 41);
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name) VALUES (<clientid>, 41, 'Citcon');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<accountid>, 41, <MERCHANTTOKEN>); -- For Android and iOS merchant accounts.
INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position,psp_type) VALUES (<clientid>, 39, true, 41, <CountryId>, 1, null,5);

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('MERCHANT_API_TOKEN', <WEB-MERCHANT-TOKEN>, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = <clientid> and pspid = 41), 'merchant');


INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('MERCHANT_VENDOR', 'wechatpay', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = <clientid> and pspid = 41), 'merchant');

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('ALLOW_DUPLICATES', 'no', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = <clientid> and pspid = 41), 'merchant');

--QR Code timeout value in seconds
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('QR_CODE_TIMEOUT', '180', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = <clientid> and pspid = 41), 'merchant');

--Virtual payment page timer value in mm:ss, this should be less than or equal to the QR code timeout property
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('VIRTUAL_PAYMENT_TIMER', '02:00', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = <clientid> and pspid = 41), 'merchant');

--url to link wechat icon
INSERT INTO client.url_tbl(urltypeid, clientid, url)
VALUES (14, <clientid>, "https://s3-ap-southeast-1.amazonaws.com/cpmassets/payment/icons");

--Redirect URL for HPP integration - can be blank or accept url or as required for client configuration
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('WECHAT_CALLBACK_URL', '', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = <clientid> and pspid = 41), 'merchant');

/*=========================End===================================== */



/* ========== Paypal ================= */

INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (24, 'PayPal',4);
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (28, 'PayPal', 23, -1, -1, -1,4);
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (28, 24);


INSERT INTO Client.CardAccess_Tbl (countryid, clientid, cardid, pspid,enabled,stateid,psp_type) values (638,<clientid>, 28, 24, 't', 1, 4 );
insert into system.cardpricing_tbl (pricepointid, cardid, enabled) values ('-458', 28, 't');
insert into System.PSPCurrency_Tbl (pspid, name, enabled,currencyid) values (24, 'MYR', 't',458);


INSERT INTO Client.CardAccess_Tbl (countryid, clientid, cardid, pspid,enabled,stateid,psp_type) values (647,<clientid>, 28, 24, 't', 1, 4 );
insert into system.cardpricing_tbl (pricepointid, cardid, enabled) values ('-784', 28, 't');
insert into System.PSPCurrency_Tbl (pspid, name, enabled,currencyid) values (24, 'AUD', 't',36);


INSERT INTO Client.CardAccess_Tbl (countryid, clientid, cardid, pspid,enabled,stateid,psp_type) values (500,<clientid>, 28, 24, 't', 1, 4 );
insert into system.cardpricing_tbl (pricepointid, cardid, enabled) values ('-36', 28, 't');
insert into System.PSPCurrency_Tbl (pspid, name, enabled,currencyid) values (24, 'AUD', 't',36);


INSERT INTO Client.CardAccess_Tbl (countryid, clientid, cardid, pspid,enabled,stateid,psp_type) values (202,<clientid>, 28, 24, 't', 1, 4 );
insert into system.cardpricing_tbl (pricepointid, cardid, enabled) values ('-124', 28, 't');
insert into System.PSPCurrency_Tbl (pspid, name, enabled,currencyid) values (24, 'CAD', 't',124);


INSERT INTO Client.CardAccess_Tbl (countryid, clientid, cardid, pspid,enabled,stateid,psp_type) values (103,<clientid>, 28, 24, 't', 1, 4 );
insert into system.cardpricing_tbl (pricepointid, cardid, enabled) values ('-826', 28, 't');
insert into System.PSPCurrency_Tbl (pspid, name, enabled,currencyid) values (24, 'GBP', 't',826);


INSERT INTO Client.CardAccess_Tbl (countryid, clientid, cardid, pspid,enabled,stateid,psp_type) values (614,<clientid>, 28, 24, 't', 1, 4 );
insert into system.cardpricing_tbl (pricepointid, cardid, enabled) values ('-344', 28, 't');
insert into System.PSPCurrency_Tbl (pspid, name, enabled,currencyid) values (24, 'HKD', 't',344);


INSERT INTO Client.CardAccess_Tbl (countryid, clientid, cardid, pspid,enabled,stateid,psp_type) values (505,<clientid>, 28, 24, 't', 1, 4 );
insert into system.cardpricing_tbl (pricepointid, cardid, enabled) values ('-360', 28, 't');
insert into System.PSPCurrency_Tbl (pspid, name, enabled,currencyid) values (24, 'IDR', 't',360);


INSERT INTO Client.CardAccess_Tbl (countryid, clientid, cardid, pspid,enabled,stateid,psp_type) values (616,<clientid>, 28, 24, 't', 1, 4 );
insert into system.cardpricing_tbl (pricepointid, cardid, enabled) values ('-392', 28, 't');
insert into System.PSPCurrency_Tbl (pspid, name, enabled,currencyid) values (24, 'JPY', 't',392);


INSERT INTO Client.CardAccess_Tbl (countryid, clientid, cardid, pspid,enabled,stateid,psp_type) values (632,<clientid>, 28, 24, 't', 1, 4 );
insert into system.cardpricing_tbl (pricepointid, cardid, enabled) values ('-410', 28, 't');
insert into System.PSPCurrency_Tbl (pspid, name, enabled,currencyid) values (24, 'KRW', 't',410);


INSERT INTO Client.CardAccess_Tbl (countryid, clientid, cardid, pspid,enabled,stateid,psp_type) values (513,<clientid>, 28, 24, 't', 1, 4 );
insert into system.cardpricing_tbl (pricepointid, cardid, enabled) values ('-554', 28, 't');
insert into System.PSPCurrency_Tbl (pspid, name, enabled,currencyid) values (24, 'NZD', 't',554);


INSERT INTO Client.CardAccess_Tbl (countryid, clientid, cardid, pspid,enabled,stateid,psp_type) values (674,<clientid>, 28, 24, 't', 1, 4 );
insert into system.cardpricing_tbl (pricepointid, cardid, enabled) values ('-598', 28, 't');
insert into System.PSPCurrency_Tbl (pspid, name, enabled,currencyid) values (24, 'PGK', 't',598);


INSERT INTO Client.CardAccess_Tbl (countryid, clientid, cardid, pspid,enabled,stateid,psp_type) values (606,<clientid>, 28, 24, 't', 1, 4 );
insert into system.cardpricing_tbl (pricepointid, cardid, enabled) values ('-634', 28, 't')
insert into System.PSPCurrency_Tbl (pspid, name, enabled,currencyid) values (24, 'QAR', 't',634)


INSERT INTO Client.CardAccess_Tbl (countryid, clientid, cardid, pspid,enabled,stateid,psp_type) values (608,<clientid>, 28, 24, 't', 1, 4 );
insert into system.cardpricing_tbl (pricepointid, cardid, enabled) values ('-682', 28, 't')
insert into System.PSPCurrency_Tbl (pspid, name, enabled,currencyid) values (24, 'SAR', 't',682)


INSERT INTO Client.CardAccess_Tbl (countryid, clientid, cardid, pspid,enabled,stateid,psp_type) values (644,<clientid>, 28, 24, 't', 1, 4 );
insert into system.cardpricing_tbl (pricepointid, cardid, enabled) values ('-764', 28, 't')
insert into System.PSPCurrency_Tbl (pspid, name, enabled,currencyid) values (24, 'THB', 't',764)


INSERT INTO Client.CardAccess_Tbl (countryid, clientid, cardid, pspid,enabled,stateid,psp_type) values (646,<clientid>, 28, 24, 't', 1, 4 );
insert into system.cardpricing_tbl (pricepointid, cardid, enabled) values ('-901', 28, 't');
insert into System.PSPCurrency_Tbl (pspid, name, enabled,currencyid) values (24, 'TWD', 't',901);


INSERT INTO Client.CardAccess_Tbl (countryid, clientid, cardid, pspid,enabled,stateid,psp_type) values (200,<clientid>, 28, 24, 't', 1, 4 );
insert into system.cardpricing_tbl (pricepointid, cardid, enabled) values ('-840', 28, 't');
insert into System.PSPCurrency_Tbl (pspid, name, enabled,currencyid) values (24, 'USD', 't',840);
REL_v2.14.10

INSERT INTO Client.CardAccess_Tbl (countryid, clientid, cardid, pspid,enabled,stateid,psp_type) values (640,<clientid>, 28, 24, 't', 1, 4 );
insert into system.cardpricing_tbl (pricepointid, cardid, enabled) values ('-608', 15, 't');
insert into System.PSPCurrency_Tbl (pspid, name, enabled,currencyid) values (56, 'PHP', 't',608);


INSERT INTO client.merchantaccount_tbl (clientid,pspid,name,username,passwd) VALUES (<clientid>,24,'Aj-KgkP9gPuP8-fV0MIkJdp5JSojAmYk5TWDRePvMtEFV1W3hZaeyfAd','cellpoint_sandbox_api1.pal.com.ph','Q2BBNZD97TYCP4WL')

INSERT INTO client.merchantsubaccount_tbl (accountid,pspid,"name") VALUES (<accountid>,24,'-1')

INSERT INTO client.additionalproperty_tbl( key, value, externalid, type ) SELECT 'PAYPAL_STC', 'true', id, 'merchant' FROM client.merchantaccount_tbl WHERE pspid=24 AND clientid = <clientid>;

INSERT INTO client.additionalproperty_tbl( key, value, externalid, type ) SELECT 'PAYPAL_REST_ACC_ID', '897383MMQSC9W', id, 'merchant' FROM client.merchantaccount_tbl WHERE pspid=24 AND clientid = <clientid>;

INSERT INTO client.additionalproperty_tbl( key, value, externalid, type ) SELECT 'PAYPAL_REST_CLIENT_ID', 'AejFqzw9vADty0xlc9oAgI0Rz0LQXYaoZyGPo0rlNiMx7taGI5C1VxqrGpT9zVjg1LMiPwfzkftO0W3U', id, 'merchant' FROM client.merchantaccount_tbl WHERE pspid=24 AND clientid = <clientid>;

INSERT INTO client.additionalproperty_tbl( key, value, externalid, type ) SELECT 'PAYPAL_REST_SECRET', 'EEmWU-1Bcmfuhe0xheaAlrArpEx2uzrBcB-HVkm125max3hgtVJc4d26bWe0TuDmks-kOl7WlqoRn4-G', id, 'merchant' FROM client.merchantaccount_tbl WHERE pspid=24 AND clientid = <clientid>;

INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope ) SELECT 'PAYPAL_BILLING_AGREEMENT', 'This is billing agreement with CellPoint Mobile', id, 'merchant', 2 FROM client.merchantaccount_tbl WHERE pspid=24 AND clientid = <client id>;

INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
SELECT 'PAYPAL_SUBJECT', 'pal_paypal_sandbox@pal.com.ph', id, 'merchant', 2 FROM client.merchantaccount_tbl WHERE pspid=24 AND clientid = <clientid>;



/* ========== Global-Payments   ========== */

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
 mid ::= "GlobalPayment.Wallet.MID."(transaction/@wallet-id)', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10055 and pspid=<pspid>;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.MID.44', 'PAL-IPG GOOGLEPAY', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10055 and pspid=44;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.MID.14', 'PAL-IPG APPLEPAY', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10055 and pspid=14;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.usernamerule', 'username ::= (property[@name=''<uname>''])
 uname ::= "GlobalPayment.Wallet.USERNAME."(transaction/@wallet-id)', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10055 and pspid=<pspid>;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.44', 'gpmnl042772772760', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10055 and pspid=44;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.USERNAME.14', 'gpmnl042772772761', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10055 and pspid=14;


INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.pwdrule', 'password ::= (property[@name=''<passwd>''])
 passwd ::= "GlobalPayment.Wallet.PASSWORD."(transaction/@wallet-id)', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10055 and pspid=<pspid>;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.44', 'a37Va81z32Q+qMNhRKG/2l9J1yvw1tYvw+atWXuW3jPHOFKAwcvGY9dPfuyDiST4ruhe9gDAg36lm+zdeiSUnmd3kN99Io/88daPtzVtnyzqEpT/QzgDoI8Y6rbD7rnINzVLEBWAB7xdWfw5NVRFMQrFuvOUE3VqyM3F8uQdEvCBFoPCxxIbsEo3Dn6hjevgzO7nzZPdrmqDrMO8CE9CUqleJhRlC6MfJNA4M87ZtYt03q0XpAXM/zYd8tYJJu29UelxadoeZxBdQzuoU89XXcZtYs8rUJXd5GRqtEuLXMS0tgNrnf2en7+Xe+Zl3tNSOwoJSkwdFj07yjnPF8nvdg==', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10055 and pspid=44;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'GlobalPayment.Wallet.PASSWORD.14', 'AFYDa+MdhNIANeSrpQORmKhqpOMAnMNF8xadipFE1LhLhfngyjowKj71fvDjoaBwUQZgMTN4GTofztmHbYkWNzuD/y8Qz/9QjSKs63P+o+G67fbYFwk8Q+Hcm5vS69TCi5wygsFiCLvoQsH3R0PTRdz2xoFvbFvJpjBU6gviJICMZUYQv9ZYqtVO7uEI/Ue5DSV3QIh4gSg/Rl6Yz9KfXcsm04j0D21IIu/hjxnnP+zeAhBUjxTOu1SggM9UAF+ryeOXXJc1HTDVFblotsZRFTn1mmmDrUKcgxcv1+JfVc3kqfyv0WFExHbKX9fmWZYpvJeQ8ie/3ePWSU+EZKdVhA==', true, id, 'merchant', 0 from Client.MerchantAccount_Tbl where clientid=10055 and pspid=14;



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




/* ========== MADA-MPGS ================ */

--If Entry for MADA card already exists
INSERT INTO system.card_tbl (id, name, logo, enabled, position, minlength, maxlength, cvclength, paymenttype) VALUES (71, 'MADA', null, true, 23, 16, 16, 3, 4);
--else
update  system.card_tbl set minlength = 16, maxlength = 16,cvclength = 3 where id = 71;

--//**********system.pricepoint_tbl************//
INSERT INTO system.pricepoint_tbl (id, amount, enabled, currencyid) VALUES (-682, -1, true, 682);

--//**********system.cardpricing_tbl************//
INSERT INTO system.cardpricing_tbl (pricepointid, cardid, enabled) VALUES (-682, 71, true);


--//**********system.psp_tbl************//
INSERT INTO system.psp_tbl (id, name, enabled, system_type, capture_method, installment) VALUES (57, 'MADA MPGS', true, 1, 0, 0);

--//**********system.pspcard_tbl************//
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (71, 57, true);

--//**********system.pspcurrency_tbl************//
INSERT INTO system.pspcurrency_tbl (pspid, name, enabled, currencyid) VALUES (57, 'SAR', true, 682);

--//**********client.merchantaccount_tbl************//
INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, enabled, username, passwd, stored_card) VALUES (<clientid>, 57, 'TEST603001002',  true, 'merchant.TEST603001002', 'd92028b344ea6d1df4f89d1bc9fa0b78', null);

--//**********client.merchantsubaccount_tbl************//
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, created, modified, enabled) VALUES (<accountid>, 57, '-1', '2016-03-31 08:59:59.941696', '2016-09-19 12:23:07.805804', true);

--//**********client.cardaccess_tbl************//
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment) VALUES ( <clientid>, 71, true, 57, 608, 1, null, false, 1, 0);


--//**********client.additionalproperty_tbl************//
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'username.SAR', 'merchant.TEST603001002', true, <merchantaccount-ID>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'password.SAR', 'd92028b344ea6d1df4f89d1bc9fa0b78', true, <merchantaccount-ID>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'Notification-Secret.SAR', '561da90d33a04f990e1b28d7486db58f', true, <merchantaccount-ID>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'mid.SAR', 'TEST603001002', true, <merchantaccount-ID>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'HOST', 'ap-gateway.mastercard.com', true, <merchantaccount-ID>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('mvault', 'true', true, <ClientId>, 'client', 2);


--//**********system.cardprefix_tbl Bin range************//
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,0	,0, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,400861	,400861, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,401757	,401757, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,409201	,409201, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,410685	,410685, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,417633	,417633, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,419593	,419593, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,422817	,422819, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,428331	,428331, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,428671	,428673, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,431361	,431361, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,432328	,432328, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,434107	,434107, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,439954	,439954, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,439956	,439956, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,440533	,440533, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,440647	,440647, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,440795	,440795, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,445564	,445564, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,446393	,446393, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,446404	,446404, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,446672	,446672, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,455036	,455036, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,455708	,455708, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,457865	,457865, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,458456	,458456, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,462220	,462220, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,468540	,468543, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,483010	,483012, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,484783	,484783, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,486094	,486096, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,489317	,489319, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,493428	,493428, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,504300	,504300, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,508160	,508160, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,521076	,521076, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,524130	,524130, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,524514	,524514, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,529415	,529415, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,529741	,529741, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,530906	,530906, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,531095	,531095, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,532013	,532013, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,535825	,535825, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,535989	,535989, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,536023	,536023, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,537767	,537767, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,539931	,539931, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,543357	,543357, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,554180	,554180, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,557606	,557606, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,558848	,558848, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,585265	,585265, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,588845	,588851, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,588982	,588983, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,589005	,589005, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,589206	,589206, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,604906	,604906, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,605141	,605141, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,636120	,636120, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,968201	,968211, true);




