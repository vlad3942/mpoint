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

/* ==================== ENDUSER SCHEMA START ==================== */
ALTER TABLE EndUser.Account_Tbl ADD mobile_verified BOOL DEFAULT false;
ALTER TABLE EndUser.Account_Tbl ADD externalid VARCHAR(50);
ALTER TABLE EndUser.Transaction_Tbl ADD message TEXT;
ALTER TABLE EndUser.Transaction_Tbl ADD stateid INTEGER DEFAULT 1800;
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (1800, 'Transaction Completed', 'Wallet', '');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (1808, 'Transfer Pending', 'Transfer', 'makeTransfer');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (1809, 'Transfer Cancelled', 'Transfer', 'cancelTransfer');
ALTER TABLE EndUser.Transaction_Tbl ADD CONSTRAINT Transaction2State_FK FOREIGN KEY (stateid) REFERENCES Log.State_Tbl ON UPDATE CASCADE ON DELETE RESTRICT;
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
/* ==================== CLIENT SCHEMA END ==================== */
