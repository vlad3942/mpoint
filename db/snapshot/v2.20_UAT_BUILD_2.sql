-- Update system_type for EGHL FPX 
update system.psp_tbl set system_type = 7 where id = 51;

/* ========== HPP Additional Property for Card Option Display : CPMEH-728 ========== */
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES ('hideCardPaymentOption', 'true', <clientid>, 'client', true, 2);

-- Update system_type for Paytabs 
update system.psp_tbl set system_type = 7 where id = 38;