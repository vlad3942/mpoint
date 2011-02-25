-- URL for My Account Icon
ALTER TABLE Log.Transaction_Tbl ADD iconurl VARCHAR(255);
ALTER TABLE Client.Client_Tbl ADD iconurl VARCHAR(255);

-- Change in behaviour for Store Card
ALTER TABLE Client.Client_Tbl DROP CONSTRAINT storecard_chk;
UPDATE Client.Client_Tbl SET store_card = 2 WHERE store_card = 1;
ALTER TABLE Client.Client_Tbl ADD CONSTRAINT storecard_chk CHECK (store_card = 0 OR (store_card >= 2 AND store_card <= 3) );
UPDATE Client.Client_Tbl SET store_card = 3 WHERE id = 10005;

/* ==================== ADMIN SCHEMA START ==================== */
CREATE SCHEMA Admin AUTHORIZATION jona;

GRANT USAGE ON SCHEMA Admin TO mpoint;

-- Table: Admin.User_Tbl
-- Data table for all mPoint Administrators
CREATE TABLE Admin.User_Tbl
(
	id			SERIAL,
	countryid	INT4 NOT NULL,	-- ID of the country the account is valid in

	firstname	VARCHAR(50),
	lastname	VARCHAR(50),

	mobile		VARCHAR(15),	-- MSISDN which may be used to contact the Administrator
	email		VARCHAR(50),	-- E-Mail address which may be used contact the Administrator
	
	username	VARCHAR(50),
	passwd		VARCHAR(50),

	CONSTRAINT User_PK PRIMARY KEY (id),
	CONSTRAINT User2Country_FK FOREIGN KEY (countryid) REFERENCES System.Country_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE UNIQUE INDEX User_Mobile_UQ ON Admin.User_Tbl (countryid, mobile);
CREATE UNIQUE INDEX User_EMail_UQ ON Admin.User_Tbl (countryid, Upper(email) );
CREATE UNIQUE INDEX User_Username_UQ ON Admin.User_Tbl (username);

CREATE TRIGGER Update_User
BEFORE UPDATE
ON Admin.User_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

-- Internal
INSERT INTO Admin.User_Tbl (id, countryid, firstname, enabled) VALUES (0, 0, 'System Record', false);

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE Admin.User_Tbl TO mpoint;
GRANT SELECT, UPDATE, INSERT ON TABLE Admin.User_Tbl_id_seq TO mpoint;

-- Table: Admin.User_Tbl
-- Data table for all mPoint Administrators specifying which clients they have access to
CREATE TABLE Admin.Access_Tbl
(
	id			SERIAL,
	userid		INT4 NOT NULL,
	clientid	INT4 NOT NULL,

	CONSTRAINT Access_PK PRIMARY KEY (id),
	CONSTRAINT Access2User_FK FOREIGN KEY (userid) REFERENCES Admin.User_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT Access2Client_FK FOREIGN KEY (clientid) REFERENCES Client.Client_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT Access_UQ UNIQUE (userid, clientid),
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE TRIGGER Update_Access
BEFORE UPDATE
ON Admin.Access_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

-- Internal
INSERT INTO Admin.Access_Tbl (id, userid, clientid, enabled) VALUES (0, 0, 0, false);

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE Admin.Access_Tbl TO mpoint;
GRANT SELECT, UPDATE, INSERT ON TABLE Admin.Access_Tbl_id_seq TO mpoint;
/* ==================== ADMIN SCHEMA END ==================== */