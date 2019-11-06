--//********Cellulant*******************//

--//**********system.card_tbl************//
INSERT INTO system.card_tbl (id, name, logo, enabled, position, minlength, maxlength, cvclength, paymenttype) VALUES (86, 'CELLULANT', null, true, 23, -1, -1, -1, 4);

--//**********system.pricepoint_tbl************//
INSERT INTO system.pricepoint_tbl (id, amount, enabled, currencyid) VALUES (-404, -1, true, 404);

--//**********system.cardpricing_tbl************//
INSERT INTO system.cardpricing_tbl (pricepointid, cardid, enabled) VALUES (-404, 86, true);

--//**********system.psp_tbl************//
INSERT INTO system.psp_tbl (id, name, enabled, system_type) VALUES (58, 'CELLULANT', true, 1);

--//**********system.pspcard_tbl************//
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (86, 58, true);

--//**********system.pspcurrency_tbl************//
INSERT INTO system.pspcurrency_tbl (pspid, name, enabled, currencyid) VALUES (58, 'KES', true, 404);

--//**********client.merchantaccount_tbl************//
INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, enabled, username, passwd, stored_card) VALUES (<clientid>, 58, 'TESDEV6626',  true, '01b58e3e-4c1b-4b73-9f53-b88783a45271', 'SbLujjXRvPxLanySxBhPFyL3e2kTqrgb5lbgR5Ft', null);

--//**********client.merchantsubaccount_tbl************//
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (<accountid>, 58, '-1', true);

--//**********client.cardaccess_tbl************//
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment) VALUES ( <clientid>, 86, true, 58, 325, 1, null, false, 1, 0);

--//**********client.additionalproperty_tbl************//
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'accountNumber', 'ACtest0048', true, <merchant-id>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'serviceCode', 'TESDEV6626', true, <merchant-id>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'accessCode', '$2a$08$rOTOrGCQ9FCeRF0qMOKqMeUd0IOm2RYFapi4GNzvhObA14njGT2F6', true, <merchant-id>, 'merchant', 2);

--//**********system.cardprefix_tbl Bin range************//
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (86,0	,0, true);
//********END OF Cellulant*******************//