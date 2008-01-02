/**
 * Master SQL script for the PostGreSQL databse.
 * The file include general functions and trigger functions useful in most types of databases
 */

/* ==================== TEMPLATE SCHEMA START ==================== */
CREATE SCHEMA Template AUTHORIZATION jona;

CREATE TABLE Template.General_Tbl
(
	created		TIMESTAMP DEFAULT NOW(),
	modified	TIMESTAMP DEFAULT NOW(),
	enabled		BOOL DEFAULT true
) WITHOUT OIDS;
/* ==================== TEMPLATE SCHEMA END ==================== */
 
/* ==================== GENERAL TRIGGER FUNCTIONS START ==================== */
/**
 * General trigger function for updating a table
 */
CREATE OR REPLACE FUNCTION Public.Update_Table_Proc() RETURNS opaque AS
'
BEGIN
	NEW.modified := NOW();
	
	RETURN NEW;
END;
'
LANGUAGE 'plpgsql';
/* ==================== GENERAL TRIGGER FUNCTIONS END ==================== */

/* ==================== GENERAL FUNCTIONS START ==================== */
/**
 * Constructs a date from year, month day
 * 
 * @param	int4 in_year		Year
 * @param	int4 in_month		Month
 * @param	int4 in_day			Day
 * @return	int4				Constructed Date
 */
CREATE OR REPLACE FUNCTION Const_Date_Proc(int4, int4, int4) RETURNS date AS
'
DECLARE
	-- Declare aliases for input
	in_year ALIAS FOR $1;
	in_month ALIAS FOR $2;
	in_day ALIAS FOR $3;
BEGIN
	RETURN in_year || \'-\' || in_month || \'-\' || in_day;
END;
'
LANGUAGE 'plpgsql' VOLATILE;
/* ==================== GENERAL FUNCTIONS END ==================== */

/* ==================== SYSTEM SCHEMA START ==================== */
CREATE SCHEMA System AUTHORIZATION jona;

GRANT USAGE ON SCHEMA System TO mpoint;

-- Table: System.Country_Tbl
-- Data table for all Countries mPoint can be used in
CREATE TABLE System.Country_Tbl
(
	id			SERIAL,
	
	name		VARCHAR(50),
	currency	CHAR(3),		-- Currency used in the Country in ISO-4217 format
	minmob		VARCHAR(15),	-- Minimum Value a vald Mobile Number can be in the Country
	maxmob		VARCHAR(15),	-- Maximum Value a vald Mobile Number can be in the Country
	channel		VARCHAR(10),	-- GoMobile Channel used for SMS Communication in the Country
	
	CONSTRAINT Country_PK PRIMARY KEY (id),
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE UNIQUE INDEX Country_UQ ON System.Country_Tbl (Upper(name) );

CREATE TRIGGER Update_Country
BEFORE UPDATE
ON System.Country_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

-- Internal
INSERT INTO System.Country_Tbl (id, name, enabled) VALUES (0, 'System Record', false);

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE System.Country_Tbl TO mpoint;


-- Table: System.Type_Tbl
-- Data table for all Transaction Types that mPoint can process
CREATE TABLE System.Type_Tbl
(
	id			SERIAL,
	
	name		VARCHAR(50),
		
	CONSTRAINT Type_PK PRIMARY KEY (id),
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE UNIQUE INDEX Type_UQ ON System.Type_Tbl (Upper(name) );

CREATE TRIGGER Update_Type
BEFORE UPDATE
ON System.Type_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

-- Internal
INSERT INTO System.Type_Tbl (id, name, enabled) VALUES (0, 'System Record', false);
INSERT INTO System.Type_Tbl (id, name) VALUES (10, 'SMS Purchase');
INSERT INTO System.Type_Tbl (id, name) VALUES (11, 'SMS Subscription');
INSERT INTO System.Type_Tbl (id, name) VALUES (20, 'Web Purchase');
INSERT INTO System.Type_Tbl (id, name) VALUES (21, 'Web Subscription');

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE System.Type_Tbl TO mpoint;


-- Table: System.PSP_Tbl
-- Data table for all Payment Service Provider mPoint is connected to
CREATE TABLE System.PSP_Tbl
(
	id			SERIAL,
	
	name		VARCHAR(50),
		
	CONSTRAINT PSP_PK PRIMARY KEY (id),
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE UNIQUE INDEX PSP_UQ ON System.PSP_Tbl (Upper(name) );

CREATE TRIGGER Update_PSP
BEFORE UPDATE
ON System.PSP_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

-- Internal
INSERT INTO System.PSP_Tbl (id, name, enabled) VALUES (0, 'System Record', false);

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE System.PSP_Tbl TO mpoint;


-- Table: System.PSPCurrency_Tbl
-- Translation table for translating mPoint currency codes into the correct code for each Payment Service Provider
CREATE TABLE System.PSPCurrency_Tbl
(
	id			SERIAL,
	countryid	INT4 NOT NULL,	-- ID of the Currency the translation is valid for
	pspid		INT4 NOT NULL,	-- ID of the Payment Service Provider the translation is valid for
	
	name		CHAR(3),
		
	CONSTRAINT PSPCurrency_PK PRIMARY KEY (id),
	CONSTRAINT PSPCurrency2Country_FK FOREIGN KEY (countryid) REFERENCES System.Country_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT PSPCurrency2PSP_FK FOREIGN KEY (pspid) REFERENCES System.PSP_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT PSPCurrency_UQ UNIQUE (countryid, pspid),
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE TRIGGER Update_PSPCurrency
BEFORE UPDATE
ON System.PSPCurrency_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

-- Internal
INSERT INTO System.PSPCurrency_Tbl (id, countryid, pspid, name, enabled) VALUES (0, 0, 0, '', false);

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE System.PSPCurrency_Tbl TO mpoint;


-- Table: System.Card_Tbl
-- Data table for all available Credit Cards
CREATE TABLE System.Card_Tbl
(
	id			SERIAL,
	
	name		VARCHAR(50),
	logo		BYTEA,
		
	CONSTRAINT Card_PK PRIMARY KEY (id),
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE UNIQUE INDEX Card_UQ ON System.Card_Tbl (Upper(name) );

CREATE TRIGGER Update_Card
BEFORE UPDATE
ON System.Card_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

-- Internal
INSERT INTO System.Card_Tbl (id, name, enabled) VALUES (0, 'System Record', false);

INSERT INTO System.Card_Tbl (name) VALUES ('American Express');
INSERT INTO System.Card_Tbl (name) VALUES ('Dankort');
INSERT INTO System.Card_Tbl (name) VALUES ('Diners Club');
INSERT INTO System.Card_Tbl (name) VALUES ('EuroCard');
INSERT INTO System.Card_Tbl (name) VALUES ('JCB');
INSERT INTO System.Card_Tbl (name) VALUES ('Maestro');
INSERT INTO System.Card_Tbl (name) VALUES ('Master Card');
INSERT INTO System.Card_Tbl (name) VALUES ('VISA');
INSERT INTO System.Card_Tbl (name) VALUES ('VISA Electron');

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE System.Card_Tbl TO mpoint;
/* ==================== SYSTEM SCHEMA END ==================== */

/* ==================== CLIENT SCHEMA START ==================== */
CREATE SCHEMA Client AUTHORIZATION jona;

GRANT USAGE ON SCHEMA Client TO mpoint;

-- Table: Client.Client_Tbl
-- Data table for all Countries mPoint can be used in
CREATE TABLE Client.Client_Tbl
(
	id			SERIAL,
	countryid	INT4 NOT NULL,	-- ID of the Country the Client can Operate in
	
	name		VARCHAR(50),
	username	VARCHAR(50),	-- GoMobile Username
	passwd		VARCHAR(50),	-- GoMobile Password
	
	logourl		VARCHAR(255),	-- Absolute URL where the mPoint can fetch the Client Logo
	cssurl		VARCHAR(255),	-- Absolute URL where the mPoint can fetch custom CSS file
	callbackurl	VARCHAR(255),	-- Absolute URL where mPoint should send payment status
	accepturl	VARCHAR(255),	-- Absolute URL where mPoint should direct the customer to upon accepted payment
	cancelurl	VARCHAR(255),	-- Absolute URL where mPoint should direct the customer to upon the customer cancelling the payment
	
	CONSTRAINT Client_PK PRIMARY KEY (id),
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE UNIQUE INDEX Client_UQ ON Client.Client_Tbl (Upper(name) );

CREATE TRIGGER Update_Client
BEFORE UPDATE
ON Client.Client_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

SELECT setval('Client.Client_Tbl_id_seq', 9999);

-- Internal
INSERT INTO Client.Client_Tbl (id, countryid, name, enabled) VALUES (0, 0, 'System Record', false);

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE Client.Client_Tbl TO mpoint;


-- Table: Client.CardAccess_Tbl
-- Link table for defining which credit cards each client has access to
CREATE TABLE Client.CardAccess_Tbl
(
	id			SERIAL,
	clientid	INT4 NOT NULL,	-- ID of the Client who has access to the credit card
	cardid		INT4 NOT NULL,	-- ID of the Credit card to which the Client has access
		
	CONSTRAINT CardAccess_PK PRIMARY KEY (id),
	CONSTRAINT CardAccess2Client_FK FOREIGN KEY (clientid) REFERENCES Client.Client_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT CardAccess2Card_FK FOREIGN KEY (cardid) REFERENCES System.Card_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT CardAccess_UQ UNIQUE (clientid, cardid),
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE TRIGGER Update_CardAccess
BEFORE UPDATE
ON Client.CardAccess_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

-- Internal
INSERT INTO Client.CardAccess_Tbl (id, clientid, cardid, enabled) VALUES (0, 0, 0, false);

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE Client.CardAccess_Tbl TO mpoint;


-- Table: Client.MerchantAccount_Tbl
-- Link table for defining which Payment Service Providers (PSP) a Client has access to and what
-- account name mPoint should use for communicating with the PSP
CREATE TABLE Client.MerchantAccount_Tbl
(
	id			SERIAL,
	clientid	INT4 NOT NULL,	-- ID of the Client who has access to the credit card
	pspid		INT4 NOT NULL,	-- ID of the PSP the Merchant Account is Valid for
	
	name		VARCHAR(50),	-- Clients account name with the PSP
		
	CONSTRAINT MerchantAccount_PK PRIMARY KEY (id),
	CONSTRAINT MerchantAccount2Client_FK FOREIGN KEY (clientid) REFERENCES Client.Client_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT MerchantAccount2PSP_FK FOREIGN KEY (pspid) REFERENCES System.PSP_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT MerchantAccount_UQ UNIQUE (clientid, pspid),
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE TRIGGER Update_MerchantAccount
BEFORE UPDATE
ON Client.MerchantAccount_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

-- Internal
INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name, enabled) VALUES (0, 0, 0, '', false);

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE Client.MerchantAccount_Tbl TO mpoint;


-- Table: Client.Account_Tbl
-- Data table for all Sub-Accounts that a Client may have.
-- Sub-Accounts provides a convenient way to track transactions from separate departments or Sales persons
CREATE TABLE Client.Account_Tbl
(
	id			SERIAL,
	clientid	INT4 NOT NULL,	-- ID of the Client who owns the Account
	
	name		VARCHAR(50),	-- Name of the Account
	address		VARCHAR(15),	-- MSISDN of the sales person who uses the account
		
	CONSTRAINT Account_PK PRIMARY KEY (id),
	CONSTRAINT Account2Client_FK FOREIGN KEY (clientid) REFERENCES Client.Client_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT Account_UQ UNIQUE (clientid, address),
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE UNIQUE INDEX AccountName_UQ ON Client.Account_Tbl (Upper(name) );

CREATE TRIGGER Update_Account
BEFORE UPDATE
ON Client.Account_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

SELECT setval('Client.Account_Tbl_id_seq', 99999);

-- Internal
INSERT INTO Client.Account_Tbl (id, clientid, name, enabled) VALUES (0, 0, 'System Record', false);

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE Client.Account_Tbl TO mpoint;


-- Table: Client.MerchantSubAccount_Tbl
-- Link table for defining which Payment Service Providers (PSP) a Client has access to and what
-- sub-account names mPoint should use for communicating with the PSP
CREATE TABLE Client.MerchantSubAccount_Tbl
(
	id			SERIAL,
	accountid	INT4 NOT NULL,	-- ID of the mPoint Sub-Account
	pspid		INT4 NOT NULL,	-- ID of the PSP the Sub-Account is valid for
	
	name		VARCHAR(50),	-- Clients sub-account name with the PSP
		
	CONSTRAINT MerchantSubAccount_PK PRIMARY KEY (id),
	CONSTRAINT MerchantSubAccount2Account_FK FOREIGN KEY (accountid) REFERENCES Client.Account_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT MerchantSubAccount2PSP_FK FOREIGN KEY (pspid) REFERENCES System.PSP_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT MerchantSubAccount_UQ UNIQUE (accountid, pspid),
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE TRIGGER Update_MerchantSubAccount
BEFORE UPDATE
ON Client.MerchantSubAccount_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

-- Internal
INSERT INTO Client.MerchantSubAccount_Tbl (id, accountid, pspid, name, enabled) VALUES (0, 0, 0, 'System Record', false);

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE Client.MerchantSubAccount_Tbl TO mpoint;


-- Table: Client.Keyword_Tbl
-- Data table for all Keywords that a Client has defined for making purchases via SMS
CREATE TABLE Client.Keyword_Tbl
(
	id			SERIAL,
	clientid	INT4 NOT NULL,	-- ID of the Client who owns the Keyword
	
	name		VARCHAR(50),	-- Name of the Keyword
	price		INT4,			-- Price that any purchase made using this keyword should be charged at in Countrys smallest currency
		
	CONSTRAINT Keyword_PK PRIMARY KEY (id),
	CONSTRAINT Keyword2Client_FK FOREIGN KEY (clientid) REFERENCES Client.Client_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE UNIQUE INDEX Keyword_UQ ON Client.Keyword_Tbl (clientid, Upper(name) );

CREATE TRIGGER Update_Keyword
BEFORE UPDATE
ON Client.Keyword_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

-- Internal
INSERT INTO Client.Keyword_Tbl (id, clientid, name, enabled) VALUES (0, 0, 'System Record', false);

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE Client.Keyword_Tbl TO mpoint;


-- Table: Client.Product_Tbl
-- Data table for all Products that can be sold by sending a keyword in an SMS
CREATE TABLE Client.Product_Tbl
(
	id			SERIAL,
	keywordid	INT4 NOT NULL,	-- ID of the Keyword the Product belongs to
	
	name		VARCHAR(50),	-- Name of the Product
	units		INT4 DEFAULT 1,	-- Number of units purchased for this product
	price		INT4,			-- Price of Product
	logourl		VARCHAR(255),	-- URL where mPoint can fetch the product logo
		
	CONSTRAINT Product_PK PRIMARY KEY (id),
	CONSTRAINT Product2Keyword_FK FOREIGN KEY (keywordid) REFERENCES Client.Keyword_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE TRIGGER Update_Product
BEFORE UPDATE
ON Client.Product_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

-- Internal
INSERT INTO Client.Product_Tbl (id, keywordid, name, enabled) VALUES (0, 0, 'System Record', false);

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE Client.Product_Tbl TO mpoint;
/* ==================== CLIENT SCHEMA END ==================== */


/* ==================== LOG SCHEMA START ==================== */
CREATE SCHEMA Log AUTHORIZATION jona;

GRANT USAGE ON SCHEMA Log TO mpoint;

-- Table: Log.Transaction_Tbl
-- Log table for holding the important data for each Transaction
CREATE TABLE Log.Transaction_Tbl
(
	id			SERIAL,
	typeid		INT4 NOT NULL,	-- ID of the Transaction Type
	clientid	INT4 NOT NULL,	-- ID of the Client who owns the Transaction
	accountid	INT4 NOT NULL,	-- ID of the Account the Transaction was made through
	countryid	INT4 NOT NULL,	-- ID of the Country the Transaction was made in
	pspid		INT4 NOT NULL,	-- ID of the PSP the Transaction was cleared by
	cardid		INT4 NOT NULL,	-- ID of the Credit card the customer used to pay for the transaction
	keywordid	INT4,			-- ID of the Keyword the Transaction belongs to
	amount		INT4 NOT NULL,	-- Total amount charged to the customer for the Transaction
	
	orderid		VARCHAR(40) NOT NULL,	-- Clients Order ID of the Transaction
	extid		VARCHAR(40) NOT NULL,	-- External ID returned by the PSP
	
	address		VARCHAR(15),	-- MSISDN of the customer who made the purchase
	operatorid	INT4,			-- GoMobile ID for the Customers Mobile Network Operator
	
	logourl		VARCHAR(255),	-- URL to the logo that was used for the Transaction
	cssurl		VARCHAR(255),	-- URL to the Stylesheet that was used for the Transaction
	callbackurl	VARCHAR(255),	-- URL where mPoint sent the order details
	accepturl	VARCHAR(255),	-- URL where the customer was taken upon successfully completing the payment
	cancelurl	VARCHAR(255),	-- URL where the customer was taken upon cancelling the payment
		
	CONSTRAINT Transaction_PK PRIMARY KEY (id),
	CONSTRAINT Txn2Type_FK FOREIGN KEY (typeid) REFERENCES System.Type_Tbl ON UPDATE CASCADE ON DELETE RESTRICT,
	CONSTRAINT Txn2Client_FK FOREIGN KEY (clientid) REFERENCES Client.Client_Tbl ON UPDATE CASCADE ON DELETE RESTRICT,
	CONSTRAINT Txn2Account_FK FOREIGN KEY (accountid) REFERENCES Client.Account_Tbl ON UPDATE CASCADE ON DELETE RESTRICT,
	CONSTRAINT Txn2Country_FK FOREIGN KEY (countryid) REFERENCES System.Country_Tbl ON UPDATE CASCADE ON DELETE RESTRICT,
	CONSTRAINT Txn2PSP_FK FOREIGN KEY (pspid) REFERENCES System.PSP_Tbl ON UPDATE CASCADE ON DELETE RESTRICT,
	CONSTRAINT Txn2Card_FK FOREIGN KEY (cardid) REFERENCES System.Card_Tbl ON UPDATE CASCADE ON DELETE RESTRICT,
	CONSTRAINT Txn2Keyword_FK FOREIGN KEY (keywordid) REFERENCES Client.Keyword_Tbl ON UPDATE CASCADE ON DELETE RESTRICT,
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE TRIGGER Update_Transaction
BEFORE UPDATE
ON Log.Transaction_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

-- Internal
INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, cardid, amount, orderid, extid, enabled) VALUES (0, 0, 0, 0, 0, 0, 0, -1, 'System Record', 'System Record', false);

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE Log.Transaction_Tbl TO mpoint;


-- Table: Log.State_Tbl
-- Data table for all States that a Transaction can go through
CREATE TABLE Log.State_Tbl
(
	id			SERIAL,
	name		VARCHAR(50),	-- Name of the State
	
	module		VARCHAR(255),	-- Name of the Module that logs this State
	func		VARCHAR(255),	-- Name of the Function that logs this State
		
	CONSTRAINT State_PK PRIMARY KEY (id),
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE TRIGGER Update_State
BEFORE UPDATE
ON Log.State_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

-- Internal
INSERT INTO Log.State_Tbl (id, name, enabled) VALUES (0, 'System Record', false);

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE Log.State_Tbl TO mpoint;


-- Table: Log.Message_Tbl
-- Link table for defining which Payment Service Providers (PSP) a Client has access to and what
-- sub-account names mPoint should use for communicating with the PSP
CREATE TABLE Log.Message_Tbl
(
	id			SERIAL,
	txnid		INT4 NOT NULL,	-- ID of the Transaction the message belongs to
	stateid		INT4 NOT NULL,	-- ID of the State the message identifies
	
	data		TEXT,			-- Application data for debugging purposes
		
	CONSTRAINT Message_PK PRIMARY KEY (id),
	CONSTRAINT Msg2Txn_FK FOREIGN KEY (txnid) REFERENCES Log.Transaction_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT Msg2State_FK FOREIGN KEY (stateid) REFERENCES Log.State_Tbl ON UPDATE CASCADE ON DELETE RESTRICT,
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE TRIGGER Update_Message
BEFORE UPDATE
ON Log.Message_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

-- Internal
INSERT INTO Log.Message_Tbl (id, txnid, stateid, data, enabled) VALUES (0, 0, 0, 'System Record', false);

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE Log.Message_Tbl TO mpoint;
/* ==================== LOG SCHEMA END ==================== */