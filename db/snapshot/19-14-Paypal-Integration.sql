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

/*=========================End===================================== */

