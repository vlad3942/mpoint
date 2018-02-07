/*  ===========  START : Adding New Processor Type  ==================  */
INSERT INTO system.processortype_tbl (id, name) VALUES (6, 'Merchant Plug-in');
/*  ===========  END : Adding New Processor Type  ==================  */

INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (42, 'NETS MPI',6);
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (840,42,'USD');
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 42, 'NETS MPI', '', '');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 42, '-1');
INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position) VALUES (10007, 8, true, 42, 200, 1, null);