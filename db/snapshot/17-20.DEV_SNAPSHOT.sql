
 /*
 *
 * Created a new Table in the client schema {Client.AdditionalProperty_tbl} to retain additional client and merchant configuration
 * for every channel - CMP-1862
 *
 */
-- Table: client.additionalproperty_tbl

-- DROP TABLE client.additionalproperty_tbl;

CREATE TABLE client.additionalproperty_tbl
(
  id serial NOT NULL,
  key character varying(200) NOT NULL,
  value character varying(4000) NOT NULL,
  modified timestamp without time zone DEFAULT now(),
  created timestamp without time zone DEFAULT now(),
  enabled boolean NOT NULL DEFAULT true,
  externalid integer NOT NULL,
  type VARCHAR(20) NOT NULL,
  CONSTRAINT additionalprop_pk PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE client.additionalproperty_tbl
  OWNER TO mpoint;



/* Update process type 2's name from Bank to Acquirer*/

UPDATE system.processortype_tbl SET name = 'Acquirer' WHERE id = 2;

/* ========== CONFIGURE NETS START ========== */
/*START: Adding PSP entries to the PSP_Tbl table for NETS*/

INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (35, 'NETS',2);

/*END: Adding PSP entries to the PSP_Tbl table for NETS*/

/*START: Adding Currency entries to the PSPCurrency_Tbl table for NETS*/


/* Update process type 2's name from Bank to Acquirer*/

UPDATE system.processortype_tbl SET name = 'Acquirer' WHERE id = 2;

/* ========== CONFIGURE NETS START ========== */
/*START: Adding PSP entries to the PSP_Tbl table for NETS*/

INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (35, 'NETS',2);

/*END: Adding PSP entries to the PSP_Tbl table for NETS*/

/*START: Adding Currency entries to the PSPCurrency_Tbl table for NETS*/

INSERT INTO system.pspcurrency_tbl (countryid, pspid, name) VALUES (100,35,'DKK');
INSERT INTO system.pspcurrency_tbl (countryid, pspid, name) VALUES (101,35,'SEK');
INSERT INTO system.pspcurrency_tbl (countryid, pspid, name) VALUES (102,35,'NOK');
INSERT INTO system.pspcurrency_tbl (countryid, pspid, name) VALUES (104,35,'EUR');
INSERT INTO system.pspcurrency_tbl (countryid, pspid, name) VALUES (127,35,'DKK');
INSERT INTO system.pspcurrency_tbl (countryid, pspid, name) VALUES (130,35,'DKK');
INSERT INTO system.pspcurrency_tbl (countryid, pspid, name) VALUES (132,35,'ISK');

/*END: Adding Currency entries to the PSPCurrency_Tbl table for NETS*/

/* ========== CONFIGURE DEMO ACCOUNT FOR NETS START ========== */
-- Wire-Card
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 35, '9105bb4f-ae68-4768-9c3b-3eda968f57ea', '70000-APILUHN-CARD', '8mhwavKVb91T');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 35, '-1');

/* ========== CONFIGURE DEMO ACCOUNT FOR NETS END ====== */

/* Additional Properties for client, merchant and PSP */


/*====================== Test Data =========================*/
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('PROCESSING_CODE', '000000', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CARD_ACCEPTOR_BUSINESS_CODE', '4511
', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CARD_ACCEPTOR_IDENTIFICATION_CODE', '1234', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CARD_ACCEPTOR_NAME', 'Test', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CARD_ACCEPTOR_ADDRESS', 'Test', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CARD_ACCEPTOR_CITY', 'Test', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CARD_ACCEPTOR_ZIP', '123456', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CARD_ACCEPTOR_REGION', 'ABC', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CARD_ACCEPTOR_COUNTRY', 'ABC', 10007, 'client');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('POS_DATA_CODE', '1234', 206, 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('FUNCTION_CODE', 'FULL', 206, 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CARD_ACCEPTOR_TERMINAL_ID', 'ABCD1234', 206, 'merchant');
/*====================== Test Data END =========================*/

/* END */


ALTER TABLE log.transaction_tbl ADD mask VARCHAR(20) NULL;
ALTER TABLE log.transaction_tbl ADD expiry VARCHAR(5) NULL;
ALTER TABLE log.transaction_tbl ADD token CHARACTER VARYING(512) COLLATE pg_catalog."default" NULL;
ALTER TABLE log.transaction_tbl ADD authOriginalData CHARACTER VARYING(512) NULL;


ALTER TABLE enduser.address_tbl DROP CONSTRAINT address2state_fk;
ALTER TABLE enduser.address_tbl DROP stateid;
ALTER TABLE enduser.address_tbl ADD state VARCHAR(200);