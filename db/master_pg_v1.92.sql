/* ==================== Client SCHEMA START ==================== */
ALTER TABLE Client.CardAccess_tbl ADD position integer default NULL;
/* ==================== Client SCHEMA END ==================== */

CREATE INDEX CONCURRENTLY externalid_transaction_idx ON Log.Transaction_Tbl (extid, pspid);
