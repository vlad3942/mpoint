/*======= ADD NEW PROCESSOR TYPE FOR TOKENIZATION SYSTEM ======== */
INSERT INTO system.processortype_tbl (id, name) VALUES (8, 'Tokenize');
/*======= END NEW PROCESSOR TYPE FOR TOKENIZATION SYSTEM ======== */


/* ========== CONFIGURE UATP START FOR SOUTHWEST========== */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (49, 'UATP',2);
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (15, 49); /*With Apple-Pay*/
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, psp_type) VALUES (10007, 15, 49, 200, 8);/*With Apple-Pay*/
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 49, 'uatp_cellpoint', 'uatp_cellpointcrdacc', 'hUprlx_6');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 49, '-1');