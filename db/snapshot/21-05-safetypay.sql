--Add all new cards
INSERT INTO system.card_tbl (id, name, logo, enabled, position, minlength, maxlength, cvclength, paymenttype) VALUES (63, 'SafetyPay', null, true, 23, -1, -1, -1, 4);
INSERT INTO system.card_tbl (id, name, logo, enabled, position, minlength, maxlength, cvclength, paymenttype) VALUES (97, 'PSE', null, true, 23, -1, -1, -1, 7);
INSERT INTO system.card_tbl (id, name, logo, enabled, position, minlength, maxlength, cvclength, paymenttype) VALUES (98, 'Boleto', null, true, 23, -1, -1, -1, 8);
INSERT INTO system.card_tbl (id, name, logo, enabled, position, minlength, maxlength, cvclength, paymenttype) VALUES (99, 'Efecty', null, true, 23, -1, -1, -1, 8);
INSERT INTO system.card_tbl (id, name, logo, enabled, position, minlength, maxlength, cvclength, paymenttype) VALUES (100, 'Banco de Bogata', null, true, 23, -1, -1, -1, 8);


/* ========== Global Common Configuration for SWISH = STARTS ========== */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (70, 'SafetyPay',4);

INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (986,70,'BRL');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (170,70,'COP');

INSERT INTO system.cardpricing_tbl (pricepointid, cardid, enabled) VALUES (-986, 70, true);
INSERT INTO system.cardpricing_tbl (pricepointid, cardid, enabled) VALUES (-170, 70, true);

/*
* SafetyPay card options with SafetyPay APM 
*/

INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (63, 70);
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (97, 70);
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (98, 70);
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (99, 70);
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (100, 70);

