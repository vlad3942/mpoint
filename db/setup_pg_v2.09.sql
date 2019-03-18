--Setup this additional property if 3DS is to be requested with every request to Adyen, the rules configured in Adyen will override
--Do not add this if 3DS is not required
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'MANUALTHREED', 'true', id, 'merchant' from client.merchantaccount_tbl where clientid=<clientid> and pspid=12;

--Setup this additional property if 3DS is to be requested to Adyen based on dynamic rules configured.
--Do not add this if 3DS is not required
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'DYNAMICTHREED', 'true', id, 'merchant' from client.merchantaccount_tbl where clientid=<clientid> and pspid=12;

--enable Offline Installment option for a PSP - Adyen
UPDATE system.psp_tbl SET installment = 1 WHERE id = 12;

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

-- PAYU Integration --
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (53, 'PayU',1);
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (53, 1);	-- American Express
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (53, 3);	-- Diners Club
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (53, 7);	-- MasterCard
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (53, 8);	-- VISA

--Add currency support as required for client
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (840,53,'USD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (986,53,'BRL');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (32,53,'ARS');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (152,53,'CLP');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (170,53,'COP');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (604,53,'PEN');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (484,53,'MXN');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (604,53,'PEN');

-- Merchant MID configuration --
--Sandbox env details.
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 53, 'PayU LATAM', '', '');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 53, '-1');
--enable Offline Installment option for a PSP - payu if applicable for the client and route.
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment) VALUES (10007, 8, true, 53, 403, 1, null, false, 1,1);

-- Additional properties for API credentials per currency
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'app_id.BRL', 'com.cellpointmobile.cellpointdev', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10007 AND pspid=53;

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'private_key.BRL', '2c96253a-14e8-4e2f-817e-4ca7775ed08e', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10007 AND pspid=53;

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'public_key.BRL', '4fff7dd4-3ee1-4295-8c2e-cc35deaacec6', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10007 AND pspid=53;


--production sql
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (<clientid>, 53, <merchant name>, '', '');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<accountid>, 53, '-1');
--edit if installment is to be enabled for specific SR, 0 means no installment option. 1 means installment is enabled.
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment) VALUES (<clientid>, <cardid>, true, 53, <countryid>, 1, null, false, 1,1);
-- Additional properties for API credentials per currency
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'app_id.BRL', <app-id from payu portal>, id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=53;

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'private_key.BRL', <privatekey from pay portal>, id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=53;

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'public_key.BRL', <public key from payu portal>, id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<clientid> AND pspid=53;

--End PayU Integration --