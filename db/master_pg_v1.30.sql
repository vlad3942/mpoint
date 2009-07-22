/* ==================== END-USER SCHEMA START ==================== */
CREATE SCHEMA enduser AUTHORIZATION jona;

GRANT USAGE ON SCHEMA EndUser TO mpoint;

-- Table: EndUser.Account_Tbl
-- Data table for all End-User Accounts
CREATE TABLE EndUser.Account_Tbl
(
	id			SERIAL,
	countryid	INT4 NOT NULL,	-- ID of the country the account is valid in

	firstname	VARCHAR(50),
	lastname	VARCHAR(50),

	mobile		VARCHAR(15),	-- MSISDN which may be used for authenticating with the account
	email		VARCHAR(50),	-- E-Mail address which may be used for authenticating with the account
	
	passwd		VARCHAR(50),	-- User password for authenticating with the account
	
	balance		INT4 DEFAULT 0,	-- Account Balance if the account has been topped up manually

	CONSTRAINT Account_PK PRIMARY KEY (id),
	CONSTRAINT Account2Country_FK FOREIGN KEY (countryid) REFERENCES System.Country_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE UNIQUE INDEX Account_Mobile_UQ ON EndUser.Account_Tbl (countryid, mobile);
CREATE UNIQUE INDEX Account_EMail_UQ ON EndUser.Account_Tbl (countryid, Upper(email) );

CREATE TRIGGER Update_Account
BEFORE UPDATE
ON EndUser.Account_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

-- Internal
INSERT INTO EndUser.Account_Tbl (id, countryid, firstname, enabled) VALUES (0, 0, 'System Record', false);

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE EndUser.Account_Tbl TO mpoint;
GRANT SELECT, UPDATE, INSERT ON TABLE EndUser.Account_Tbl_id_seq TO mpoint;


-- Table: EndUser.Card_Tbl
-- Data table for all stored card information
CREATE TABLE EndUser.Card_Tbl
(
	id			SERIAL,
	accountid	INT4 NOT NULL,	-- ID of the end-user account that the account belongs to
	clientid	INT4,			-- ID of the Client that the card has been registered for, NULL for "Global Client"
	cardid		INT4 NOT NULL,	-- ID of the card type
	pspid		INT4 NOT NULL,	-- ID of the Payment Service Provider the saved card is valid through

	ticket		INT4,			-- Ticket representing the stored credit card which may be used to make withdrawals automatically

	mask		VARCHAR(20),	-- Masked card number
	expiry		VARCHAR(5),		-- Expiry date for the card in MM/YY format

	preferred	BOOL DEFAULT false,

	CONSTRAINT Card_PK PRIMARY KEY (id),
	CONSTRAINT Card2Account_FK FOREIGN KEY (accountid) REFERENCES EndUser.Account_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT Card2Client_FK FOREIGN KEY (clientid) REFERENCES Client.Client_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT Card2Card_FK FOREIGN KEY (cardid) REFERENCES System.Card_Tbl ON UPDATE CASCADE ON DELETE RESTRICT,
	CONSTRAINT Card2PSP_FK FOREIGN KEY (pspid) REFERENCES System.PSP_Tbl ON UPDATE CASCADE ON DELETE RESTRICT,
	CONSTRAINT Card_UQ UNIQUE (accountid, clientid, cardid, mask, expiry),
	CONSTRAINT Ticket_UQ UNIQUE (accountid, ticket),
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE TRIGGER Update_Card
BEFORE UPDATE
ON EndUser.Card_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

-- Internal
INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, enabled) VALUES (0, 0, 0, 0, false);

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE EndUser.Card_Tbl TO mpoint;
GRANT SELECT, UPDATE, INSERT ON TABLE EndUser.Card_Tbl_id_seq TO mpoint;


-- Table: EndUser.CLAccess_Tbl
-- Link table identifying which Clients an Account is valid for
CREATE TABLE EndUser.CLAccess_Tbl
(
	id			SERIAL,
	clientid	INT4 NOT NULL,	-- ID of the Client the Account is valid for
	accountid	INT4 NOT NULL,	-- ID of End-User Account

	CONSTRAINT CLAccess_PK PRIMARY KEY (id),
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE TRIGGER Update_CLAccess
BEFORE UPDATE
ON EndUser.CLAccess_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

-- Internal
INSERT INTO EndUser.CLAccess_Tbl (id, clientid, accountid, enabled) VALUES (0, 0, 0, false);

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE EndUser.CLAccess_Tbl TO mpoint;
GRANT SELECT, UPDATE, INSERT ON TABLE EndUser.CLAccess_Tbl_id_seq TO mpoint;


-- Table: EndUser.Transaction_Tbl
-- Transaction table for all e-money based transactions performed by the account holders
CREATE TABLE EndUser.Transaction_Tbl
(
	id			SERIAL,
	accountid	INT4 NOT NULL,	-- Account ID of the Transaction owner
	typeid		INT4 NOT NULL,	-- Unique ID of the Transaction type
		
	fromid		INT4,			-- Account ID of the sender, NULL if transaction is a purchase
	toid		INT4,			-- Account ID of the recipient, NULL if transaction is a purchase

	txnid		INT4,			-- Transaction ID for purchase, only applicable
	
	amount		INT4,

	CONSTRAINT Transaction_PK PRIMARY KEY (id),
	CONSTRAINT TxnOwner2Account_FK FOREIGN KEY (accountid) REFERENCES EndUser.Account_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT Txn2Type_FK FOREIGN KEY (typeid) REFERENCES System.Type_Tbl ON UPDATE CASCADE ON DELETE RESTRICT,
	CONSTRAINT TxnFrom2Account_FK FOREIGN KEY (fromid) REFERENCES EndUser.Account_Tbl ON UPDATE CASCADE ON DELETE NO ACTION,
	CONSTRAINT TxnTo2Account_FK FOREIGN KEY (toid) REFERENCES EndUser.Account_Tbl ON UPDATE CASCADE ON DELETE NO ACTION,
	CONSTRAINT Txn2Txn_FK FOREIGN KEY (txnid) REFERENCES Log.Transaction_Tbl ON UPDATE CASCADE ON DELETE NO ACTION,
	CONSTRAINT Transaction_Chk CHECK ( (fromid IS NULL AND toid IS NULL) OR txnid IS NULL),
	CONSTRAINT Transaction_UQ UNIQUE (typeid, txnid),
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

-- Trigger function for modifying the Transaction table.
-- The balance of the End User's account will be recalculated and stored in the "balance" field of EndUser.Account_Tbl
CREATE OR REPLACE FUNCTION Modify_EndUserTxn_Proc() RETURNS opaque AS
$BODY$
DECLARE
	iAccountID INT4;
BEGIN
	IF TG_OP = 'DELETE' THEN
		iAccountID := OLD.accountid;
	ELSE
		iAccountID := NEW.accountid;
	END IF;
	
	-- Update available balance on EndUser's Account
	UPDATE EndUser.Account_Tbl
	SET balance = (SELECT Sum(amount)
				   FROM EndUser.Transaction_Tbl
				   WHERE accountid = iAccountID AND enabled = true)
	WHERE id = iAccountID;
	
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

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE EndUser.Transaction_Tbl TO mpoint;
GRANT SELECT, UPDATE, INSERT ON TABLE EndUser.Transaction_Tbl_id_seq TO mpoint;


-- Table: EndUser.Activation_Tbl
-- Data table for holding all pending activations
CREATE TABLE EndUser.Activation_Tbl
(
	id			SERIAL,
	accountid	INT4 NOT NULL,
	
	code 		INT4,
	address		VARCHAR(50),
	
	active 		BOOL DEFAULT false,
	expiry		TIMESTAMP DEFAULT NOW() + interval '86400',
	
	CONSTRAINT Activation_PK PRIMARY KEY (id),
	CONSTRAINT Activation2Account_FK FOREIGN KEY (accountid) REFERENCES EndUser.Account_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT Activate_UQ UNIQUE (accountid, code)
) INHERITS (Template.General_Tbl) WITHOUT OIDS;

CREATE TRIGGER Insert_Activation
BEFORE UPDATE
ON EndUser.Activation_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE EndUser.Activation_Tbl TO mpoint;
GRANT SELECT, UPDATE, INSERT ON TABLE EndUser.Activation_Tbl_id_seq TO mpoint;
/* ==================== END-USER SCHEMA END ==================== */

ALTER TABLE Client.Client_Tbl ADD store_card INT4;
ALTER TABLE Client.Client_Tbl ALTER store_card SET DEFAULT 0;
ALTER TABLE Client.Client_Tbl ADD CONSTRAINT StoreCard_Chk CHECK (store_card >= 0 AND store_card <= 1);

ALTER TABLE Log.Transaction_Tbl RENAME address TO mobile;
ALTER TABLE Client.Account_Tbl RENAME address TO mobile;

ALTER TABLE Log.Transaction_Tbl ADD euaid INT4;
ALTER TABLE Log.Transaction_Tbl ADD CONSTRAINT Txn2EUA_FK FOREIGN KEY (euaid) REFERENCES EndUser.Account_Tbl ON UPDATE CASCADE ON DELETE RESTRICT;

/* ==================== SYSTEM SCHEMA START ==================== */
CREATE TABLE System.IPRange_Tbl
(
	id 			SERIAL,
	countryid	INT4 NOT NULL,
	
	min			INT8,
	max			INT8,
	country		VARCHAR(50),
	
	CONSTRAINT IPRange_PK PRIMARY KEY (id),
	CONSTRAINT IPRange2Country_FK FOREIGN KEY (countryid) REFERENCES System.Country_tbl ON UPDATE CASCADE ON DELETE NO ACTION,
	CONSTRAINT IPRange_UQ UNIQUE (min, max),
  	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

-- Internal
INSERT INTO System.IPRange_Tbl (id, countryid, country, enabled) VALUES (0, 0, 'System Record', false);

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE System.IPRange_Tbl TO mpoint;


CREATE TABLE System.DepositOption_Tbl
(
	id 			SERIAL,
	countryid	INT4 NOT NULL,
	
	amount		INT4,
	
	CONSTRAINT DepositOption_PK PRIMARY KEY (id),
	CONSTRAINT DepositOption2Country_FK FOREIGN KEY (countryid) REFERENCES System.Country_tbl ON UPDATE CASCADE ON DELETE NO ACTION,
	CONSTRAINT DepositOption_UQ UNIQUE (countryid, amount),
  	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

-- Internal
INSERT INTO System.DepositOption_Tbl (id, countryid, amount, enabled) VALUES (0, 0, 0, false);

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE System.DepositOption_Tbl TO mpoint;

ALTER TABLE System.Country_Tbl ADD maxbalance INT4;
ALTER TABLE System.Country_Tbl ADD mintransfer INT4;
ALTER TABLE System.Country_Tbl ADD symbol VARCHAR(3);
/* ==================== SYSTEM SCHEMA END ==================== */