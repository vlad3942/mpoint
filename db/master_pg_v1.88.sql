/* ==================== SYSTEM SCHEMA START ==================== */
-- Table: System.CardState_Tbl 
CREATE TABLE System.CardState_Tbl
(
	id			SERIAL,
	name		VARCHAR(100),

	CONSTRAINT CardState_PK PRIMARY KEY (id),
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE TRIGGER Update_Info
BEFORE UPDATE
ON System.CardState_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE System.CardState_Tbl TO mpoint;
GRANT SELECT, UPDATE, USAGE ON TABLE System.CardState_Tbl_id_seq TO mpoint;

-- Create link to Client.CardAccess_Tbl
ALTER TABLE Client.CardAccess_Tbl ADD COLUMN stateid INT4 DEFAULT 1;
ALTER TABLE Client.CardAccess_Tbl ADD CONSTRAINT CardAccess2CardState_FK FOREIGN KEY (stateid) REFERENCES System.CardState_Tbl ON UPDATE CASCADE ON DELETE RESTRICT;


-- Table: System.IINAction_Tbl
-- Definition table for all actions (Block, Whitelist etc.) that may be taken for an Issuer Identification Number.
CREATE TABLE System.IINAction_Tbl
(
	id			INT4,
	
	name		VARCHAR(100),
	note		TEXT,

	CONSTRAINT IINAction_PK PRIMARY KEY (id),
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE UNIQUE INDEX IINAction_UQ ON System.IINAction_Tbl (Lower(name) ); 

CREATE TRIGGER Update_IINAction
BEFORE UPDATE
ON System.IINAction_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE System.IINAction_Tbl TO mpoint;
/* ==================== SYSTEM SCHEMA END ==================== */

/* ==================== CLIENT SCHEMA START ==================== */
-- Table: Client.InfoType_Tbl
-- Definition table for all information types: About Text, Contact Info, Terms & Conditions, Help Text, Reminder Message, OTP Message etc.
CREATE TABLE Client.InfoType_Tbl
(
	id			SERIAL,
	name		VARCHAR(100),
	note		TEXT,

	CONSTRAINT InfoType_PK PRIMARY KEY (id),
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE TRIGGER Update_InfoType
BEFORE UPDATE
ON Client.InfoType_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE Client.InfoType_Tbl TO mpoint;
GRANT SELECT, UPDATE, USAGE ON TABLE Client.InfoType_Tbl_id_seq TO mpoint;

CREATE UNIQUE INDEX InfoType_UQ ON Client.InfoType_Tbl (Lower(name) );

-- Table: Client.Info_Tbl
-- Data table for all Client Information, texts may be in XHTML format
CREATE TABLE Client.Info_Tbl
(
	id			SERIAL,
	infotypeid	INT4 NOT NULL,	-- ID of the Information Type
	clientid		INT4 NOT NULL,	-- ID of the Client who owns the Info
	pspid			INT4,
	language		CHAR(2) DEFAULT 'gb',

	text		TEXT,			-- Information text

	CONSTRAINT Info_PK PRIMARY KEY (id),
	CONSTRAINT Info2InfoType_FK FOREIGN KEY (infotypeid) REFERENCES Client.InfoType_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT Info2Client_FK FOREIGN KEY (clientid) REFERENCES Client.Client_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT Info2PSP_FK FOREIGN KEY (pspid) REFERENCES System.PSP_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE UNIQUE INDEX Info_PSP_UQ ON Client.Info_Tbl (infotypeid, clientid, language, pspid) WHERE pspid IS NOT NULL;
CREATE UNIQUE INDEX Info_UQ ON Client.Info_Tbl (infotypeid, clientid, language) WHERE pspid IS NULL;

CREATE TRIGGER Update_Info
BEFORE UPDATE
ON Client.Info_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE Client.Info_Tbl TO mpoint;
GRANT SELECT, UPDATE, USAGE ON TABLE Client.Info_Tbl_id_seq TO mpoint;


-- Table: Client.IINList_Tbl
-- Data table for each client's lists of actions taken for a range of Issuer Identification Numbers
CREATE TABLE Client.IINList_Tbl
(
	id			SERIAL,
	iinactionid	INT4 NOT NULL,	-- ID of the action to take for the defined range of Issuer Identification Numbers
	clientid	INT4 NOT NULL,	-- ID of the client for which the specified action is defined

	min			INT8,
	max			INT8,

	CONSTRAINT IINList_PK PRIMARY KEY (id),
	CONSTRAINT IINList2Client_FK FOREIGN KEY (clientid) REFERENCES Client.Client_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT IINList2IINAction_FK FOREIGN KEY (iinactionid) REFERENCES System.IINAction_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE TRIGGER Update_IINList
BEFORE UPDATE
ON Client.IINList_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE Client.IINList_Tbl TO mpoint;
GRANT SELECT, UPDATE, INSERT ON TABLE Client.IINList_Tbl_id_seq TO mpoint;

CREATE INDEX IINRanges_Idx ON Client.IINList_Tbl (clientid, min, max);
/* ==================== CLIENT SCHEMA END ==================== */

/* ==================== SYSTEM SCHEMA START ==================== */
-- Table: System.CardChargeType_Tbl 
CREATE TABLE System.CardChargeType_Tbl 
(
	id			SERIAL,
	name		VARCHAR(100),

	CONSTRAINT CardCharge_PK PRIMARY KEY (id),
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE TRIGGER Update_Info
BEFORE UPDATE
ON System.CardChargeType_Tbl  FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE System.CardChargeType_Tbl  TO mpoint;
GRANT SELECT, UPDATE, USAGE ON TABLE System.CardChargeType_Tbl_id_seq TO mpoint;
/* ==================== SYSTEM SCHEMA END ==================== */

-- Create link to EndUser.Card_Tbl
ALTER TABLE EndUser.Card_Tbl ADD COLUMN chargetypeid INT4 DEFAULT 0;

/* ==================== LOG SCHEMA START ==================== */

CREATE INDEX Transaction_Created_Idx ON Log.Transaction_Tbl (created);

/* ==================== LOG SCHEMA END ====================*/
