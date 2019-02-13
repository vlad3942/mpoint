/* ========== Consolidated script for CONFIGURING GOOGLE PAY - START ========== */
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (41, 'Google Pay', 19, -1, -1, -1,3);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (41, -1, -1);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 41, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 840;


INSERT INTO System.PSP_Tbl (id, name, system_type) VALUES (44, 'Google Pay',3);
INSERT INTO System.PSPCurrency_Tbl (pspid, name,currencyid) VALUES (44,'USD',840);

-- Enable Google Pay Wallet for Google Pay PSP
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (44, 41);

--Note: public need not be added to merchant account tbl.
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (<clientid>, 44, '<merchant name>', NULL, NULL);
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<account id>, 44, 'Google Pay');

--static route
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid, psp_type) VALUES (<clientid>, 41, <pspid>, <countryid>,1);
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (<pspid>, 41);

--Enable WireCard for GPay and USD - Sample
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid, psp_type) VALUES (<clientid>, 41, 18,200,1);
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (18, 41);

/* ========== CONFIGURATION FOR GOOGLE PAY - END ========== */