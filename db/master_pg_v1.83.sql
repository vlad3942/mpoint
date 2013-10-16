ALTER TABLE Log.Transaction_Tbl ADD COLUMN description TEXT; -- Added´s a description to a transaction 


-- Table: System.State_Tbl
-- Data table for all States in a Country
CREATE TABLE System.State_Tbl
(
	id 			SERIAL NOT NULL,
	countryid 	INT4 NOT NULL,
  	
	name 		VARCHAR(50),
	code 		VARCHAR(5),
	
	vat			FLOAT4 DEFAULT 0.0,	-- VAT charged in the State
	
	CONSTRAINT State_PK PRIMARY KEY (id),
	CONSTRAINT State2Country_FK FOREIGN KEY (countryid) REFERENCES System.Country_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
  	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE UNIQUE INDEX State_UQ ON System.State_Tbl (countryid, Upper(code) );

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE System.State_Tbl TO mpoint;
GRANT SELECT, UPDATE, USAGE ON TABLE System.State_Tbl_id_seq TO mpoint;


-- Table: System.PostalCode_Tbl
-- Data table for all Postal Codes in a State
CREATE TABLE System.PostalCode_Tbl
(
	id 			SERIAL NOT NULL,
	stateid 	INT4 NOT NULL,
	
  	code 		VARCHAR(10),
	city 		VARCHAR(50),
	
	latitude 	FLOAT4,
	longitude 	FLOAT4,
	utc_offset 	INT4,
	
	vat			FLOAT4 DEFAULT 0.0,	-- VAT charged in the Postal Code
	
	CONSTRAINT PostalCode_PK PRIMARY KEY (id),
	CONSTRAINT PostalCode2State_FK FOREIGN KEY (stateid) REFERENCES System.State_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
  	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE UNIQUE INDEX PostalCode_UQ ON System.PostalCode_Tbl (latitude, longitude, code, lower(city) );
  
GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE System.PostalCode_Tbl TO mpoint;
GRANT SELECT, UPDATE, USAGE ON TABLE System.PostalCode_Tbl_id_seq TO mpoint;

-- Table: EndUser.Address_Tbl
-- Data table for all billing addresses registered by end-users
CREATE TABLE EndUser.Address_Tbl
(
	id			SERIAL,
	accountid	INT4,
	cardid		INT4,
	countryid	INT4 NOT NULL,
	stateid		INT4 NOT NULL,
	
	firstname	VARCHAR(50),
	lastname	VARCHAR(50),
	company		VARCHAR(50),
	street		VARCHAR(50),
	postalcode	VARCHAR(10),
	city		VARCHAR(50),
	
	CONSTRAINT Address_PK PRIMARY KEY (id),
	CONSTRAINT Address2Account_FK FOREIGN KEY (accountid) REFERENCES EndUser.Account_Tbl (id) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT Address2Card_FK FOREIGN KEY (cardid) REFERENCES EndUser.Card_Tbl (id) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT Address2Country_FK FOREIGN KEY (countryid) REFERENCES System.Country_Tbl (id) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT Address2State_FK FOREIGN KEY (stateid) REFERENCES System.State_Tbl (id) ON UPDATE CASCADE ON DELETE CASCADE,
	CHECK ( (accountid IS NULL AND cardid IS NOT NULL) OR (accountid IS NOT NULL AND cardid IS NULL) ),
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE TRIGGER Update_Address
BEFORE UPDATE
ON EndUser.Address_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE EndUser.Address_Tbl TO mpoint;
GRANT SELECT, UPDATE, USAGE ON TABLE EndUser.Address_Tbl_id_seq TO mpoint;

-- Table: Client.IpAddress_Tbl 
-- Used for IP WhiteListing 
CREATE TABLE Client.IPAddress_Tbl
(
	id				SERIAL,
	clientid		INT4 NOT NULL,
	ipaddress		VARCHAR(20),
	CONSTRAINT IPAddress_PK PRIMARY KEY (id),
	LIKE Template.General_Tbl INCLUDING DEFAULTS,
	CONSTRAINT IPAccess2Client_FK FOREIGN KEY (clientid) REFERENCES Client.Client_Tbl ON UPDATE CASCADE ON DELETE CASCADE
) WITHOUT OIDS;

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE Client.IPAddress_Tbl TO mpoint;
GRANT SELECT, UPDATE, USAGE ON TABLE Client.IPAddress_Tbl_id_seq TO mpoint;

ALTER TABLE Client.CardAccess_tbl ADD countryid INT4;