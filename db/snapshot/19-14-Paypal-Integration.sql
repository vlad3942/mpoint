-- Paypal subject SITL & UAT
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
    SELECT 'PAYPAL_SUBJECT', 'pal_paypal_sandbox@pal.com.ph', id, 'merchant', 2 FROM client.merchantaccount_tbl WHERE pspid=24 AND clientid = 10055;

-- Paypal subject - PROD --edit client ID if required.
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
SELECT 'PAYPAL_SUBJECT', <value>, id, 'merchant', 2 FROM client.merchantaccount_tbl WHERE pspid=24 AND clientid = <clientid>;


/* ========== Global Configuration for Paypal : START========== */

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

