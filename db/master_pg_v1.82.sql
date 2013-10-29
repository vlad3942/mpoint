-- Table: Log.Note_Tbl
-- Data table for all notes related to a transaction
CREATE TABLE Log.Note_Tbl
(
	id			SERIAL,
	txnid		INT4 NOT NULL,
	userid		INT4 NOT NULL,
	
	message		TEXT,				-- 
	
	CONSTRAINT Note_PK PRIMARY KEY (id),
	CONSTRAINT Note2Transaction_FK FOREIGN KEY (txnid) REFERENCES enduser.transaction_tbl (id) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT Note2User_FK FOREIGN KEY (userid) REFERENCES admin.user_tbl (id) ON UPDATE CASCADE ON DELETE RESTRICT,
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE TRIGGER Update_Note
BEFORE UPDATE
ON Log.Note_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE Log.Note_Tbl TO mpoint;
GRANT SELECT, UPDATE, USAGE ON TABLE Log.Note_Tbl_id_seq TO mpoint;



-- Table: Admin.Role_Tbl
-- Data table for the available Roles
CREATE TABLE Admin.Role_Tbl
(
	id			SERIAL,
	
	name		VARCHAR(100),		-- Name of Role
	assignable	BOOL DEFAULT true,	-- Flag indicating whether users may be assigned to this role using the Web Interface
	note		TEXT,				-- Description of Role
	
	CONSTRAINT Role_PK PRIMARY KEY (id),
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE UNIQUE INDEX Role_UQ ON Admin.Role_Tbl (Lower(name) );

CREATE TRIGGER Update_Role
BEFORE UPDATE
ON Admin.Role_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE Admin.Role_Tbl TO mpoint;
GRANT SELECT, UPDATE, USAGE ON TABLE Admin.Role_Tbl_id_seq TO mpoint;


-- Table: Admin.RoleInfo_Tbl
-- Data table for the available User RoleInfos
CREATE TABLE Admin.RoleInfo_Tbl
(
	id			SERIAL,
	roleid		INT4 NOT NULL,
	languageid	INT4 NOT NULL,
	
	name		VARCHAR(100),		-- Translated name for the role
	note		TEXT,				-- Translated description for the role
	
	CONSTRAINT RoleInfo_PK PRIMARY KEY (id),
	CONSTRAINT RoleInfo_UQ UNIQUE (roleid, languageid),
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE TRIGGER Update_RoleInfo
BEFORE UPDATE
ON Admin.RoleInfo_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE Admin.RoleInfo_Tbl TO mpoint;
GRANT SELECT, UPDATE, USAGE ON TABLE Admin.RoleInfo_Tbl_id_seq TO mpoint;


-- Table: Admin.Role_Tbl
-- Link table for assigning a user to one or more roles
CREATE TABLE Admin.RoleAccess_Tbl
(
	id			SERIAL,
	roleid		INT4 NOT NULL,
	userid		INT4 NOT NULL,
		
	CONSTRAINT RoleAccess_PK PRIMARY KEY (id),
	CONSTRAINT RoleAccess2Role_FK FOREIGN KEY (roleid) REFERENCES Admin.Role_Tbl (id) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT RoleAccess2User_FK FOREIGN KEY (userid) REFERENCES Admin.User_Tbl (id) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT RoleAccess_UQ UNIQUE (roleid, userid),
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE TRIGGER Update_RoleAccess
BEFORE UPDATE
ON Admin.RoleAccess_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE Admin.RoleAccess_Tbl TO mpoint;
GRANT SELECT, UPDATE, USAGE ON TABLE Admin.RoleAccess_Tbl_id_seq TO mpoint;