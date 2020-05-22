
/* ========== CONFIGURE RMFSS START========== */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (65, 'CEBU-RMFSS',9);
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (<cardid>, 65);

INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, enabled, username, passwd,) VALUES (<clientid>, 65, 'CEBU-RMFSS', true, 'By9AjPV6j14jgb3DXRIpW0mInOfMEafS', 'E9NBawrSH6UAtw1v');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<accountid>, 65, '-1');

INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (840,65,'USD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (608,65,'PHP');

/*=================== Create a new static route for Fraud check : START =======================*/
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, psp_type, stateid) VALUES (<clientid>, <cardid>, true, 65, 200, 9, 1);
/*=================== Create a new static route for Fraud check : END =======================*/

