--//********First-Data*******************//

--//**********system.card_tbl************//
--//**********system.pricepoint_tbl************//
INSERT INTO system.pricepoint_tbl (id, amount, enabled, currencyid) VALUES (-608, -1, true, 608);

--//**********system.cardpricing_tbl************//
INSERT INTO system.cardpricing_tbl (pricepointid, cardid, enabled) VALUES (-608, 7, true);
INSERT INTO system.cardpricing_tbl (pricepointid, cardid, enabled) VALUES (-608, 8, true);


--//**********system.psp_tbl************//
INSERT INTO system.psp_tbl (id, name, enabled, system_type) VALUES (62, 'FIRST DATA', true, 1);

--//**********system.pspcard_tbl************//
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (7, 62, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (8, 62, true);

--//**********system.pspcurrency_tbl************//
INSERT INTO system.pspcurrency_tbl (pspid, name, enabled, currencyid) VALUES (61, 'PHL', true, 608);

--//**********client.merchantaccount_tbl************//
INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, enabled, username, passwd, stored_card) VALUES (<clientid>, 62, '6160800000',  true, 'WS6160800000._.1', 'tester01$', null);

--//**********client.merchantsubaccount_tbl************//
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (<accountid>, 62, '-1', true);

--//**********client.cardaccess_tbl************//
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment, capture_type) VALUES ( <clientid>, 7, true, 62, 640, 1, null, false, 1, 0, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment, capture_type) VALUES ( <clientid>, 8, true, 62, 640, 1, null, false, 1, 0, 1);


--//**********client.additionalproperty_tbl************//
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( '3DVERIFICATION', 'false', true, <merchant-id>, 'merchant', 2);

//********END OF First-Data*******************//