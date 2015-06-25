CONNECT client_ownr;
ALTER TABLE client_OWNR.client_tbl
ADD( transaction_ttl Number(10) DEFAULT 0);
COMMENT ON COLUMN client_OWNR.client_tbl.transaction_ttl
IS 'Transaction Time To Live in seconds';

CONNECT Client_OWNR;
INSERT INTO Client_OWNR.InfoType_Tbl (id, name, note) VALUES (1, 'PSP Message', 'A message which is shown during payment through a specific Payment Service Provider');

CONNECT system_ownr;
UPDATE SYSTEM_OWNR.PSPCURRENCY_TBL set name = 'USD' where id = 642;
INSERT INTO System_OWNR.CardState_Tbl (id, name) VALUES (1, 'Enabled');
INSERT INTO System_OWNR.CardState_Tbl (id, name) VALUES (2, 'Disabled By Merchant');
INSERT INTO System_OWNR.CardState_Tbl (id, name) VALUES (3, 'Disabled By PSP');
INSERT INTO System_OWNR.CardState_Tbl (id, name) VALUES (4, 'Prerequisite not Met');
INSERT INTO System_OWNR.CardState_Tbl (id, name) VALUES (5, 'Temporarily Unavailable');

INSERT INTO System_OWNR.IINAction_Tbl (id, name, note) VALUES (1, 'Blocked', 'Used for blocking cards based on their Issuer Identification Number');
INSERT INTO System_OWNR.IINAction_Tbl (id, name, note) VALUES (2, 'Whitelisted', 'Used for whitelisting cards based on their Issuer Identification Number');

INSERT INTO System_OWNR.CardChargeType_Tbl (id, name) VALUES (0, 'No type Available');
INSERT INTO System_OWNR.CardChargeType_Tbl (id, name) VALUES (1, 'Pre-Paid');
INSERT INTO System_OWNR.CardChargeType_Tbl (id, name) VALUES (2, 'Debit');
INSERT INTO System_OWNR.CardChargeType_Tbl (id, name) VALUES (3, 'Credit');

CONNECT system_ownr;
GRANT REFERENCES, UPDATE ON System_Ownr.CardChargeType_Tbl TO enduser_ownr;
CONNECT enduser_ownr;
ALTER TABLE EndUser_OWNR.Card_Tbl ADD CONSTRAINT Card2CardCharge_FK FOREIGN KEY (chargetypeid) REFERENCES System_OWNR.CardChargeType_Tbl(id) ON DELETE CASCADE;

CONNECT system_ownr;
GRANT REFERENCES, UPDATE ON System_Ownr.CardState_Tbl TO Client_OWNR;
CONNECT Client_OWNR;
ALTER TABLE Client_OWNR.CardAccess_Tbl ADD CONSTRAINT CardAccess2CardState_FK FOREIGN KEY (stateid) REFERENCES System_OWNR.CardState_Tbl(id) ON DELETE CASCADE;

