--//********OMANNET*******************//

--//**********system.card_tbl************//

INSERT INTO system.card_tbl (id, name, logo, enabled, position, minlength, maxlength, cvclength, paymenttype) VALUES (87, 'OMANNET', null, true, 23, -1, -1, -1, 4);

--//**********system.pricepoint_tbl************//
INSERT INTO system.pricepoint_tbl (id, amount, enabled, currencyid) VALUES (-512, -1, true, 512);

--//**********system.cardpricing_tbl************//
INSERT INTO system.cardpricing_tbl (pricepointid, cardid, enabled) VALUES (-512, 87, true);


--//**********system.psp_tbl************//
INSERT INTO system.psp_tbl (id, name, enabled, system_type, installment) VALUES (38, 'PayTabs', true, 1, 0);

--//**********system.pspcard_tbl************//
INSERT INTO system.pspcard_tbl (cardid, pspid,enabled) VALUES (87, 38, true);

--//**********system.pspcurrency_tbl************//
INSERT INTO system.pspcurrency_tbl (pspid, name, enabled, currencyid) VALUES (38, 'OMR', true, 512);

--//**********client.merchantaccount_tbl************//
INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, enabled, username, passwd, stored_card) VALUES (<clientid>, 38, 'PayTabs', true, 'arun123', 'Sunrise@123', null);

--//**********client.merchantsubaccount_tbl************//
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (<accountid>, 38, '-1', true);

--//**********client.cardaccess_tbl************//
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment, capture_method) VALUES (<clientid>, 87, true, 38, 605, 1, null, false, 1, 0, 0);


--//**********client.additionalproperty_tbl For SIT/UAT************//
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ('OMANNET_Secret_Key', 'nJkmOlYkWDlV8YQaGbqbZU0FawETEZr2JMox7nS40e08cQiT1I51D5GOClHhl9k5VIC35v76J7eWXxQZgV0U1HIE2qVnzgsuY5x4', true, <merchant-id>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ('OMANNET_MID', '10015651', true, <merchant-id>, 'merchant', 2);

--//**********client.additionalproperty_tbl Place hodlers************//
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ('OMANNET_Secret_Key',<secrete_key>, true, <merchant-id>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ('OMANNET_MID', <MID>, true, <merchant-id>, 'merchant', 2);


--//********END OF OMANNET*******************//