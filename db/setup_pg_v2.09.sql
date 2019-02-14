--Setup this additional property if 3DS is to be requested with every request to Adyen, the rules configured in Adyen will override
--Do not add this if 3DS is not required
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'MANUALTHREED', 'true', id, 'merchant' from client.merchantaccount_tbl where clientid=<clientid> and pspid=12;

--Setup this additional property if 3DS is to be requested to Adyen based on dynamic rules configured.
--Do not add this if 3DS is not required
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'DYNAMICTHREED', 'true', id, 'merchant' from client.merchantaccount_tbl where clientid=<clientid> and pspid=12;

--  CMP-2810 Add Paypal STC related credentials to additional properties table linked to merchant config --
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type )
    SELECT 'PAYPAL_STC', 'true', id, 'merchant' FROM client.merchantaccount_tbl WHERE pspid=24;
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type )
    SELECT 'PAYPAL_REST_ACC_ID', '897383MMQSC9W', id, 'merchant' FROM client.merchantaccount_tbl WHERE pspid=24;
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type )
    SELECT 'PAYPAL_REST_CLIENT_ID', 'AejFqzw9vADty0xlc9oAgI0Rz0LQXYaoZyGPo0rlNiMx7taGI5C1VxqrGpT9zVjg1LMiPwfzkftO0W3U', id, 'merchant' FROM client.merchantaccount_tbl WHERE pspid=24;
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type )
    SELECT 'PAYPAL_REST_SECRET', 'EEmWU-1Bcmfuhe0xheaAlrArpEx2uzrBcB-HVkm125max3hgtVJc4d26bWe0TuDmks-kOl7WlqoRn4-G', id, 'merchant' FROM client.merchantaccount_tbl WHERE pspid=24;
-- CMP-2810 --



/* ========== Consolidated script for CONFIGURING GOOGLE PAY - START ========== */
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (41, 'Google Pay', 19, -1, -1, -1,3);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (41, -1, -1);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 41, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 840;


INSERT INTO System.PSP_Tbl (id, name, system_type) VALUES (44, 'Google Pay',3);
INSERT INTO System.PSPCurrency_Tbl (pspid, name,currencyid) VALUES (44,'USD',840);

-- Enable Google Pay Wallet for Google Pay PSP
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (44, 41);

--Note: public need not be added to merchant account tbl.
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (<clientid>, 44, '<merchantid provided by google to merchant>', NULL, NULL);
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<account id>, 44, 'Google Pay');

--static route
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid, psp_type) VALUES (<clientid>, 41, <pspid>, <countryid>,1);
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (<pspid>, 41);

--Enable WireCard for GPay and USD - Sample
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid, psp_type) VALUES (<clientid>, 41, 18,200,1);
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (18, 41);

/* ========== CONFIGURATION FOR GOOGLE PAY - END ========== */