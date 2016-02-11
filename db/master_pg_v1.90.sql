/* ==================== LOG SCHEMA START ==================== */
CREATE INDEX CONCURRENTLY transaction_search_mobile_idx ON Log.Transaction_Tbl (clientid, mobile, created);
CREATE INDEX CONCURRENTLY transaction_search_email_idx ON Log.Transaction_Tbl (clientid, email, created);
CREATE INDEX CONCURRENTLY transaction_search_customer_ref_idx ON Log.Transaction_Tbl (clientid, customer_ref, created);
CREATE INDEX CONCURRENTLY transaction_search_order_idx ON Log.Transaction_Tbl (clientid, orderid, created);
/* ==================== LOG SCHEMA END ==================== */

/* ==================== LOG CLIENT START ==================== */
ALTER TABLE Client.CardAccess_tbl DROP CONSTRAINT cardaccess_uq;

CREATE UNIQUE INDEX CONCURRENTLY cardaccess_uq ON Client.CardAccess_tbl (clientid, cardid, pspid) WHERE countryid IS NULL;
CREATE UNIQUE INDEX CONCURRENTLY cardaccess_country_uq ON Client.CardAccess_tbl (clientid, cardid, pspid, countryid) WHERE countryid IS NOT NULL;
/* ==================== LOG CLIENT END ==================== */

/* ==================== LOG CLIENT START CMC-1786 ==================== */
DROP INDEX CONCURRENTLY Client.cardaccess_uq;
DROP INDEX CONCURRENTLY Client.Clientcardaccess_country_uq;
CREATE UNIQUE INDEX CONCURRENTLY cardaccess_uq ON Client.CardAccess_tbl (clientid, cardid) WHERE countryid IS NULL;
CREATE UNIQUE INDEX CONCURRENTLY cardaccess_card_country_uq ON Client.CardAccess_tbl (clientid, cardid, countryid) WHERE countryid IS NOT NULL;