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
	clientid	INT4 NOT NULL,	-- ID of the Client who owns the Info
	pspid		INT4,
	language	CHAR(2) DEFAULT 'gb',

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
/* ==================== CLIENT SCHEMA END ==================== */