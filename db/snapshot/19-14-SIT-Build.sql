-- Paypal subject SITL & UAT
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
    SELECT 'PAYPAL_SUBJECT', 'pal_paypal_sandbox@pal.com.ph', id, 'merchant', 2 FROM client.merchantaccount_tbl WHERE pspid=24 AND clientid = 10055;

-- Paypal subject - PROD --edit client ID if required.
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
SELECT 'PAYPAL_SUBJECT', 'pal_paypal_sandbox@pal.com.ph', id, 'merchant', 2 FROM client.merchantaccount_tbl WHERE pspid=24 AND clientid = 10020;


--- Enable paypal for different country / currency ---------
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


IDR-  INSERT INTO Client.CardAccess_Tbl (countryid, clientid, cardid, pspid,enabled,stateid,psp_type) values (505,<clientid>, 28, 24, 't', 1, 4 );
insert into system.cardpricing_tbl (pricepointid, cardid, enabled) values ('-360', 28, 't');
insert into System.PSPCurrency_Tbl (pspid, name, enabled,currencyid) values (24, 'IDR', 't',360);


JPY-  INSERT INTO Client.CardAccess_Tbl (countryid, clientid, cardid, pspid,enabled,stateid,psp_type) values (616,<clientid>, 28, 24, 't', 1, 4 );
insert into system.cardpricing_tbl (pricepointid, cardid, enabled) values ('-392', 28, 't');
insert into System.PSPCurrency_Tbl (pspid, name, enabled,currencyid) values (24, 'JPY', 't',392);


KRW-  INSERT INTO Client.CardAccess_Tbl (countryid, clientid, cardid, pspid,enabled,stateid,psp_type) values (632,<clientid>, 28, 24, 't', 1, 4 );
insert into system.cardpricing_tbl (pricepointid, cardid, enabled) values ('-410', 28, 't');
insert into System.PSPCurrency_Tbl (pspid, name, enabled,currencyid) values (24, 'KRW', 't',410);


NZD-  INSERT INTO Client.CardAccess_Tbl (countryid, clientid, cardid, pspid,enabled,stateid,psp_type) values (513,<clientid>, 28, 24, 't', 1, 4 );
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







