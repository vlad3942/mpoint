/**
 * Master SQL script for the PostGreSQL databse.
 * The file include general functions and trigger functions useful in most types of databases
 */

CREATE OR REPLACE FUNCTION Nextvalue(varchar) RETURNS integer LANGUAGE plpgsql
    AS $BODY$
DECLARE
	-- Declare aliases for input
	sequence ALIAS FOR $1;
	num INT4;
BEGIN
	EXECUTE 'SELECT Nextval('''|| sequence || ''')' INTO num;
	
	RETURN num;
END;
$BODY$;

CREATE OR REPLACE VIEW Public.DUAL AS SELECT E'Provides compatibility with Oracle when selecting from functions.\nUse "SELECT [FUNCTION] FROM DUAL" rather than "SELECT [FUNCTION]"';

GRANT SELECT ON TABLE Public.DUAL TO mpoint;

/* ==================== LOG SCHEMA START ==================== */
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (1800, 'Transaction Completed', 'Wallet', '');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (1808, 'Transfer Pending', 'Transfer', 'makeTransfer');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (1809, 'Transfer Cancelled', 'Transfer', 'cancelTransfer');

ALTER TABLE Log.Transaction_Tbl ADD authurl VARCHAR(255);		-- URL where the customer may be authenticated.
ALTER TABLE Log.Transaction_Tbl ADD customer_ref VARCHAR(50);	-- The Client's Reference for the Customer
/* ==================== LOG SCHEMA END ==================== */

/* ==================== ENDUSER SCHEMA START ==================== */
ALTER TABLE EndUser.Account_Tbl ADD mobile_verified BOOL DEFAULT false;
ALTER TABLE EndUser.Account_Tbl ADD externalid VARCHAR(50);
ALTER TABLE EndUser.Transaction_Tbl ADD message TEXT;
ALTER TABLE EndUser.Transaction_Tbl ADD stateid INTEGER DEFAULT 1800;
ALTER TABLE EndUser.Transaction_Tbl ADD CONSTRAINT Transaction2State_FK FOREIGN KEY (stateid) REFERENCES Log.State_Tbl ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE EndUser.Card_Tbl RENAME ticket TO ticket_old;
ALTER TABLE EndUser.Card_Tbl ADD ticket VARCHAR(255);
UPDATE EndUser.Card_Tbl SET ticket = ticket_old;
ALTER TABLE EndUser.Card_Tbl DROP ticket_old;

DROP TRIGGER Modify_Transaction ON EndUser.Transaction_Tbl;
DROP FUNCTION Modify_EndUserTxn_Proc();
-- Trigger function for modifying the Transaction table.
-- The balance of either the End User's e-money account or the End-User's loyalty account
-- will be recalculated and stored in the "balance" or "points" field of EndUser.Account_Tbl
CREATE OR REPLACE FUNCTION Modify_EndUserTxn_Proc() RETURNS opaque AS
$BODY$
DECLARE
	iAccountID INT4;
	iTypeID INT4;
BEGIN
	IF TG_OP = 'DELETE' THEN
		iAccountID := OLD.accountid;
		iTypeID := OLD.typeid;
	ELSE
		iAccountID := NEW.accountid;
		iTypeID := NEW.typeid;
	END IF;
	
	-- Update available balance on EndUser's e-Money based account
	IF 1000 <= iTypeID AND iTypeID <= 1003 THEN
		UPDATE EndUser.Account_Tbl
		SET balance = (SELECT (Sum(amount) + Sum(Abs(fee) * -1) )
					   FROM EndUser.Transaction_Tbl
					   WHERE accountid = iAccountID AND 1000 <= typeid AND typeid <= 1003 AND enabled = true AND stateid != 1809)
		WHERE id = iAccountID;
	-- Update available balance on EndUser's loyalty account
	ELSIF 1004 <= iTypeID AND iTypeID <= 1007 THEN
		UPDATE EndUser.Account_Tbl
		SET points = (SELECT (Sum(amount) + Sum(Abs(fee) * -1) )
					   FROM EndUser.Transaction_Tbl
					   WHERE accountid = iAccountID AND 1004 <= typeid AND typeid <= 1007 AND enabled = true AND stateid != 1809)
		WHERE id = iAccountID;
	END IF;
	
	IF TG_OP = 'DELETE' THEN
		RETURN OLD;
	ELSE
		NEW.Modified := NOW();
		RETURN NEW;
	END IF;
END;
$BODY$
LANGUAGE 'plpgsql';

CREATE TRIGGER Modify_Transaction
AFTER INSERT OR UPDATE OR DELETE
ON EndUser.Transaction_Tbl FOR EACH ROW
EXECUTE PROCEDURE Modify_EndUserTxn_Proc();
/* ==================== ENDUSER SCHEMA END ==================== */

/* ==================== SYSTEM SCHEMA START ==================== */
-- Table: System.URLType_Tbl
-- Data table for all URL Types that may be used by mRetail to contact external systems
CREATE TABLE System.URLType_Tbl
(
	id			SERIAL,

	name		VARCHAR(50),	-- Name of the URL Type

	CONSTRAINT URLType_PK PRIMARY KEY (id),
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE UNIQUE INDEX URLType_UQ ON System.URLType_Tbl (Lower(name) );

CREATE TRIGGER Update_URLType
BEFORE UPDATE
ON System.URLType_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE System.URLType_Tbl TO mpoint;
GRANT SELECT, UPDATE, USAGE ON TABLE System.URLType_Tbl_id_seq TO mpoint;
/* ==================== SYSTEM SCHEMA END ==================== */

/* ==================== CLIENT SCHEMA START ==================== */
-- Table: Client.URL_Tbl
-- Data table for all URLs that mRetail may use to contact external systems on the Client's behalf
CREATE TABLE Client.URL_Tbl
(
	id			SERIAL,
	urltypeid	INT4 NOT NULL,	-- ID of the URL Type
	clientid	INT4 NOT NULL,	-- ID of the Client who owns the URL

	url			VARCHAR(255),

	CONSTRAINT URL_PK PRIMARY KEY (id),
	CONSTRAINT URL2URLType_FK FOREIGN KEY (urltypeid) REFERENCES System.URLType_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT URL2Client_FK FOREIGN KEY (clientid) REFERENCES Client.Client_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT URL_UQ UNIQUE (urltypeid, clientid),
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE UNIQUE INDEX Client_URL_UQ ON Client.URL_Tbl (clientid, Lower(url) );

CREATE TRIGGER Update_URL
BEFORE UPDATE
ON Client.URL_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE Client.URL_Tbl TO mpoint;
GRANT SELECT, UPDATE, USAGE ON TABLE Client.URL_Tbl_id_seq TO mpoint;


ALTER TABLE Client.MerchantAccount_Tbl ADD stored_card BOOL DEFAULT NULL;
ALTER TABLE Client.MerchantAccount_Tbl DROP CONSTRAINT MerchantAccount_UQ;
CREATE UNIQUE INDEX MerchantAccount_UQ ON Client.MerchantAccount_Tbl (clientid, pspid) WHERE stored_card IS NULL;
CREATE UNIQUE INDEX MerchantAccount_StoredCard_UQ ON Client.MerchantAccount_Tbl (clientid, pspid, stored_card);
/* ==================== CLIENT SCHEMA END ==================== */

CREATE INDEX CONCURRENTLY Transaction_Order_Idx ON Log.Transaction_Tbl (clientid, orderid);