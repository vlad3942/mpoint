/* ==================== LOG SCHEMA START ==================== */
CREATE INDEX transaction_search_mobile_idx ON Log_Ownr.Transaction_Tbl (clientid, mobile, created);
CREATE INDEX transaction_search_email_idx ON Log_Ownr.Transaction_Tbl (clientid, email, created);
CREATE INDEX transaction_search_custref_idx ON Log_Ownr.Transaction_Tbl (clientid, customer_ref, created);
CREATE INDEX transaction_search_order_idx ON Log_Ownr.Transaction_Tbl (clientid, orderid, created);
/* ==================== LOG SCHEMA END ==================== */