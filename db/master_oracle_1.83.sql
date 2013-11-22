-- Return database to pristine version
--DROP USER mpnt_user CASCADE;
--DROP USER admin CASCADE;
--DROP USER enduser CASCADE;
--DROP USER log CASCADE;
--DROP USER client CASCADE;
CONNECT log_ownr/log_ownr;
DROP TABLE log_ownr.Note_tbl;
DROP SEQUENCE log_ownr.Note_tbl_id_seq;
CONNECT enduser_ownr/enduser_ownr;
DROP TABLE enduser_ownr.transaction_tbl;
DROP SEQUENCE enduser_ownr.transaction_tbl_id_seq;
CONNECT log_ownr/log_ownr;
DROP TABLE log_ownr.message_tbl;
DROP SEQUENCE log_ownr.message_tbl_id_seq;
DROP TABLE log_ownr.state_tbl;
DROP SEQUENCE log_ownr.state_tbl_id_seq;
DROP TABLE log_ownr.transaction_tbl;
DROP SEQUENCE log_ownr.transaction_tbl_id_seq;
DROP TABLE log_ownr.auditlog_tbl;
DROP SEQUENCE log_ownr.auditlog_tbl_id_seq;
DROP TABLE log_ownr.operation_tbl;
DROP SEQUENCE log_ownr.operation_tbl_id_seq;
CONNECT enduser_ownr/enduser_ownr;
DROP TABLE enduser_ownr.claccess_tbl;
DROP SEQUENCE enduser_ownr.claccess_tbl_id_seq;
DROP TABLE enduser_ownr.Address_tbl;
DROP SEQUENCE enduser_ownr.Address_tbl_id_seq;
DROP TABLE enduser_ownr.card_tbl;
DROP SEQUENCE enduser_ownr.card_tbl_id_seq;
DROP TABLE enduser_ownr.activation_tbl;
DROP SEQUENCE enduser_ownr.activation_tbl_id_seq;
DROP TABLE enduser_ownr.account_tbl;
DROP SEQUENCE enduser_ownr.account_tbl_id_seq;
CONNECT admin_ownr/admin_ownr;
DROP TABLE admin_ownr.access_tbl;
DROP SEQUENCE admin_ownr.access_tbl_id_seq;
DROP TABLE admin_ownr.RoleAccess_tbl;
DROP SEQUENCE admin_ownr.RoleAccess_tbl_id_seq;
DROP TABLE admin_ownr.user_tbl;
DROP SEQUENCE admin_ownr.user_tbl_id_seq;
DROP TABLE admin_ownr.RoleInfo_tbl;
DROP SEQUENCE admin_ownr.RoleInfo_tbl_id_seq;
DROP TABLE admin_ownr.Role_tbl;
DROP SEQUENCE admin_ownr.Role_tbl_id_seq;
CONNECT client_ownr/client_ownr;
DROP TABLE client_ownr.shipping_tbl;
DROP SEQUENCE client_ownr.shipping_tbl_id_seq;
DROP TABLE client_ownr.shop_tbl;
DROP SEQUENCE client_ownr.shop_tbl_id_seq;
DROP TABLE client_ownr.product_tbl;
DROP SEQUENCE client_ownr.product_tbl_id_seq;
DROP TABLE client_ownr.surepay_tbl;
DROP SEQUENCE client_ownr.surepay_tbl_id_seq;
DROP TABLE client_ownr.merchantsubaccount_tbl;
DROP SEQUENCE client_ownr.merchantsubaccount_tbl_id_seq;
DROP TABLE client_ownr.merchantaccount_tbl;
DROP SEQUENCE client_ownr.merchantaccount_tbl_id_seq;
DROP TABLE client_ownr.keyword_tbl;
DROP SEQUENCE client_ownr.keyword_tbl_id_seq;
DROP TABLE client_ownr.cardaccess_tbl;
DROP SEQUENCE client_ownr.cardaccess_tbl_id_seq;
DROP TABLE client_ownr.account_tbl;
DROP SEQUENCE client_ownr.account_tbl_id_seq;
DROP TABLE client_ownr.URL_tbl;
DROP SEQUENCE client_ownr.URL_tbl_id_seq;
DROP TABLE client_ownr.IPAddress_tbl;
DROP SEQUENCE client_ownr.IPAddress_tbl_id_seq;
DROP TABLE client_ownr.client_tbl;
DROP SEQUENCE client_ownr.client_tbl_id_seq;

CONNECT system_ownr/system_ownr;
DROP TABLE system_ownr.shipping_tbl;
DROP TABLE system_ownr.cardprefix_tbl;
DROP TABLE system_ownr.cardpricing_tbl;
DROP TABLE system_ownr.fee_tbl;
DROP TABLE system_ownr.feetype_tbl;
DROP TABLE system_ownr.iprange_tbl;
DROP TABLE system_ownr.pricepoint_tbl;
DROP TABLE system_ownr.pspcard_tbl;
DROP TABLE system_ownr.pspcurrency_tbl;
DROP TABLE system_ownr.depositoption_tbl;
DROP TABLE system_ownr.psp_tbl;
DROP TABLE system_ownr.card_tbl;
DROP TABLE system_ownr.flow_tbl;
DROP TABLE system_ownr.type_tbl;
DROP TABLE system_ownr.URLType_tbl;
DROP TABLE System_Ownr.PostalCode_Tbl;
DROP SEQUENCE System_Ownr.PostalCode_Tbl_id_seq;
DROP TABLE System_Ownr.State_Tbl;
DROP SEQUENCE System_Ownr.State_Tbl_id_seq;
DROP TABLE system_ownr.country_tbl;

DROP SEQUENCE system_ownr.country_tbl_id_seq;
DROP SEQUENCE system_ownr.shipping_tbl_id_seq;
DROP SEQUENCE system_ownr.card_tbl_id_seq;
DROP SEQUENCE system_ownr.cardprefix_tbl_id_seq;
DROP SEQUENCE system_ownr.cardpricing_tbl_id_seq;
DROP SEQUENCE system_ownr.feetype_tbl_id_seq;
DROP SEQUENCE system_ownr.fee_tbl_id_seq;
DROP SEQUENCE system_ownr.iprange_tbl_id_seq;
DROP SEQUENCE system_ownr.pricepoint_tbl_id_seq;
DROP SEQUENCE system_ownr.psp_tbl_id_seq;
DROP SEQUENCE system_ownr.pspcard_tbl_id_seq;
DROP SEQUENCE system_ownr.pspcurrency_tbl_id_seq;
DROP SEQUENCE system_ownr.depositoption_tbl_id_seq;
DROP SEQUENCE system_ownr.flow_tbl_id_seq;
DROP SEQUENCE system_ownr.type_tbl_id_seq;
DROP SEQUENCE system_ownr.URLType_tbl_id_seq;

--CREATE USER mpnt_user IDENTIFIED BY mpnt_user;
--GRANT CREATE SESSION TO mpnt_user;

/* ========== SYSTEM SCHEMA START ========== */
--CREATE USER system IDENTIFIED BY system_ownr;
--GRANT UNLIMITED TABLESPACE TO system_ownr;

CONNECT system_ownr/system_ownr;
CREATE TABLE system_ownr.country_tbl 
(
   id NUMBER(10,0) NOT NULL,
   name VARCHAR2(100),
   currency CHAR(3),
   minmob VARCHAR2(15),
   maxmob VARCHAR2(15),
   channel VARCHAR2(10),
   priceformat VARCHAR2(18),
   decimals NUMBER(10,0),
   addr_lookup NUMBER(1,0) DEFAULT 0,
   doi NUMBER(1,0) DEFAULT 0,
   maxbalance NUMBER(10,0),
   mintransfer NUMBER(10,0),
   symbol VARCHAR2(3),
   add_card_amount NUMBER(10,0),
   max_psms_amount NUMBER(10,0),
   min_pwd_amount NUMBER(10,0),
   min_2fa_amount NUMBER(10,0),
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT country_pk PRIMARY KEY (id)
);
GRANT ALL ON System_Ownr.Country_Tbl TO mpnt_ownr;
GRANT SELECT ON system_ownr.Country_Tbl TO mpnt_user;
CREATE SEQUENCE system_ownr.country_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = system_ownr;
CREATE OR REPLACE TRIGGER insert_country_trg BEFORE INSERT ON system_ownr.country_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT system_ownr.country_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_country_trg BEFORE UPDATE ON system_ownr.country_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
CREATE UNIQUE INDEX country_uq ON system_ownr.country_tbl (lower(name) );

CREATE TABLE system_ownr.shipping_tbl 
(
   id NUMBER(10,0) NOT NULL,
   name VARCHAR2(50),
   logourl VARCHAR2(100),
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT shipping_pk PRIMARY KEY (id)
);
GRANT ALL ON System_Ownr.Shipping_Tbl TO mpnt_ownr;
GRANT SELECT ON system_ownr.Shipping_Tbl TO mpnt_user;
CREATE SEQUENCE system_ownr.shipping_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = system_ownr;
CREATE OR REPLACE TRIGGER insert_shipping_trg BEFORE INSERT ON system_ownr.shipping_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT system_ownr.shipping_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_shipping_trg BEFORE UPDATE ON system_ownr.shipping_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
CREATE UNIQUE INDEX shipping_uq ON system_ownr.shipping_tbl (lower(name) );

CREATE TABLE system_ownr.type_tbl 
(
   id NUMBER(10,0) NOT NULL,
   name VARCHAR2(50),
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT type_pk PRIMARY KEY (id)
);
GRANT ALL ON System_Ownr.type_tbl TO mpnt_ownr;
GRANT SELECT ON system_ownr.type_tbl TO mpnt_user;
CREATE SEQUENCE system_ownr.type_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = system_ownr;
CREATE OR REPLACE TRIGGER insert_type_trg BEFORE INSERT ON system_ownr.type_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT system_ownr.type_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_type_trg BEFORE UPDATE ON system_ownr.type_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
CREATE UNIQUE INDEX type_uq ON system_ownr.type_tbl (lower(name) );

CREATE TABLE system_ownr.pricepoint_tbl 
(
   id NUMBER(10,0) NOT NULL,
   countryid NUMBER(10,0),
   amount NUMBER(10,0),
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT pricepoint_pk PRIMARY KEY (id),
   CONSTRAINT pricepoint2country_fk FOREIGN KEY(countryid) REFERENCES country_tbl(id) ON DELETE CASCADE,
   CONSTRAINT pricepoint_uq UNIQUE (countryid, amount)
);
GRANT ALL ON System_Ownr.PricePoint_Tbl TO mpnt_ownr;
GRANT SELECT ON system_ownr.PricePoint_Tbl TO mpnt_user;
CREATE SEQUENCE system_ownr.pricepoint_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = system_ownr;
CREATE OR REPLACE TRIGGER insert_pricepoint_trg BEFORE INSERT ON system_ownr.pricepoint_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT system_ownr.pricepoint_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_pricepoint_trg BEFORE UPDATE ON system_ownr.pricepoint_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
 	
CREATE TABLE system_ownr.card_tbl 
(
   id NUMBER(10,0) NOT NULL,
   name VARCHAR2(50),
   logo BLOB,
   position NUMBER(10,0),
   minlength NUMBER(10,0),
   maxlength NUMBER(10,0),
   cvclength NUMBER(10,0),
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT card_pk PRIMARY KEY (id)
);
GRANT ALL ON System_Ownr.Card_Tbl TO mpnt_ownr;
GRANT SELECT ON system_ownr.Card_Tbl TO mpnt_user;
CREATE SEQUENCE system_ownr.card_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = system_ownr;
CREATE OR REPLACE TRIGGER insert_card_trg BEFORE INSERT ON system_ownr.card_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT system_ownr.card_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_card_trg BEFORE UPDATE ON system_ownr.card_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
CREATE UNIQUE INDEX card_uq ON system_ownr.card_tbl (Lower(name) );

CREATE TABLE system_ownr.cardprefix_tbl 
(
   id NUMBER(10,0) NOT NULL,
   cardid NUMBER(10,0) NOT NULL,
   min NUMBER(19,0),
   "max" NUMBER(19,0),
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT cardprefix_pk PRIMARY KEY (id),
   CONSTRAINT cardprefix2card_fk FOREIGN KEY(cardid) REFERENCES card_tbl(id) ON DELETE CASCADE
);
GRANT ALL ON System_Ownr.CardPrefix_Tbl TO mpnt_ownr;
GRANT SELECT ON system_ownr.CardPrefix_Tbl TO mpnt_user;
CREATE SEQUENCE system_ownr.cardprefix_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = system_ownr;
CREATE OR REPLACE TRIGGER insert_cardprefix_trg BEFORE INSERT ON system_ownr.cardprefix_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT system_ownr.cardprefix_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_cardprefix_trg BEFORE UPDATE ON system_ownr.cardprefix_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/

CREATE TABLE system_ownr.cardpricing_tbl 
(
   id NUMBER(10,0) NOT NULL,
   pricepointid NUMBER(10,0),
   cardid NUMBER(10,0),
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT cardpricing_pk PRIMARY KEY (id),
   CONSTRAINT cardpricing2card_fk FOREIGN KEY(cardid) REFERENCES card_tbl(id) ON DELETE CASCADE,
   CONSTRAINT cardpricing2pricepoint_fk FOREIGN KEY(pricepointid) REFERENCES pricepoint_tbl(id) ON DELETE CASCADE,
   CONSTRAINT cardpricing_uq UNIQUE (pricepointid, cardid)
);
GRANT ALL ON System_Ownr.CardPricing_Tbl TO mpnt_ownr;
GRANT SELECT ON system_ownr.CardPricing_Tbl TO mpnt_user;
CREATE SEQUENCE system_ownr.cardpricing_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = system_ownr;
CREATE OR REPLACE TRIGGER insert_cardpricing_trg BEFORE INSERT ON system_ownr.cardpricing_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT system_ownr.cardpricing_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_cardpricing_trg BEFORE UPDATE ON system_ownr.cardpricing_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
ALTER SEQUENCE system_ownr.cardpricing_tbl_id_seq INCREMENT BY 127;

CREATE TABLE system_ownr.feetype_tbl 
(
   id NUMBER(10,0) NOT NULL,
   name VARCHAR2(50),
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT feetype_pk PRIMARY KEY (id)
);
GRANT ALL ON System_Ownr.FeeType_Tbl TO mpnt_ownr;
GRANT SELECT ON system_ownr.FeeType_Tbl TO mpnt_user;
CREATE SEQUENCE system_ownr.feetype_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = system_ownr;
CREATE OR REPLACE TRIGGER insert_feetype_trg BEFORE INSERT ON system_ownr.feetype_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT system_ownr.feetype_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_feetype_trg BEFORE UPDATE ON system_ownr.feetype_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
CREATE UNIQUE INDEX feetype_uq ON system_ownr.feetype_tbl (Lower(name) );

CREATE TABLE system_ownr.fee_tbl 
(
   id NUMBER(10,0) NOT NULL,
   typeid NUMBER(10,0) NOT NULL,
   fromid NUMBER(10,0) NOT NULL,
   toid NUMBER(10,0) NOT NULL,
   minfee NUMBER(10,0),
   basefee NUMBER(10,0),
   "share" BINARY_DOUBLE,
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT fee_pk PRIMARY KEY (id),
   CONSTRAINT fee2fromcountry_fk FOREIGN KEY(fromid) REFERENCES country_tbl(id) ON DELETE CASCADE,
   CONSTRAINT fee2tocountry_fk FOREIGN KEY(toid) REFERENCES country_tbl(id) ON DELETE CASCADE,
   CONSTRAINT fee2type_fk FOREIGN KEY(typeid) REFERENCES feetype_tbl(id) ON DELETE CASCADE,
   CONSTRAINT fee_uq UNIQUE (typeid, fromid, toid)
);
GRANT ALL ON System_Ownr.Fee_Tbl TO mpnt_ownr;
GRANT SELECT ON system_ownr.Fee_Tbl TO mpnt_user;
CREATE SEQUENCE system_ownr.fee_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = system_ownr;
CREATE OR REPLACE TRIGGER insert_fee_trg BEFORE INSERT ON system_ownr.fee_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT system_ownr.fee_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_fee_trg BEFORE UPDATE ON system_ownr.fee_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/

CREATE TABLE system_ownr.iprange_tbl 
(
   id NUMBER(10,0) NOT NULL,
   countryid NUMBER(10,0) NOT NULL,
   min NUMBER(19,0),
   max NUMBER(19,0),
   country VARCHAR2(50),
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT iprange_pk PRIMARY KEY (id),
   CONSTRAINT iprange2country_fk FOREIGN KEY(countryid) REFERENCES system_ownr.country_tbl(id),
   CONSTRAINT iprange_uq UNIQUE (min, max)
);
GRANT ALL ON System_Ownr.IPRange_Tbl TO mpnt_ownr;
GRANT SELECT ON system_ownr.IPRange_Tbl TO mpnt_user;
CREATE SEQUENCE system_ownr.iprange_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = system_ownr;
CREATE OR REPLACE TRIGGER insert_iprange_trg BEFORE INSERT ON system_ownr.iprange_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT system_ownr.iprange_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_iprange_trg BEFORE UPDATE ON system_ownr.iprange_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/

CREATE TABLE system_ownr.psp_tbl 
(
   id NUMBER(10,0) NOT NULL,
   name VARCHAR2(50),
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT psp_pk PRIMARY KEY (id)
);
GRANT ALL ON System_Ownr.PSP_Tbl TO mpnt_ownr;
GRANT SELECT ON system_ownr.PSP_Tbl TO mpnt_user;
CREATE SEQUENCE system_ownr.psp_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = system_ownr;
CREATE OR REPLACE TRIGGER insert_psp_trg BEFORE INSERT ON system_ownr.psp_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT system_ownr.psp_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_psp_trg BEFORE UPDATE ON system_ownr.psp_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
CREATE UNIQUE INDEX psp_uq ON system_ownr.psp_tbl (Lower(name) );

CREATE TABLE system_ownr.pspcard_tbl 
(
   id NUMBER(10,0) NOT NULL,
   cardid NUMBER(10,0) NOT NULL,
   pspid NUMBER(10,0) NOT NULL,
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT pspcard_pk PRIMARY KEY (id),
   CONSTRAINT pspcard2country_fk FOREIGN KEY(cardid) REFERENCES card_tbl(id) ON DELETE CASCADE,
   CONSTRAINT pspcard2psp_fk FOREIGN KEY(pspid) REFERENCES psp_tbl(id) ON DELETE CASCADE,
   CONSTRAINT pspcard_uq UNIQUE (cardid, pspid)
);
GRANT ALL ON System_Ownr.PSPCard_Tbl TO mpnt_ownr;
GRANT SELECT ON system_ownr.PSPCard_Tbl TO mpnt_user;
CREATE SEQUENCE system_ownr.pspcard_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = system_ownr;
CREATE OR REPLACE TRIGGER insert_pspcard_trg BEFORE INSERT ON system_ownr.pspcard_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT system_ownr.pspcard_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_pspcard_trg BEFORE UPDATE ON system_ownr.pspcard_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/

CREATE TABLE system_ownr.pspcurrency_tbl 
(
   id NUMBER(10,0) NOT NULL,
   countryid NUMBER(10,0) NOT NULL,
   pspid NUMBER(10,0) NOT NULL,
   name CHAR(3),
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT pspcurrency_pk PRIMARY KEY (id),
   CONSTRAINT pspcurrency2country_fk FOREIGN KEY(countryid) REFERENCES country_tbl(id) ON DELETE CASCADE,
   CONSTRAINT pspcurrency2psp_fk FOREIGN KEY(pspid) REFERENCES psp_tbl(id) ON DELETE CASCADE,
   CONSTRAINT pspcurrency_uq UNIQUE (countryid, pspid)
);
GRANT ALL ON System_Ownr.PSPCurrency_Tbl TO mpnt_ownr;
GRANT SELECT ON system_ownr.PSPCurrency_Tbl TO mpnt_user;
CREATE SEQUENCE system_ownr.pspcurrency_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = system_ownr;
CREATE OR REPLACE TRIGGER insert_pspcurrency_trg BEFORE INSERT ON system_ownr.pspcurrency_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT system_ownr.pspcurrency_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_pspcurrency_trg BEFORE UPDATE ON system_ownr.pspcurrency_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/

CREATE TABLE system_ownr.depositoption_tbl 
(
   id NUMBER(10,0) NOT NULL,
   countryid NUMBER(10,0) NOT NULL,
   amount NUMBER(10,0),
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT depositoption_pk PRIMARY KEY (id),
   CONSTRAINT depositoption2country_fk FOREIGN KEY(countryid) REFERENCES country_tbl(id),
   CONSTRAINT depositoption_uq UNIQUE (countryid, amount)
);
GRANT ALL ON System_Ownr.DepositOption_Tbl TO mpnt_ownr;
GRANT SELECT ON system_ownr.DepositOption_Tbl TO mpnt_user;
CREATE SEQUENCE system_ownr.depositoption_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = system_ownr;
CREATE OR REPLACE TRIGGER insert_depositoption_trg BEFORE INSERT ON system_ownr.depositoption_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT system_ownr.depositoption_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_depositoption_trg BEFORE UPDATE ON system_ownr.depositoption_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/

CREATE TABLE system_ownr.flow_tbl 
(
   id NUMBER(10,0) NOT NULL,
   name VARCHAR2(50),
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT flow_pk PRIMARY KEY (id)
);
GRANT ALL ON System_Ownr.flow_tbl TO mpnt_ownr;
GRANT SELECT ON system_ownr.flow_tbl TO mpnt_user;
CREATE SEQUENCE system_ownr.flow_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = system_ownr;
CREATE OR REPLACE TRIGGER insert_flow_trg BEFORE INSERT ON system_ownr.flow_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT system_ownr.flow_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_flow_trg BEFORE UPDATE ON system_ownr.flow_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
CREATE UNIQUE INDEX flow_uq ON system_ownr.flow_tbl (Lower(name) );


CREATE TABLE system_ownr.URLType_tbl 
(
   id NUMBER(10,0) NOT NULL,
   name VARCHAR2(50),
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT URLType_pk PRIMARY KEY (id)
);
GRANT ALL ON System_Ownr.URLType_tbl TO mpnt_ownr;
GRANT SELECT ON system_ownr.URLType_tbl TO mpnt_user;
CREATE SEQUENCE system_ownr.URLType_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = system_ownr;
CREATE OR REPLACE TRIGGER insert_URLType_trg BEFORE INSERT ON system_ownr.URLType_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT system_ownr.URLType_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_URLType_trg BEFORE UPDATE ON system_ownr.URLType_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
CREATE UNIQUE INDEX URLType_uq ON system_ownr.URLType_tbl (Lower(name) );


-- Table: System_Ownr.State_Tbl
-- Data table for all States in a Country
CREATE TABLE System_Ownr.State_Tbl
(
	id 			NUMBER(10,0) NOT NULL,
	countryid 	NUMBER(10,0) NOT NULL,
  	
	name 		VARCHAR2(50),
	code 		VARCHAR2(5),
	
	vat			NUMBER DEFAULT 0.0,	-- VAT charged in the State
		
	created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
	
	CONSTRAINT State_PK PRIMARY KEY (id),
	CONSTRAINT State2Country_FK FOREIGN KEY (countryid) REFERENCES System_Ownr.Country_Tbl ON DELETE CASCADE
);

CREATE UNIQUE INDEX State_UQ ON System_Ownr.State_Tbl (countryid, Upper(code) );

GRANT ALL ON System_Ownr.State_Tbl TO mpnt_ownr;
GRANT SELECT ON System_Ownr.State_Tbl TO mpnt_user;
CREATE SEQUENCE System_Ownr.State_Tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = System_Ownr;
CREATE OR REPLACE TRIGGER insert_State_trg BEFORE INSERT ON System_Ownr.State_Tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT System_Ownr.State_Tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_State_trg BEFORE UPDATE ON System_Ownr.State_Tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/


-- Table: System_Ownr.PostalCode_Tbl
-- Data table for all Postal Codes in a State
CREATE TABLE System_Ownr.PostalCode_Tbl
(
	id 			NUMBER(10,0) NOT NULL,
	stateid 	NUMBER(10,0) NOT NULL,
	
  	code 		VARCHAR2(10),
	city 		VARCHAR2(50),
	
	latitude 	NUMBER,
	longitude 	NUMBER,
	utc_offset 	NUMBER(10,0),
	
	vat			NUMBER DEFAULT 0.0,	-- VAT charged in the Postal Code
		
	created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
	
	CONSTRAINT PostalCode_PK PRIMARY KEY (id),
	CONSTRAINT PostalCode2State_FK FOREIGN KEY (stateid) REFERENCES System_Ownr.State_Tbl ON DELETE CASCADE
);

CREATE UNIQUE INDEX PostalCode_UQ ON System_Ownr.PostalCode_Tbl (latitude, longitude, code, lower(city) );
  
GRANT ALL ON System_Ownr.PostalCode_Tbl TO mpnt_ownr;
GRANT SELECT ON System_Ownr.PostalCode_Tbl TO mpnt_user;
CREATE SEQUENCE System_Ownr.PostalCode_Tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = System_Ownr;
CREATE OR REPLACE TRIGGER insert_PostalCode_trg BEFORE INSERT ON System_Ownr.PostalCode_Tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT System_Ownr.PostalCode_Tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_PostalCode_trg BEFORE UPDATE ON System_Ownr.PostalCode_Tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
/* ========== SYSTEM SCHEMA END ========== */

/* ========== CLIENT SCHEMA START ========== */
--CREATE USER client IDENTIFIED BY client_ownr;
--GRANT UNLIMITED TABLESPACE TO client_ownr;

GRANT REFERENCES, UPDATE ON system_ownr.country_tbl TO client_ownr;
GRANT REFERENCES, UPDATE ON system_ownr.flow_tbl TO client_ownr;

CONNECT client_ownr/client_ownr;
CREATE TABLE client_ownr.client_tbl 
(
   id NUMBER(10,0) NOT NULL,
   countryid NUMBER(10,0) NOT NULL,
   flowid NUMBER(10,0) NOT NULL,
   name VARCHAR2(50),
   username VARCHAR2(50),
   passwd VARCHAR2(50),
   logourl VARCHAR2(255),
   cssurl VARCHAR2(255),
   callbackurl VARCHAR2(255),
   accepturl VARCHAR2(255),
   cancelurl VARCHAR2(255),
   maxamount NUMBER(10,0),
   lang CHAR(2) DEFAULT 'gb',
   smsrcpt NUMBER(1,0) DEFAULT 1,
   emailrcpt NUMBER(1,0) DEFAULT 1,
   method VARCHAR2(6) DEFAULT 'mPoint',
   terms VARCHAR2(4000),
   "mode" NUMBER(10,0) DEFAULT 0,
   auto_capture NUMBER(1,0) DEFAULT 1,
   send_pspid NUMBER(1,0) DEFAULT 1,
   store_card NUMBER(10,0) DEFAULT 0,
   iconurl VARCHAR2(255),
   show_all_cards CHAR(1) DEFAULT '0' CHECK (show_all_cards IN ('0','1') ),
   
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT client_pk PRIMARY KEY (id),
   CONSTRAINT client2country_fk FOREIGN KEY (countryid) REFERENCES system_ownr.country_tbl(id),
   CONSTRAINT client2flow_fk FOREIGN KEY (flowid) REFERENCES system_ownr.flow_tbl(id),
   CONSTRAINT client_chk CHECK (method = 'mPoint' OR method = 'PSP'),
   CONSTRAINT storecard_chk CHECK (store_card = 0 OR (store_card >= 2 AND store_card <= 3) )
);
GRANT ALL ON client_ownr.Client_Tbl TO mpnt_ownr;
GRANT SELECT ON client_ownr.Client_Tbl TO mpnt_user;
CREATE SEQUENCE client_ownr.client_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = client_ownr;
CREATE OR REPLACE TRIGGER insert_client_trg BEFORE INSERT ON client_ownr.client_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT client_ownr.client_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_client_trg BEFORE UPDATE ON client_ownr.client_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/

CREATE TABLE client_ownr.account_tbl 
(
   id NUMBER(10,0) NOT NULL,
   clientid NUMBER(10,0) NOT NULL,
   name VARCHAR2(50),
   mobile NUMBER(15,0),
   markup VARCHAR2(5),
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT account_pk PRIMARY KEY (id),
   CONSTRAINT account2client_fk FOREIGN KEY (clientid) REFERENCES client_ownr.client_tbl(id) ON DELETE CASCADE
);
GRANT ALL ON client_ownr.Account_Tbl TO mpnt_ownr;
GRANT SELECT ON client_ownr.Account_Tbl TO mpnt_user;
CREATE SEQUENCE client_ownr.account_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = client_ownr;
CREATE OR REPLACE TRIGGER insert_account_trg BEFORE INSERT ON client_ownr.account_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT client_ownr.account_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_account_trg BEFORE UPDATE ON client_ownr.account_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
CREATE UNIQUE INDEX accountname_uq ON account_tbl (clientid, Lower(name) );

CONNECT system_ownr/system_ownr;
GRANT REFERENCES, UPDATE ON system_ownr.card_tbl TO client_ownr;
CONNECT client_ownr/client_ownr;
CREATE TABLE client_ownr.cardaccess_tbl 
(
   id NUMBER(10,0) NOT NULL,
   clientid NUMBER(10,0) NOT NULL,
   cardid NUMBER(10,0) NOT NULL,
   pspid NUMBER(10,0) NOT NULL,
   countryid NUMBER(10,0),
   
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT cardaccess_pk PRIMARY KEY (id),
   CONSTRAINT cardaccess2card_fk FOREIGN KEY (cardid) REFERENCES system_ownr.card_tbl(id) ON DELETE CASCADE,
   CONSTRAINT cardaccess2client_fk FOREIGN KEY (clientid) REFERENCES client_tbl(id) ON DELETE CASCADE,
   CONSTRAINT cardaccess2psp_fk FOREIGN KEY (pspid) REFERENCES system_ownr.card_tbl(id) ON DELETE CASCADE,
   CONSTRAINT cardaccess2country_fk FOREIGN KEY (countryid) REFERENCES system_ownr.country_tbl(id) ON DELETE CASCADE,
   CONSTRAINT cardaccess_uq UNIQUE (clientid, cardid, pspid, countryid)
);
GRANT ALL ON client_ownr.CardAccess_Tbl TO mpnt_ownr;
GRANT SELECT ON client_ownr.CardAccess_Tbl TO mpnt_user;
CREATE SEQUENCE client_ownr.cardaccess_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = client_ownr;
CREATE OR REPLACE TRIGGER insert_cardaccess_trg BEFORE INSERT ON client_ownr.cardaccess_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT client_ownr.cardaccess_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_cardaccess_trg BEFORE UPDATE ON client_ownr.cardaccess_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
 	
CREATE TABLE client_ownr.keyword_tbl 
(
   id NUMBER(10,0) NOT NULL,
   clientid NUMBER(10,0) NOT NULL,
   name VARCHAR2(50),
   standard NUMBER(1,0) DEFAULT 0,
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT keyword_pk PRIMARY KEY (id),
   CONSTRAINT keyword2client_fk FOREIGN KEY (clientid) REFERENCES client_ownr.client_tbl(id) ON DELETE CASCADE
);
GRANT ALL ON client_ownr.Keyword_Tbl TO mpnt_ownr;
GRANT SELECT ON client_ownr.Keyword_Tbl TO mpnt_user;
CREATE SEQUENCE client_ownr.keyword_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = client_ownr;
CREATE OR REPLACE TRIGGER insert_keyword_trg BEFORE INSERT ON client_ownr.keyword_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT client_ownr.keyword_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_keyword_trg BEFORE UPDATE ON client_ownr.keyword_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
CREATE UNIQUE INDEX keyword_uq ON keyword_tbl (clientid, Lower(name) );


-- Table: Client_Ownr.IpAddress_Tbl 
-- Used for IP WhiteListing 
CREATE TABLE Client_Ownr.IPAddress_Tbl
(
	id				NUMBER(10,0),
	clientid		NUMBER(10,0) NOT NULL,
	
	ipaddress		VARCHAR2(20),
	
	created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
	CONSTRAINT IPAddress_PK PRIMARY KEY (id),
	CONSTRAINT IPAccess2Client_FK FOREIGN KEY (clientid) REFERENCES Client_Ownr.Client_Tbl ON DELETE CASCADE
);

GRANT ALL ON Client_Ownr.IPAddress_Tbl TO mpnt_ownr;
GRANT SELECT ON Client_Ownr.IPAddress_Tbl TO mpnt_user;
CREATE SEQUENCE Client_Ownr.IPAddress_Tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = Client_Ownr;
CREATE OR REPLACE TRIGGER insert_IPAddress_trg BEFORE INSERT ON Client_Ownr.IPAddress_Tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT Client_Ownr.IPAddress_Tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_IPAddress_trg BEFORE UPDATE ON Client_Ownr.IPAddress_Tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
/* ==================== CLIENT SCHEMA END ==================== */

CONNECT system_ownr/system_ownr;
GRANT REFERENCES, UPDATE ON system_ownr.psp_tbl TO client_ownr;
CONNECT client_ownr/client_ownr;
CREATE TABLE client_ownr.merchantaccount_tbl 
(
   id NUMBER(10,0) NOT NULL,
   clientid NUMBER(10,0) NOT NULL,
   pspid NUMBER(10,0) NOT NULL,
   name VARCHAR2(50),
   username VARCHAR2(50),
   passwd VARCHAR2(50),
   stored_card CHAR(1) CHECK (stored_card IN ('0','1') ),
   
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT merchantaccount_pk PRIMARY KEY (id),
   CONSTRAINT merchantaccount2client_fk FOREIGN KEY (clientid) REFERENCES client_ownr.client_tbl(id) ON DELETE CASCADE,
   CONSTRAINT merchantaccount2psp_fk FOREIGN KEY (pspid) REFERENCES system_ownr.psp_tbl(id) ON DELETE CASCADE,
   CONSTRAINT merchantaccount_uq UNIQUE (clientid, pspid)
);
GRANT ALL ON client_ownr.MerchantAccount_Tbl TO mpnt_ownr;
GRANT SELECT ON client_ownr.MerchantAccount_Tbl TO mpnt_user;
CREATE SEQUENCE client_ownr.merchantaccount_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = client_ownr;
CREATE OR REPLACE TRIGGER insert_merchantaccount_trg BEFORE INSERT ON client_ownr.merchantaccount_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT client_ownr.merchantaccount_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_merchantaccount_trg BEFORE UPDATE ON client_ownr.merchantaccount_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/

CREATE TABLE client_ownr.merchantsubaccount_tbl 
(
   id NUMBER(10,0) NOT NULL,
   accountid NUMBER(10,0) NOT NULL,
   pspid NUMBER(10,0) NOT NULL,
   name VARCHAR2(50),
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT merchantsubaccount_pk PRIMARY KEY (id),
   CONSTRAINT merchantsubaccount2account_fk FOREIGN KEY (accountid) REFERENCES client_ownr.account_tbl(id) ON DELETE CASCADE,
   CONSTRAINT merchantsubaccount2psp_fk FOREIGN KEY (pspid) REFERENCES system_ownr.psp_tbl(id) ON DELETE CASCADE,
   CONSTRAINT merchantsubaccount_uq UNIQUE (accountid, pspid)
);
GRANT ALL ON client_ownr.MerchantSubAccount_Tbl TO mpnt_ownr;
GRANT SELECT ON client_ownr.MerchantSubAccount_Tbl TO mpnt_user;
CREATE SEQUENCE client_ownr.merchantsubaccount_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = client_ownr;
CREATE OR REPLACE TRIGGER insert_merchantsubaccount_trg BEFORE INSERT ON client_ownr.merchantsubaccount_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT client_ownr.merchantsubaccount_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_merchantsubaccount_trg BEFORE UPDATE ON client_ownr.merchantsubaccount_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
 	
CREATE TABLE client_ownr.surepay_tbl 
(
   id NUMBER(10,0) NOT NULL,
   clientid NUMBER(10,0),
   resend NUMBER(10,0),
   notify NUMBER(10,0),
   email VARCHAR2(50),
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT surepay_pk PRIMARY KEY (id),
   CONSTRAINT surepay2client_fk FOREIGN KEY (clientid) REFERENCES client_ownr.client_tbl(id) ON DELETE CASCADE,
   CONSTRAINT surepay_uq UNIQUE (clientid)
);
GRANT ALL ON client_ownr.SurePay_Tbl TO mpnt_ownr;
GRANT SELECT ON client_ownr.SurePay_Tbl TO mpnt_user;
CREATE SEQUENCE client_ownr.surepay_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = client_ownr;
CREATE OR REPLACE TRIGGER insert_surepay_trg BEFORE INSERT ON client_ownr.surepay_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT client_ownr.surepay_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_surepay_trg BEFORE UPDATE ON client_ownr.surepay_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/

CREATE TABLE client_ownr.product_tbl 
(
   id NUMBER(10,0) NOT NULL,
   keywordid NUMBER(10,0) NOT NULL,
   name VARCHAR2(50),
   quantity NUMBER(10,0) DEFAULT 1,
   price NUMBER(10,0),
   logourl VARCHAR2(255),
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT product_pk PRIMARY KEY (id),
   CONSTRAINT product2keyword_fk FOREIGN KEY (keywordid) REFERENCES client_ownr.keyword_tbl(id) ON DELETE CASCADE
);
GRANT ALL ON client_ownr.Product_Tbl TO mpnt_ownr;
GRANT SELECT ON client_ownr.Product_Tbl TO mpnt_user;
CREATE SEQUENCE client_ownr.product_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = client_ownr;
CREATE OR REPLACE TRIGGER insert_product_trg BEFORE INSERT ON client_ownr.product_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT client_ownr.product_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_product_trg BEFORE UPDATE ON client_ownr.product_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/

CREATE TABLE client_ownr.shop_tbl 
(
   id NUMBER(10,0) NOT NULL,
   clientid NUMBER(10,0) NOT NULL,
   keywordid NUMBER(10,0) NOT NULL,
   del_date NUMBER(1,0),
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT shop_pk PRIMARY KEY (id),
   CONSTRAINT shop2client_fk FOREIGN KEY (clientid) REFERENCES client_ownr.client_tbl(id) ON DELETE CASCADE,
   CONSTRAINT shop2keyword_fk FOREIGN KEY (keywordid) REFERENCES client_ownr.keyword_tbl(id) ON DELETE CASCADE
);
GRANT ALL ON client_ownr.Shop_Tbl TO mpnt_ownr;
GRANT SELECT ON client_ownr.Shop_Tbl TO mpnt_user;
CREATE SEQUENCE client_ownr.shop_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = client_ownr;
CREATE OR REPLACE TRIGGER insert_shop_trg BEFORE INSERT ON client_ownr.shop_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT client_ownr.shop_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_shop_trg BEFORE UPDATE ON client_ownr.shop_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
CONNECT system_ownr/system_ownr;
GRANT REFERENCES, UPDATE ON system_ownr.shipping_tbl TO client_ownr;
CONNECT client_ownr/client_ownr;
CREATE TABLE client_ownr.shipping_tbl 
(
   id NUMBER(10,0) NOT NULL,
   shippingid NUMBER(10,0) NOT NULL,
   shopid NUMBER(10,0) NOT NULL,
   cost NUMBER(10,0),
   free_ship NUMBER(10,0),
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT shipping_pk PRIMARY KEY (id),   
   CONSTRAINT shipping2shipping_fk FOREIGN KEY (shippingid) REFERENCES system_ownr.shipping_tbl(id) ON DELETE CASCADE,
   CONSTRAINT shipping2shop_fk FOREIGN KEY (shopid) REFERENCES client_ownr.shop_tbl(id) ON DELETE CASCADE
);
GRANT ALL ON client_ownr.Shipping_Tbl TO mpnt_ownr;
GRANT SELECT ON client_ownr.Shipping_Tbl TO mpnt_user;
CREATE SEQUENCE client_ownr.shipping_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = client_ownr;
CREATE OR REPLACE TRIGGER insert_shipping_trg BEFORE INSERT ON client_ownr.shipping_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT client_ownr.shipping_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_shipping_trg BEFORE UPDATE ON client_ownr.shipping_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/

CONNECT system_ownr/system_ownr;
GRANT REFERENCES, UPDATE ON system_ownr.URLType_Tbl TO client_ownr;
CONNECT client_ownr/client_ownr;
-- Table: Client_Ownr.URL_Tbl
-- Data table for all URLs that mRetail may use to contact external systems on the Client's behalf
CREATE TABLE Client_Ownr.URL_Tbl
(
	id 			NUMBER(10,0) NOT NULL,
	urltypeid	NUMBER(10,0) NOT NULL,	-- ID of the URL Type
	clientid	NUMBER(10,0) NOT NULL,	-- ID of the Client who owns the URL

	url			VARCHAR2(255),
	
	created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),

	CONSTRAINT URL_PK PRIMARY KEY (id),
	CONSTRAINT URL2URLType_FK FOREIGN KEY (urltypeid) REFERENCES System_Ownr.URLType_Tbl ON DELETE CASCADE,
	CONSTRAINT URL2Client_FK FOREIGN KEY (clientid) REFERENCES Client_Ownr.Client_Tbl ON DELETE CASCADE,
	CONSTRAINT URL_UQ UNIQUE (urltypeid, clientid)
);

GRANT ALL ON client_ownr.URL_Tbl TO mpnt_ownr;
GRANT SELECT ON client_ownr.URL_Tbl TO mpnt_user;
CREATE SEQUENCE client_ownr.URL_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = client_ownr;
CREATE OR REPLACE TRIGGER insert_URL_trg BEFORE INSERT ON client_ownr.URL_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT client_ownr.URL_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_URL_trg BEFORE UPDATE ON client_ownr.URL_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
CREATE UNIQUE INDEX Client_URL_UQ ON Client_Ownr.URL_Tbl (clientid, Lower(url) );
/* ========== CLIENT SCHEMA END ========== */

/* ========== ADMIN SCHEMA START ========== */
--CREATE USER admin IDENTIFIED BY admin_ownr;
--GRANT UNLIMITED TABLESPACE TO admin_ownr;
CONNECT system_ownr/system_ownr;
GRANT REFERENCES, UPDATE ON system_ownr.country_tbl TO admin_ownr;

CONNECT admin_ownr/admin_ownr;
CREATE TABLE admin_ownr.user_tbl 
(
   id NUMBER(10,0) NOT NULL,
   countryid NUMBER(10,0) NOT NULL,
   firstname VARCHAR2(50),
   lastname VARCHAR2(50),
   mobile NUMBER(15,0),
   email VARCHAR2(50),
   username VARCHAR2(50),
   passwd VARCHAR2(50),
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT user_pk PRIMARY KEY (id),
   CONSTRAINT user2country_fk FOREIGN KEY (countryid) REFERENCES system_ownr.country_tbl(id) ON DELETE CASCADE,
   CONSTRAINT user_mobile_uq UNIQUE (countryid, mobile)
);
GRANT ALL ON admin_ownr.User_Tbl TO mpnt_ownr;
GRANT SELECT ON admin_ownr.User_Tbl TO mpnt_user;
CREATE SEQUENCE admin_ownr.user_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = admin_ownr;
CREATE OR REPLACE TRIGGER insert_user_trg BEFORE INSERT ON admin_ownr.user_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT admin_ownr.user_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_user_trg BEFORE UPDATE ON admin_ownr.user_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
CREATE UNIQUE INDEX user_email_uq ON admin_ownr.user_tbl (countryid, Lower(email) );
CREATE UNIQUE INDEX user_username_uq ON admin_ownr.user_tbl (Lower(username)) ;

CONNECT client_ownr/client_ownr;
GRANT REFERENCES, UPDATE ON client_ownr.client_tbl TO admin_ownr;
CONNECT admin_ownr/admin_ownr;
CREATE TABLE admin_ownr.access_tbl 
(
   id NUMBER(10,0) NOT NULL,
   userid NUMBER(10,0) NOT NULL,
   clientid NUMBER(10,0) NOT NULL,
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT access_pk PRIMARY KEY (id),
   CONSTRAINT access2client_fk FOREIGN KEY (clientid) REFERENCES client_ownr.client_tbl(id) ON DELETE CASCADE,
   CONSTRAINT access2user_fk FOREIGN KEY (userid) REFERENCES admin_ownr.user_tbl(id) ON DELETE CASCADE,
   CONSTRAINT access_uq UNIQUE (userid, clientid)
);
GRANT ALL ON admin_ownr.Access_tbl TO mpnt_ownr;
GRANT SELECT ON admin_ownr.Access_tbl TO mpnt_user;
CREATE SEQUENCE admin_ownr.access_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = admin_ownr;
CREATE OR REPLACE TRIGGER insert_access_trg BEFORE INSERT ON admin_ownr.access_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT admin_ownr.access_tbl_id_seq.nextval INTO :NEW.id FROM dual;
			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_access_trg BEFORE UPDATE ON admin_ownr.access_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/


-- Table: Admin_Ownr.Role_Tbl
-- Data table for the available Roles
CREATE TABLE Admin_Ownr.Role_Tbl
(
	id			NUMBER(10,0) NOT NULL,
	
	name		VARCHAR2(100),		-- Name of Role
	assignable	CHAR(1) DEFAULT '1' CHECK (assignable IN ('0','1') ),	-- Flag indicating whether users may be assigned to this role using the Web Interface
	note		CLOB,				-- Description of Role
	
	created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
	
	CONSTRAINT Role_PK PRIMARY KEY (id)
);

CREATE UNIQUE INDEX Role_UQ ON Admin_Ownr.Role_Tbl (Lower(name) );

GRANT ALL ON admin_ownr.Role_tbl TO mpnt_ownr;
GRANT SELECT ON admin_ownr.Role_tbl TO mpnt_user;
CREATE SEQUENCE admin_ownr.Role_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = admin_ownr;
CREATE OR REPLACE TRIGGER insert_Role_trg BEFORE INSERT ON admin_ownr.Role_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT admin_ownr.Role_tbl_id_seq.nextval INTO :NEW.id FROM dual;
			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_Role_trg BEFORE UPDATE ON admin_ownr.Role_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/


-- Table: Admin_Ownr.RoleInfo_Tbl
-- Data table for the available User RoleInfos
CREATE TABLE Admin_Ownr.RoleInfo_Tbl
(
	id			NUMBER(10,0) NOT NULL,
	roleid		NUMBER(10,0) NOT NULL,
	languageid	NUMBER(10,0) NOT NULL,
	
	name		VARCHAR2(100),		-- Translated name for the role
	note		CLOB,				-- Translated description for the role
	
	created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
	
	CONSTRAINT RoleInfo_PK PRIMARY KEY (id),
	CONSTRAINT RoleInfo_UQ UNIQUE (roleid, languageid)
);

GRANT ALL ON admin_ownr.RoleInfo_tbl TO mpnt_ownr;
GRANT SELECT ON admin_ownr.RoleInfo_tbl TO mpnt_user;
CREATE SEQUENCE admin_ownr.RoleInfo_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = admin_ownr;
CREATE OR REPLACE TRIGGER insert_RoleInfo_trg BEFORE INSERT ON admin_ownr.RoleInfo_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT admin_ownr.RoleInfo_tbl_id_seq.nextval INTO :NEW.id FROM dual;
			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_RoleInfo_trg BEFORE UPDATE ON admin_ownr.RoleInfo_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/


-- Table: Admin_Ownr.Role_Tbl
-- Link table for assigning a user to one or more roles
CREATE TABLE Admin_Ownr.RoleAccess_Tbl
(
	id			NUMBER(10,0) NOT NULL,
	roleid		NUMBER(10,0) NOT NULL,
	userid		NUMBER(10,0) NOT NULL,
	
	created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
		
	CONSTRAINT RoleAccess_PK PRIMARY KEY (id),
	CONSTRAINT RoleAccess2Role_FK FOREIGN KEY (roleid) REFERENCES Admin_Ownr.Role_Tbl (id) ON DELETE CASCADE,
	CONSTRAINT RoleAccess2User_FK FOREIGN KEY (userid) REFERENCES Admin_Ownr.User_Tbl (id) ON DELETE CASCADE,
	CONSTRAINT RoleAccess_UQ UNIQUE (roleid, userid)
);

GRANT ALL ON admin_ownr.RoleAccess_tbl TO mpnt_ownr;
GRANT SELECT ON admin_ownr.RoleAccess_tbl TO mpnt_user;
CREATE SEQUENCE admin_ownr.RoleAccess_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = admin_ownr;
CREATE OR REPLACE TRIGGER insert_RoleAccess_trg BEFORE INSERT ON admin_ownr.RoleAccess_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT admin_ownr.RoleAccess_tbl_id_seq.nextval INTO :NEW.id FROM dual;
			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_RoleAccess_trg BEFORE UPDATE ON admin_ownr.RoleAccess_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
/* ========== ADMIN SCHEMA END ========== */

/* ========== END-USER SCHEMA START ========== */
--CREATE USER enduser IDENTIFIED BY enduser_ownr;
--GRANT UNLIMITED TABLESPACE TO enduser_ownr;

CONNECT system_ownr/system_ownr;
GRANT REFERENCES, UPDATE ON system_ownr.country_tbl TO enduser_ownr;

CONNECT enduser_ownr/enduser_ownr;
CREATE TABLE enduser_ownr.account_tbl 
(
   id NUMBER(10,0) NOT NULL,
   countryid NUMBER(10,0) NOT NULL,
   firstname VARCHAR2(50),
   lastname VARCHAR2(50),
   mobile NUMBER(15,0),
   email VARCHAR2(50),
   passwd VARCHAR2(50),
   balance NUMBER(10,0) DEFAULT 0,
   attempts NUMBER(10,0) DEFAULT 0,
   points NUMBER(10,0) DEFAULT 0,
   mobile_verified CHAR(1) DEFAULT '0' CHECK (mobile_verified IN ('0','1') ),
   externalid VARCHAR2(50),
   
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT account_pk PRIMARY KEY (id),
   CONSTRAINT account2country_fk FOREIGN KEY(countryid) REFERENCES system_ownr.country_tbl(id) ON DELETE CASCADE
);
GRANT ALL ON enduser_ownr.Account_Tbl TO mpnt_ownr;
GRANT SELECT, INSERT, UPDATE, DELETE ON enduser_ownr.Account_Tbl TO mpnt_user;
CREATE SEQUENCE enduser_ownr.account_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
GRANT ALL ON enduser_ownr.Account_Tbl_id_seq TO mpnt_ownr;
GRANT SELECT ON enduser_ownr.Account_Tbl_id_seq TO mpnt_user;
ALTER SESSION SET CURRENT_SCHEMA = enduser_ownr;
CREATE OR REPLACE TRIGGER insert_account_trg BEFORE INSERT ON enduser_ownr.account_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT enduser_ownr.account_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_account_trg BEFORE UPDATE ON enduser_ownr.account_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
CREATE INDEX account_email_idx ON enduser_ownr.account_tbl (countryid, Lower(email), enabled);
CREATE INDEX account_mobile_idx ON enduser_ownr.account_tbl (countryid, mobile, enabled);

CREATE TABLE enduser_ownr.activation_tbl 
(
   id NUMBER(10,0) NOT NULL,
   accountid NUMBER(10,0) NOT NULL,
   code NUMBER(10,0),
   address VARCHAR2(50),
   active NUMBER(1,0) DEFAULT 0,
   expiry TIMESTAMP(6),
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT activation_pk PRIMARY KEY (id),
   CONSTRAINT activation2account_fk FOREIGN KEY(accountid) REFERENCES enduser_ownr.account_tbl(id) ON DELETE CASCADE,
   CONSTRAINT activate_uq UNIQUE (accountid, code)
);
GRANT ALL ON enduser_ownr.Activation_Tbl TO mpnt_ownr;
GRANT SELECT, INSERT, UPDATE ON enduser_ownr.Activation_Tbl TO mpnt_user;
CREATE SEQUENCE enduser_ownr.activation_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = enduser_ownr;
CREATE OR REPLACE TRIGGER insert_activation_trg BEFORE INSERT ON enduser_ownr.activation_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT enduser_ownr.activation_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_activation_trg BEFORE UPDATE ON enduser_ownr.activation_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/

CONNECT system_ownr/system_ownr;
GRANT REFERENCES, UPDATE ON system_ownr.card_tbl TO enduser_ownr;
GRANT REFERENCES, UPDATE ON system_ownr.psp_tbl TO enduser_ownr;
CONNECT client_ownr/client_ownr;
GRANT REFERENCES, UPDATE ON client_ownr.client_tbl TO enduser_ownr;
CONNECT enduser_ownr/enduser_ownr;
CREATE TABLE enduser_ownr.card_tbl 
(
   id NUMBER(10,0) NOT NULL,
   accountid NUMBER(10,0) NOT NULL,
   cardid NUMBER(10,0) NOT NULL,
   pspid NUMBER(10,0) NOT NULL,
   ticket VARCHAR2(255),
   mask VARCHAR2(20),
   expiry VARCHAR2(5),
   preferred NUMBER(1,0) DEFAULT 0,
   clientid NUMBER(10,0),
   name VARCHAR2(50),
   card_holder_name VARCHAR2(255),
   
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT card_pk PRIMARY KEY (id),
   CONSTRAINT card2account_fk FOREIGN KEY(accountid) REFERENCES enduser_ownr.account_tbl(id) ON DELETE CASCADE,
   CONSTRAINT card2card_fk FOREIGN KEY(cardid) REFERENCES system_ownr.card_tbl(id),
   CONSTRAINT card2client_fk FOREIGN KEY(clientid) REFERENCES client_ownr.client_tbl(id) ON DELETE CASCADE,
   CONSTRAINT card2psp_fk FOREIGN KEY(pspid) REFERENCES system_ownr.psp_tbl(id),
   CONSTRAINT card_uq UNIQUE (accountid, clientid, cardid, mask, expiry),
   CONSTRAINT ticket_uq UNIQUE (accountid, ticket)
);
GRANT ALL ON enduser_ownr.Card_Tbl TO mpnt_ownr;
GRANT SELECT, INSERT, UPDATE, DELETE ON enduser_ownr.Card_Tbl TO mpnt_user;
CREATE SEQUENCE enduser_ownr.card_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = enduser_ownr;
CREATE OR REPLACE TRIGGER insert_card_trg BEFORE INSERT ON enduser_ownr.card_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT enduser_ownr.card_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_card_trg BEFORE UPDATE ON enduser_ownr.card_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/

CREATE TABLE enduser_ownr.claccess_tbl 
(
   id NUMBER(10,0) NOT NULL,
   clientid NUMBER(10,0) NOT NULL,
   accountid NUMBER(10,0) NOT NULL,
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT claccess_pk PRIMARY KEY (id),
   CONSTRAINT access2account_fk FOREIGN KEY(accountid) REFERENCES enduser_ownr.account_tbl(id) ON DELETE CASCADE,
   CONSTRAINT access2client_fk FOREIGN KEY(clientid) REFERENCES client_ownr.client_tbl(id) ON DELETE CASCADE
);
GRANT ALL ON enduser_ownr.CLAccess_Tbl TO mpnt_ownr;
GRANT SELECT, INSERT ON enduser_ownr.CLAccess_Tbl TO mpnt_user;
CREATE SEQUENCE enduser_ownr.claccess_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = enduser_ownr;
CREATE OR REPLACE TRIGGER insert_claccess_trg BEFORE INSERT ON enduser_ownr.claccess_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT enduser_ownr.claccess_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_claccess_trg BEFORE UPDATE ON enduser_ownr.claccess_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
CREATE INDEX claccess_account ON enduser_ownr.claccess_tbl (accountid);


CONNECT system_ownr/system_ownr;
GRANT REFERENCES, UPDATE ON system_ownr.Country_Tbl TO EndUser_Ownr;
GRANT REFERENCES, UPDATE ON system_ownr.State_Tbl TO EndUser_Ownr;
CONNECT enduser_ownr/enduser_ownr;
-- Table: EndUser_Ownr.Address_Tbl
-- Data table for all billing addresses registered by end-users
CREATE TABLE EndUser_Ownr.Address_Tbl
(
	id			NUMBER(10,0),
	accountid	NUMBER(10,0),
	cardid		NUMBER(10,0),
	countryid	NUMBER(10,0) NOT NULL,
	stateid		NUMBER(10,0) NOT NULL,
	
	firstname	VARCHAR2(50),
	lastname	VARCHAR2(50),
	company		VARCHAR2(50),
	street		VARCHAR2(255),
	postalcode	VARCHAR2(10),
	city		VARCHAR2(50),
		
	created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
	
	CONSTRAINT Address_PK PRIMARY KEY (id),
	CONSTRAINT Address2Account_FK FOREIGN KEY (accountid) REFERENCES EndUser_Ownr.Account_Tbl (id) ON DELETE CASCADE,
	CONSTRAINT Address2Card_FK FOREIGN KEY (cardid) REFERENCES EndUser_Ownr.Card_Tbl (id) ON DELETE CASCADE,
	CONSTRAINT Address2Country_FK FOREIGN KEY (countryid) REFERENCES System_Ownr.Country_Tbl (id) ON DELETE CASCADE,
	CONSTRAINT Address2State_FK FOREIGN KEY (stateid) REFERENCES System_Ownr.State_Tbl (id) ON DELETE CASCADE,
	CHECK ( (accountid IS NULL AND cardid IS NOT NULL) OR (accountid IS NOT NULL AND cardid IS NULL) )
);

GRANT ALL ON EndUser_Ownr.Address_Tbl TO mpnt_ownr;
GRANT SELECT, INSERT, UPDATE, DELETE ON EndUser_Ownr.Address_Tbl TO mpnt_user;
CREATE SEQUENCE EndUser_Ownr.Address_Tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = EndUser_Ownr;
CREATE OR REPLACE TRIGGER insert_Address_trg BEFORE INSERT ON EndUser_Ownr.Address_Tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT EndUser_Ownr.Address_Tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_Address_trg BEFORE UPDATE ON EndUser_Ownr.Address_Tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
/* ========== END-USER SCHEMA END ========== */
 	
/* ========== LOG SCHEMA START ========== */
--CREATE USER log IDENTIFIED BY log_ownr;
--GRANT UNLIMITED TABLESPACE TO log_ownr;

CONNECT client_ownr/client_ownr;
GRANT REFERENCES, UPDATE ON client_ownr.client_tbl TO log_ownr;
GRANT REFERENCES, UPDATE ON client_ownr.account_tbl TO log_ownr;
GRANT REFERENCES, UPDATE ON client_ownr.keyword_tbl TO log_ownr;
CONNECT system_ownr/system_ownr;
GRANT REFERENCES, UPDATE ON system_ownr.country_tbl TO log_ownr;
GRANT REFERENCES, UPDATE ON system_ownr.card_tbl TO log_ownr;
GRANT REFERENCES, UPDATE ON system_ownr.psp_tbl TO log_ownr;
GRANT REFERENCES, UPDATE ON system_ownr.type_tbl TO log_ownr;
CONNECT enduser_ownr/enduser_ownr;
GRANT REFERENCES, UPDATE ON enduser_ownr.account_tbl TO log_ownr;

CONNECT log_ownr/log_ownr;
CREATE TABLE log_ownr.transaction_tbl 
(
   id NUMBER(10,0) NOT NULL,
   typeid NUMBER(10,0) NOT NULL,
   clientid NUMBER(10,0) NOT NULL,
   accountid NUMBER(10,0) NOT NULL,
   countryid NUMBER(10,0) NOT NULL,
   pspid NUMBER(10,0),
   cardid NUMBER(10,0),
   keywordid NUMBER(10,0),
   amount NUMBER(10,0),
   orderid VARCHAR2(40),
   extid VARCHAR2(40),
   lang CHAR(2) DEFAULT 'gb',
   mobile NUMBER(15,0),
   operatorid NUMBER(10,0),
   logourl VARCHAR2(255),
   cssurl VARCHAR2(255),
   callbackurl VARCHAR2(255),
   accepturl VARCHAR2(255),
   cancelurl VARCHAR2(255),
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   "mode" NUMBER(10,0) DEFAULT 0,
   email VARCHAR2(50),
   gomobileid NUMBER(10,0) DEFAULT(-1),
   auto_capture NUMBER(1,0),
   euaid NUMBER(10,0),
   ip  VARCHAR2(255) NOT NULL,
   iconurl VARCHAR2(255),
   markup VARCHAR2(5),
   points NUMBER(10,0),
   reward NUMBER(10,0),
   refund NUMBER(10,0) DEFAULT 0,
   authurl VARCHAR2(255),
   customer_ref VARCHAR2(50),
   description CLOB,
   
   CONSTRAINT transaction_pk PRIMARY KEY (id),
   CONSTRAINT txn2account_fk FOREIGN KEY(accountid) REFERENCES client_ownr.account_tbl(id),
   CONSTRAINT txn2card_fk FOREIGN KEY(cardid) REFERENCES system_ownr.card_tbl(id),
   CONSTRAINT txn2client_fk FOREIGN KEY(clientid) REFERENCES client_ownr.client_tbl(id),
   CONSTRAINT txn2country_fk FOREIGN KEY(countryid) REFERENCES system_ownr.country_tbl(id),
   CONSTRAINT txn2eua_fk FOREIGN KEY(euaid) REFERENCES enduser_ownr.account_tbl(id),
   CONSTRAINT txn2keyword_fk FOREIGN KEY(keywordid) REFERENCES client_ownr.keyword_tbl(id),
   CONSTRAINT txn2psp_fk FOREIGN KEY(pspid) REFERENCES system_ownr.psp_tbl(id),
   CONSTRAINT txn2type_fk FOREIGN KEY(typeid) REFERENCES system_ownr.type_tbl(id)
);
GRANT ALL ON log_ownr.Transaction_Tbl TO mpnt_ownr;
GRANT SELECT, INSERT, UPDATE ON log_ownr.Transaction_Tbl TO mpnt_user;
CREATE SEQUENCE log_ownr.transaction_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
GRANT ALL ON log_ownr.transaction_tbl_id_seq TO mpnt_ownr;
GRANT SELECT ON log_ownr.transaction_tbl_id_seq TO mpnt_user;
ALTER SESSION SET CURRENT_SCHEMA = log_ownr;
CREATE OR REPLACE TRIGGER insert_transaction_trg BEFORE INSERT ON log_ownr.transaction_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT transaction_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_transaction_trg BEFORE UPDATE ON log_ownr.transaction_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
CREATE INDEX Transaction_Order_Idx ON Log_Ownr.Transaction_Tbl (orderid);
CREATE INDEX transaction_email_idx ON Log_Ownr.Transaction_Tbl (email);
CREATE INDEX transaction_mobile_idx ON Log_Ownr.Transaction_Tbl (mobile);
CREATE INDEX transaction_customer_ref_idx ON Log_Ownr.Transaction_Tbl (customer_ref);

CREATE TABLE log_ownr.state_tbl 
(
   id NUMBER(10,0) NOT NULL,
   name VARCHAR2(50),
   module VARCHAR2(255),
   func VARCHAR2(255),
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT state_pk PRIMARY KEY (id)
);
GRANT ALL ON log_ownr.state_tbl TO mpnt_ownr;
GRANT SELECT ON log_ownr.state_tbl TO mpnt_user;
CREATE SEQUENCE log_ownr.state_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = log_ownr;
CREATE OR REPLACE TRIGGER insert_state_trg BEFORE INSERT ON log_ownr.state_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT state_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_state_trg BEFORE UPDATE ON log_ownr.state_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
 	
CREATE TABLE log_ownr.message_tbl 
(
   id NUMBER(10,0) NOT NULL,
   txnid NUMBER(10,0) NOT NULL,
   stateid NUMBER(10,0) NOT NULL,
   data CLOB,
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT message_pk PRIMARY KEY (id),
   CONSTRAINT msg2state_fk FOREIGN KEY(stateid) REFERENCES log_ownr.state_tbl(id),
   CONSTRAINT msg2txn_fk FOREIGN KEY(txnid) REFERENCES log_ownr.transaction_tbl(id) ON DELETE CASCADE
);
GRANT ALL ON log_ownr.Message_Tbl TO mpnt_ownr;
GRANT SELECT, INSERT, UPDATE, DELETE ON log_ownr.Message_Tbl TO mpnt_user;
CREATE SEQUENCE log_ownr.message_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = log_ownr;
CREATE OR REPLACE TRIGGER insert_message_trg BEFORE INSERT ON log_ownr.message_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT message_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_message_trg BEFORE UPDATE ON log_ownr.message_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
CREATE INDEX message_transaction_state ON log_ownr.message_tbl (txnid, stateid);


-- Table: Log_Ownr.operation_tbl
-- Data table for operations such as saving a card, deleting a card, etc.
CREATE TABLE Log_Ownr.Operation_Tbl
(
	id			NUMBER(10,0) NOT NULL,
	name		VARCHAR2(255),
	
	created		TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	modified	TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	enabled		CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
	
	CONSTRAINT operation_pk PRIMARY KEY (id)
);

GRANT ALL ON log_ownr.Operation_Tbl TO mpnt_ownr;
GRANT SELECT, INSERT, UPDATE, DELETE ON log_ownr.Operation_Tbl TO mpnt_user;
CREATE SEQUENCE log_ownr.Operation_Tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = log_ownr;
CREATE OR REPLACE TRIGGER insert_Operation_trg BEFORE INSERT ON log_ownr.Operation_Tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT Operation_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_Operation_trg BEFORE UPDATE ON log_ownr.Operation_Tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/


-- Table: Log_Ownr.auditlog_tbl
-- Logs the activities such as saving a card, deleting a card, etc.
CREATE TABLE Log_Ownr.AuditLog_Tbl
(
	id				NUMBER(10,0) NOT NULL,
	operationid		NUMBER(10,0) NOT NULL,
	
	mobile			NUMBER(15,0),
	email			VARCHAR2(255),
	customer_ref	VARCHAR2(50),
	code			NUMBER(10,0) NOT NULL,
	message			VARCHAR2(255),
	
	created			TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	modified		TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	enabled			CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
	
  	CONSTRAINT auditlog_pk PRIMARY KEY (id),
  	CONSTRAINT auditlog2operation_fk FOREIGN KEY (operationid) REFERENCES Log_Ownr.operation_tbl (id) ON DELETE CASCADE
);
  
GRANT ALL ON log_ownr.AuditLog_Tbl TO mpnt_ownr;
GRANT SELECT, INSERT, UPDATE, DELETE ON log_ownr.AuditLog_Tbl TO mpnt_user;
CREATE SEQUENCE log_ownr.AuditLog_Tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = log_ownr;
CREATE OR REPLACE TRIGGER insert_AuditLog_trg BEFORE INSERT ON log_ownr.AuditLog_Tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT AuditLog_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_AuditLog_trg BEFORE UPDATE ON log_ownr.AuditLog_Tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
/* ========== LOG SCHEMA END ========== */

/* ========== END-USER SCHEMA START ========== */
CONNECT log_ownr/log_ownr;
GRANT REFERENCES, UPDATE ON log_ownr.transaction_tbl TO enduser_ownr;
GRANT REFERENCES, UPDATE ON log_ownr.state_tbl TO enduser_ownr;
CONNECT system_ownr/system_ownr;
GRANT REFERENCES, UPDATE ON system_ownr.type_tbl TO enduser_ownr;
CONNECT enduser_ownr/enduser_ownr;

CREATE TABLE enduser_ownr.transaction_tbl 
(
   id NUMBER(10,0) NOT NULL,
   accountid NUMBER(10,0) NOT NULL,
   typeid NUMBER(10,0) NOT NULL,
   fromid NUMBER(10,0),
   toid NUMBER(10,0),
   txnid NUMBER(10,0),
   stateid NUMBER(10,0) DEFAULT 1800,
   amount NUMBER(10,0),
   fee NUMBER(10,0) DEFAULT 0,
   ip VARCHAR2(255),
   address VARCHAR2(50),
   message CLOB,
   
   created TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   modified TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
   enabled CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
   
   CONSTRAINT transaction_pk PRIMARY KEY (id),
   CONSTRAINT txn2txn_fk FOREIGN KEY(txnid) REFERENCES log_ownr.transaction_tbl(id),
   CONSTRAINT txn2type_fk FOREIGN KEY(typeid) REFERENCES system_ownr.type_tbl(id),
   CONSTRAINT txnfrom2account_fk FOREIGN KEY(fromid) REFERENCES enduser_ownr.account_tbl(id),
   CONSTRAINT txnowner2account_fk FOREIGN KEY(accountid) REFERENCES enduser_ownr.account_tbl(id) ON DELETE CASCADE,
   CONSTRAINT txnto2account_fk FOREIGN KEY(toid) REFERENCES enduser_ownr.account_tbl(id),
   CONSTRAINT Transaction2State_FK FOREIGN KEY(stateid) REFERENCES log_ownr.State_Tbl(id),
   CONSTRAINT transaction_uq UNIQUE (typeid, txnid),
   CONSTRAINT transaction_chk CHECK ( (fromid IS NULL AND toid IS NULL) OR txnid IS NULL)
);
GRANT ALL ON enduser_ownr.Transaction_Tbl TO mpnt_ownr;
GRANT SELECT, INSERT, UPDATE ON enduser_ownr.Transaction_Tbl TO mpnt_user;
CREATE SEQUENCE enduser_ownr.transaction_tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = enduser_ownr;
CREATE OR REPLACE TRIGGER insert_transaction_trg BEFORE INSERT ON enduser_ownr.transaction_tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT enduser_ownr.transaction_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_transaction_trg BEFORE UPDATE ON enduser_ownr.transaction_tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
CREATE INDEX transaction_account_idx ON EndUser_Ownr.Transaction_Tbl (accountid, txnid);
/* ========== END-USER SCHEMA END ========== */

/* ========== LOG SCHEMA START ========== */
CONNECT enduser_ownr/enduser_ownr;
GRANT REFERENCES, UPDATE ON EndUser_Ownr.transaction_tbl TO log_ownr;
CONNECT admin_ownr/admin_ownr;
GRANT REFERENCES, UPDATE ON Admin_Ownr.user_tbl TO log_ownr;
CONNECT log_ownr/log_ownr;
-- Table: Log_Ownr.Note_Tbl
-- Data table for all notes related to a transaction
CREATE TABLE Log_Ownr.Note_Tbl
(
	id			NUMBER(10,0) NOT NULL,
	txnid		NUMBER(10,0) NOT NULL,
	userid		NUMBER(10,0) NOT NULL,
	
	message		CLOB,				--
	
	created		TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	modified	TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,
	enabled		CHAR(1) DEFAULT '1' CHECK (enabled IN ('0','1') ),
	
	CONSTRAINT Note_PK PRIMARY KEY (id),
	CONSTRAINT Note2Transaction_FK FOREIGN KEY (txnid) REFERENCES EndUser_Ownr.transaction_tbl (id) ON DELETE CASCADE,
	CONSTRAINT Note2User_FK FOREIGN KEY (userid) REFERENCES Admin_Ownr.user_tbl (id)
);

GRANT ALL ON log_ownr.Note_Tbl TO mpnt_ownr;
GRANT SELECT, INSERT, UPDATE, DELETE ON log_ownr.Note_Tbl TO mpnt_user;
CREATE SEQUENCE log_ownr.Note_Tbl_id_seq
    START WITH 1
	INCREMENT BY 1
	NOMINVALUE
	NOMAXVALUE
	NOCACHE;
ALTER SESSION SET CURRENT_SCHEMA = log_ownr;
CREATE OR REPLACE TRIGGER insert_Note_trg BEFORE INSERT ON log_ownr.Note_Tbl
	FOR EACH ROW
		BEGIN
			IF :NEW.id IS NULL THEN
 				SELECT Note_tbl_id_seq.nextval INTO :NEW.id FROM dual;
 			END IF;
 		END;
 	/
CREATE OR REPLACE TRIGGER update_Note_trg BEFORE UPDATE ON log_ownr.Note_Tbl
FOR EACH ROW
	BEGIN
		:NEW.modified := CURRENT_TIMESTAMP;
	END;
/
/* ========== LOG SCHEMA END ========== */

/* ========== STORED PROCEDURES START ========== */
ALTER SESSION SET CURRENT_SCHEMA = mpnt_ownr;
CONNECT mpnt_ownr/mpnt_ownr;
CREATE OR REPLACE FUNCTION Nextvalue ( sequence IN VARCHAR2) RETURN number
IS
	num NUMBER(10,0);
BEGIN
	EXECUTE IMMEDIATE 'SELECT '|| sequence ||'.nextval FROM dual' INTO num;
	
	RETURN num;
END;
/
GRANT EXECUTE ON mpnt_ownr.Nextvalue TO mpnt_user;

CREATE OR REPLACE PUBLIC SYNONYM Nextvalue FOR mpnt_ownr.Nextvalue;
/* ========== STORED PROCEDURES END ========== */
/*
CREATE FUNCTION const_date_proc(integer, integer, integer) RETURNS date
    LANGUAGE plpgsql
    AS $_$
DECLARE
	-- Declare aliases for input
	in_year ALIAS FOR $1;
	in_month ALIAS FOR $2;
	in_day ALIAS FOR $3;
BEGIN
	RETURN in_year || '-' || in_month || '-' || in_day;
END;
$_$;

CREATE FUNCTION modify_endusertxn_proc() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
	iAccountID NUMBER(10,0);
	iTypeID NUMBER(10,0);
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
		UPDATE enduser_ownr.Account_Tbl
		SET balance = (SELECT (Sum(amount) + Sum(Abs(fee) * -1) )
					   FROM enduser_ownr.Transaction_Tbl
					   WHERE accountid = iAccountID AND 1000 <= typeid AND typeid <= 1003 AND enabled = true)
		WHERE id = iAccountID;
	-- Update available balance on EndUser's loyalty account
	ELSIF 1004 <= iTypeID AND iTypeID <= 1007 THEN
		UPDATE enduser_ownr.Account_Tbl
		SET points = (SELECT (Sum(amount) + Sum(Abs(fee) * -1) )
					   FROM enduser_ownr.Transaction_Tbl
					   WHERE accountid = iAccountID AND 1004 <= typeid AND typeid <= 1007 AND enabled = true)
		WHERE id = iAccountID;
	END IF;
	
	IF TG_OP = 'DELETE' THEN
		RETURN OLD;
	ELSE
		NEW.Modified := NOW();
		RETURN NEW;
	END IF;
END;
$$;


CREATE FUNCTION modify_transfer_proc() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
	iAccountID NUMBER(10,0);
BEGIN
	IF TG_OP = 'DELETE' THEN
		iAccountID := OLD.accountid;
	ELSE
		iAccountID := NEW.accountid;
	END IF;
	
	-- Update available balance on EndUser's Account
	UPDATE enduser_ownr.Account_Tbl
	SET balance = (SELECT Sum(amount)
				   FROM enduser_ownr.Transfer_Tbl
				   WHERE accountid = iAccountID AND enabled = true)
	WHERE id = iAccountID;
	
	IF TG_OP = 'DELETE' THEN
		RETURN OLD;
	ELSE
		NEW.Modified := NOW();
		RETURN NEW;
	END IF;
END;
$$;

CREATE FUNCTION update_table_proc() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
	NEW.modified := NOW();

	RETURN NEW;
END;
$$;
*/