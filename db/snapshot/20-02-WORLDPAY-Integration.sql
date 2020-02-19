--//********Worldpay*******************//

--//**********system.pricepoint_tbl************//
INSERT INTO system.pricepoint_tbl (id, amount, enabled, currencyid) VALUES (-392, -1, true, 392);

--//**********system.cardpricing_tbl************//
INSERT INTO system.cardpricing_tbl (pricepointid, cardid, enabled) VALUES (-392, 7, true);
INSERT INTO system.cardpricing_tbl (pricepointid, cardid, enabled) VALUES (-392, 8, true);

--//**********system.psp_tbl************//
INSERT INTO system.psp_tbl (id, name, enabled, system_type) VALUES (4, 'WORLDPAY', true, 1);

--//**********system.pspcard_tbl************//
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (7, 4, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (8, 4, true);

--//**********system.pspcurrency_tbl************//
INSERT INTO system.pspcurrency_tbl (pspid, name, enabled, currencyid) VALUES (4, 'JPY', true, 392);

--//**********client.merchantaccount_tbl************//
INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, enabled, username, passwd, stored_card) VALUES (<clientid>, 4, 'CELLPOINT',  true, 'CELLPOINT', 'Mesb@1234', null);

--//**********client.merchantsubaccount_tbl************//
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (<accountid>, 4, '-1', true);

--//**********client.cardaccess_tbl************//
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment) VALUES ( <clientid>, 7, true, 4, 616, 1, null, false, 1, 0);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment) VALUES ( <clientid>, 8, true, 4, 616, 1, null, false, 1, 0);

--//**********client.additionalproperty_tbl************//
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( '3DVERIFICATION', 'false', true, <merchant-id>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'debug', 'false', true, <cliend-id>, 'client', 2);

//********END OF Worldpay*******************//