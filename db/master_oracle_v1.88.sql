CONNECT system_ownr/system_ownr;
DROP TABLE System_Ownr.CardState_Tbl;
DROP TABLE system_ownr.IINAction_Tbl;
DROP TABLE system_CardChargeType_Tbl;

DROP SEQUENCE System_Ownr.CardState_Tbl_id_seq;
DROP SEQUENCE System_Ownr.IINAction_Tbl_id_seq;
DROP SEQUENCE System_Ownr.CardChargeType_Tbl_id_seq;

CONNECT client_ownr/client_ownr;

DROP TABLE Client_Ownr.InfoType_Tbl;
DROP TABLE Client_Ownr.Info_Tbl;
DROP TABLE Client_Ownr.IINList_Tbl;

DROP SEQUENCE Client_Ownr.InfoType_Tbl_id_seq;
DROP SEQUENCE Client_Ownr.Info_Tbl_id_seq;
DROP SEQUENCE Client_Ownr.IINList_Tbl_id_seq;

CONNECT system_ownr/system_ownr;
/* ==================== SYSTEM SCHEMA START ==================== */
-- Table: System.CardState_Tbl 
CREATE TABLE System_Ownr.CardState_Tbl
(
	id			NUMBER(10,0) NOT NULL,
	name		VARCHAR2(100),
	created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
	
	CONSTRAINT CardState_PK PRIMARY KEY (id)
);

GRANT ALL ON System_Ownr.CardState_Tbl TO mpnt_ownr;
GRANT SELECT ON System_Ownr.CardState_Tbl TO mpnt_user;
CREATE SEQUENCE System_Ownr.CardState_Tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = System_Ownr;
CREATE OR REPLACE TRIGGER insert_CardState_trg BEFORE INSERT ON System_Ownr.IINAction_Tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT System_ownr.CardState_Tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_CardState_trg BEFORE UPDATE ON System_Ownr.CardState_Tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/

-- Table: System.IINAction_Tbl
-- Definition table for all actions (Block, Whitelist etc.) that may be taken for an Issuer Identification Number.
CREATE TABLE System_Ownr.IINAction_Tbl
(
	id			NUMBER(10,0) NOT NULL,
	
	name		VARCHAR2(100),
	note		CLOB,
	
	created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
	
	CONSTRAINT IINAction_PK PRIMARY KEY (id)
);

GRANT ALL ON System_Ownr.IINAction_Tbl TO mpnt_ownr;
GRANT SELECT ON System_Ownr.IINAction_Tbl TO mpnt_user;
CREATE SEQUENCE System_Ownr.IINAction_Tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = System_Ownr;
CREATE OR REPLACE TRIGGER insert_IINAction_trg BEFORE INSERT ON System_Ownr.IINAction_Tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT System_Ownr.IINAction_Tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_IINAction_trg BEFORE UPDATE ON System_Ownr.IINAction_Tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/

CREATE UNIQUE INDEX IINAction_UQ ON System_Ownr.IINAction_Tbl (Lower(name) ); 

-- Table: System_Ownr.CardChargeType_Tbl 
CREATE TABLE System_Ownr.CardChargeType_Tbl 
(
	id			NUMBER(10,0) NOT NULL,
	name		VARCHAR2(100),
	
	created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
	
	CONSTRAINT CardCharge_PK PRIMARY KEY (id)
);

GRANT ALL ON System_Ownr.CardChargeType_Tbl TO mpnt_ownr;
GRANT SELECT ON System_Ownr.CardChargeType_Tbl TO mpnt_user;
CREATE SEQUENCE System_Ownr.CardChargeType_Tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = System_Ownr;
CREATE OR REPLACE TRIGGER insert_CardChargeType_trg BEFORE INSERT ON System_Ownr.CardChargeType_Tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT System_Ownr.CardChargeType_Tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_IINAction_trg BEFORE UPDATE ON System_Ownr.CardChargeType_Tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
/* ==================== SYSTEM SCHEMA END ==================== */

CONNECT client_ownr/client_ownr;

/* ==================== CLIENT SCHEMA START ==================== */

-- Create link to Client_Ownr.CardAccess_Tbl
ALTER TABLE Client_Ownr.CardAccess_Tbl ADD(stateid Number(10) DEFAULT 1);


-- Table: Client.InfoType_Tbl
-- Definition table for all information types: About Text, Contact Info, Terms & Conditions, Help Text, Reminder Message, OTP Message etc.
CREATE TABLE Client_Ownr.InfoType_Tbl
(
	id			NUMBER(10,0) NOT NULL,
	name		VARCHAR2(100),
	note		CLOB,

	created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),

	CONSTRAINT InfoType_PK PRIMARY KEY (id)
);

GRANT ALL ON Client_Ownr.InfoType_Tbl TO mpnt_ownr;
GRANT SELECT ON Client_Ownr.InfoType_Tbl TO mpnt_user;
CREATE SEQUENCE Client_Ownr.InfoType_Tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = System_Ownr;
CREATE OR REPLACE TRIGGER insert_InfoType_trg BEFORE INSERT ON Client_Ownr.InfoType_Tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT Client_Ownr.InfoType_Tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_IINAction_trg BEFORE UPDATE ON Client_Ownr.InfoType_Tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
CREATE UNIQUE INDEX InfoType_UQ ON Client_Ownr.InfoType_Tbl (Lower(name) );

-- Table: Client_Ownr.Info_Tbl
-- Data table for all Client Information, texts may be in XHTML format
CREATE TABLE Client_Ownr.Info_Tbl
(
	id				NUMBER(10,0) NOT NULL,
	infotypeid		NUMBER(10) NOT NULL,	-- ID of the Information Type
	clientid		NUMBER(10) NOT NULL,	-- ID of the Client who owns the Info
	pspid			NUMBER(10),
	language		VARCHAR2(2) DEFAULT 'gb',

	text			CLOB,		-- Information text
	
	created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),

	CONSTRAINT Info_PK PRIMARY KEY (id),
	CONSTRAINT Info2InfoType_FK FOREIGN KEY (infotypeid) REFERENCES Client_Ownr.InfoType_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT Info2Client_FK FOREIGN KEY (clientid) REFERENCES Client_Ownr.Client_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT Info2PSP_FK FOREIGN KEY (pspid) REFERENCES System_Ownr.PSP_Tbl ON UPDATE CASCADE ON DELETE CASCADE
);

GRANT ALL ON Client_Ownr.Info_Tbl TO mpnt_ownr;
GRANT SELECT ON Client_Ownr.Info_Tbl TO mpnt_user;
CREATE SEQUENCE Client_Ownr.Info_Tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = System_Ownr;
CREATE OR REPLACE TRIGGER insert_ClientInfo_trg BEFORE INSERT ON Client_Ownr.Info_Tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT Client_Ownr.Info_Tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_ClientInfo_trg BEFORE UPDATE ON Client_Ownr.Info_Tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/

CREATE UNIQUE INDEX Info_PSP_UQ ON Client_Ownr.Info_Tbl (infotypeid, clientid, language, pspid) WHERE pspid IS NOT NULL;
CREATE UNIQUE INDEX Info_UQ ON Client_Ownr.Info_Tbl (infotypeid, clientid, language) WHERE pspid IS NULL;

-- Table: Client.IINList_Tbl
-- Data table for each client's lists of actions taken for a range of Issuer Identification Numbers
CREATE TABLE Client_Ownr.IINList_Tbl
(
	id			NUMBER(10,0) NOT NULL,
	iinactionid	NUMBER(10) NOT NULL,	-- ID of the action to take for the defined range of Issuer Identification Numbers
	clientid	NUMBER(10) NOT NULL,	-- ID of the client for which the specified action is defined

	min			Number(19),
	max			Number(19),

	created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),

	CONSTRAINT IINList_PK PRIMARY KEY (id),
	CONSTRAINT IINList2Client_FK FOREIGN KEY (clientid) REFERENCES Client_Ownr.Client_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT IINList2IINAction_FK FOREIGN KEY (iinactionid) REFERENCES System_Ownr.IINAction_Tbl ON UPDATE CASCADE ON DELETE CASCADE
);

GRANT ALL ON Client_Ownr.IINList_Tbl TO mpnt_ownr;
GRANT SELECT ON Client_Ownr.IINList_Tbl TO mpnt_user;
CREATE SEQUENCE Client_Ownr.IINList_Tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = System_Ownr;
CREATE OR REPLACE TRIGGER insert_IINList_trg BEFORE INSERT ON Client_Ownr.IINList_Tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT Client_Ownr.IINList_Tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_IINList_trg BEFORE UPDATE ON Client_Ownr.IINList_Tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/

CREATE INDEX IINRanges_Idx ON Client_Ownr.IINList_Tbl (clientid, min, max);
/* ==================== CLIENT SCHEMA END ==================== */

/* ==================== ENDUSER SCHEMA START ==================== */
CONNECT enduser_ownr/enduser_ownr;
-- Create link to EndUser.Card_Tbl
ALTER TABLE EndUser_Ownr.Card_Tbl ADD(charge_typeid NUMBER(10) DEFAULT 0);
/* ==================== ENDUSER SCHEMA START ==================== */

