
-- mPoint DB Scripts :

-- CMP-5675

-- To enable CEBU RMFSS for Cards, I can see Visa and Master are already enabled. We have to enable the Amex and JCB.

--1
update client.cardaccess_tbl set enabled = true, dccenabled = true where clientid = 10077 and cardid = 5 and pspid = 65 and id = 1682;

--2
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10077, 1, true, 65, NULL, 1, NULL, false, 9, 0, 0, 1, NULL, true);

-- Prod S2S Callback URL  - Email
update client.client_tbl set callbackurl = 'https://soar.cebupacificair.com/ceb-payment_center/cpd/payment' where id = 10077;