-- Set HPP bilingDetails validation property Per client
INSERT INTO client.additionalproperty_tbl (k
ey, value, externalid, type, enabled) VALUES ('mandateBillingDetails', true, 10007, 'client', true);

-- Card Prefix for visa and Master --
INSERT INTO "system".cardprefix_tbl ( cardid, min, max) VALUES( 7, 5110, 5210);
INSERT INTO "system".cardprefix_tbl ( cardid, min, max) VALUES( 7, 2700, 2730);
-- END Card Prefix for visa and Master --