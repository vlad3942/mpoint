-- Hpp Iframe flag
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('isEmbeddedHpp', 'true', <clientid>, 'client', true);

/*======= ADD NEW PROCESSOR TYPE FOR TOKENIZATION SYSTEM ======== */
INSERT INTO system.processortype_tbl (id, name) VALUES (9, 'Fraud Gateway');
/*======= END NEW PROCESSOR TYPE FOR TOKENIZATION SYSTEM ======== */

/* ========== CONFIGURE EZY START========== */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (58, 'EZY Fraud Gateway',9);
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (15, 58); /*With Apple-Pay*/

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name) VALUES (10007, 58, 'EZY');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 58, '-1');

INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (208,58,'DKK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (840,58,'USD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (826,58,'GBP');

/*=================== Adding new states for Fraud Check : START =======================*/
INSERT INTO log.state_tbl (id, name, module, enabled) VALUES (2040 , 'Fraud Check Passed', 'Authorization', true);
INSERT INTO log.state_tbl (id, name, module, enabled) VALUES (2041 , 'Fraud Check Failed', 'Authorization', true);
/*=================== Adding new states for Fraud Check : END =======================*/


/*=================== Create a new static route for Fraud check : START =======================*/
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, psp_type, stateid) VALUES (10007, 15, true, 58, 200, 9, 1);
/*=================== Create a new static route for Fraud check : END =======================*/

