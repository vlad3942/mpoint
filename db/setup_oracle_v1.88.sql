ALTER TABLE client_OWNR.client_tbl
ADD( transaction_ttl Number(10) DEFAULT 0);
COMMENT ON COLUMN client_OWNR.client_tbl.transaction_ttl
IS 'Transaction Time To Live in seconds';

INSERT INTO Client_OWNR.InfoType_Tbl (id, name, note) VALUES (1, 'PSP Message', 'A message which is shown during payment through a specific Payment Service Provider');

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
ALTER TABLE EndUser_OWNR.Card_Tbl ADD CONSTRAINT Card2CardCharge_FK FOREIGN KEY (charge_typeid) REFERENCES System.CardChargeType_Tbl ON UPDATE CASCADE ON DELETE RESTRICT;

UPDATE SYSTEM_OWNR.PSPCURRENCY_TBL set name = 'USD' where id = 642;

ALTER TABLE Client_OWNR.CardAccess_Tbl ADD CONSTRAINT CardAccess2CardState_FK FOREIGN KEY (stateid) REFERENCES System_OWNR.CardState_Tbl ON UPDATE CASCADE ON DELETE RESTRICT;

