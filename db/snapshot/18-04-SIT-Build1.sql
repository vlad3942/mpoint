/* ========== CONFIGURE PPRO START ========== */

/* START: Adding CARD Configuration Entries */

INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (42, 'PPRO', 23, -1, -1, -1,4);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (42, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 42, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 840;


/* END: Adding CARD Configuration Entries */

/*START: Adding PSP entries to the PSP_Tbl table for PPRO */

INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (46, 'PPRO',4);

/*END: Adding PSP entries to the PSP_Tbl table for PPRO*/

/*START: Adding Currency entries to the PSPCurrency_Tbl table for PPRO*/

INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (840,46,'DKK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (458,46,'MYR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (978,46,'EUR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (608,46,'PHP');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (702,46,'SGD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (752,46,'SEK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (764,46,'THB');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (985,46,'PLN');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (203,46,'CZK');
/*END: Adding Currency entries to the PSPCurrency_Tbl table for PPRO*/

/* ========== CONFIGURE DEMO ACCOUNT FOR PPRO START ========== */
-- Wire-Card
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 46, 'CELLPOINTMOBILETESTCONTRACT', 'CELLPOINTTEST', '8eX67I13el8Q3LBF');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 46, '-1');

/* START : Additional Properties */

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) SELECT 'ppro-shared-secret', '', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10007 AND pspid=46 ;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) SELECT 'ppro-notification-secret', '', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10007 AND pspid=46 ;

/* END : Additional Properties */



/* START: Adding CARD Configuration Entries FOR testing purpose only*/


INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (43, 'Affin Bank', 23, -1, -1, -1, 4);
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (44, 'Ambank', 23, -1, -1, -1, 4);
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (45, 'Bancontact', 23, -1, -1, -1, 4);
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (46, 'CIMB Clicks', 23, -1, -1, -1, 4);
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (47, 'Dragonpay', 23, -1, -1, -1, 4);
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (48, 'eNETS', 23, -1, -1, -1, 4);
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (49, 'Entercash', 23, -1, -1, -1, 4);
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (50, 'EPS', 23, -1, -1, -1, 4);
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (51, 'Estonian Banks', 23, -1, -1, -1, 4);
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (52, 'giropay', 23, -1, -1, -1, 4);
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (53, 'Ideal', 23, -1, -1, -1, 4);
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (54, 'Latvian Banks', 23, -1, -1, -1, 4);
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (55, 'Lithuanian Banks', 23, -1, -1, -1, 4);
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (56, 'Maybank2u', 23, -1, -1, -1, 4);
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (57, 'Multibanco', 23, -1, -1, -1, 4);
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (58, 'MyClear FPX', 23, -1, -1, -1, 4);
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (59, 'Paysbuy', 23, -1, -1, -1, 4);
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (60, 'PayU', 23, -1, -1, -1, 4);
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (61, 'Przelewy24 ', 23, -1, -1, -1, 4);
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (62, 'RHB Bank', 23, -1, -1, -1, 4);
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (63, 'Safetypay', 23, -1, -1, -1, 4);
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (64, 'SEPA', 23, -1, -1, -1, 4);
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (65, 'Singpost', 23, -1, -1, -1, 4);
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (66, 'SOFORT banking', 23, -1, -1, -1, 4);
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (67, 'UnionPay', 23, -1, -1, -1, 4);
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (68, 'Verkkopankki - Finnish Online Banking', 23, -1, -1, -1, 4);

INSERT INTO system.cardprefix_tbl (cardid, min, max) VALUES (43, 0, 0);
INSERT INTO system.cardprefix_tbl (cardid, min, max) VALUES (44, 0, 0);
INSERT INTO system.cardprefix_tbl (cardid, min, max) VALUES (45, 0, 0);
INSERT INTO system.cardprefix_tbl (cardid, min, max) VALUES (46, 0, 0);
INSERT INTO system.cardprefix_tbl (cardid, min, max) VALUES (47, 0, 0);
INSERT INTO system.cardprefix_tbl (cardid, min, max) VALUES (48, 0, 0);
INSERT INTO system.cardprefix_tbl (cardid, min, max) VALUES (49, 0, 0);
INSERT INTO system.cardprefix_tbl (cardid, min, max) VALUES (50, 0, 0);
INSERT INTO system.cardprefix_tbl (cardid, min, max) VALUES (51, 0, 0);
INSERT INTO system.cardprefix_tbl (cardid, min, max) VALUES (52, 0, 0);
INSERT INTO system.cardprefix_tbl (cardid, min, max) VALUES (53, 0, 0);
INSERT INTO system.cardprefix_tbl (cardid, min, max) VALUES (54, 0, 0);
INSERT INTO system.cardprefix_tbl (cardid, min, max) VALUES (55, 0, 0);
INSERT INTO system.cardprefix_tbl (cardid, min, max) VALUES (56, 0, 0);
INSERT INTO system.cardprefix_tbl (cardid, min, max) VALUES (57, 0, 0);
INSERT INTO system.cardprefix_tbl (cardid, min, max) VALUES (58, 0, 0);
INSERT INTO system.cardprefix_tbl (cardid, min, max) VALUES (59, 0, 0);
INSERT INTO system.cardprefix_tbl (cardid, min, max) VALUES (60, 0, 0);
INSERT INTO system.cardprefix_tbl (cardid, min, max) VALUES (61, 0, 0);
INSERT INTO system.cardprefix_tbl (cardid, min, max) VALUES (62, 0, 0);
INSERT INTO system.cardprefix_tbl (cardid, min, max) VALUES (63, 0, 0);
INSERT INTO system.cardprefix_tbl (cardid, min, max) VALUES (64, 0, 0);
INSERT INTO system.cardprefix_tbl (cardid, min, max) VALUES (65, 0, 0);
INSERT INTO system.cardprefix_tbl (cardid, min, max) VALUES (66, 0, 0);
INSERT INTO system.cardprefix_tbl (cardid, min, max) VALUES (67, 0, 0);
INSERT INTO system.cardprefix_tbl (cardid, min, max) VALUES (68, 0, 0);


INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 43, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 458;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 44, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 458;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 45, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 978;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 46, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 458;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 47, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 608;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 48, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 702;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 49, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 978;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 50, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 752;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 51, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 978;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 52, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 978;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 53, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 978;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 54, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 978;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 55, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 978;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 56, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 458;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 57, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 978;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 58, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 458;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 59, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 764;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 60, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 985;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 60, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 203;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 61, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 985;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 61, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 978;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 62, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 458;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 63, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 840;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 63, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 978;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 64, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 978;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 65, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 702;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 66, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 978;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 67, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 840;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 68, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 978;


/* END: Adding CARD Configuration Entries */

/* ========== CONFIGURE DEMO ACCOUNT FOR PPRO END ====== */