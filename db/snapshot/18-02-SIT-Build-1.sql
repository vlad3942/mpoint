/*  ===========  START : Adding Table System.ProductType_Tbl  ==================  */
CREATE TABLE system.ProductType_Tbl
(
  id INT PRIMARY KEY,
  name VARCHAR(10) NOT NULL
);
CREATE UNIQUE INDEX ProductType_Tbl_name_uindex ON system.ProductType_Tbl (name);
COMMENT ON COLUMN system.ProductType_Tbl.id IS 'Unique number of product type';
COMMENT ON COLUMN system.ProductType_Tbl.name IS 'Product type name';
COMMENT ON TABLE system.ProductType_Tbl IS 'Contains all product types';

ALTER TABLE system.ProductType_Tbl
  OWNER TO mpoint;

/*  ===========  END : Adding Table System.ProductType_Tbl  ==================  */

/*  ===========  START : Adding new product types in System.ProductType_Tbl ==================  */
INSERT INTO system.producttype_tbl (id, name) VALUES (100, 'Ticket');
INSERT INTO system.producttype_tbl (id, name) VALUES (200, 'Ancillary');
INSERT INTO system.producttype_tbl (id, name) VALUES (210, 'Insurance');
/*  ===========  END : Adding new product types in System.ProductType_Tbl ==================  */


/*  ===========  START : Adding producttype to Log.Transaction_Tbl  ==================  */
ALTER TABLE log.transaction_tbl ADD producttype INT DEFAULT 100 NOT NULL;
COMMENT ON COLUMN log.transaction_tbl.producttype IS 'Product type of transaction';
ALTER TABLE log.transaction_tbl
  ADD CONSTRAINT transaction_tbl_producttype_tbl_id_fk
FOREIGN KEY (producttype) REFERENCES system.producttype_tbl (id);

/*  ===========  END : Adding producttype to Log.Transaction_Tbl  ==================  */

/*  ===========  START : Adding New Processor Type  ==================  */
INSERT INTO system.processortype_tbl (id, name) VALUES (6, 'Merchant Plug-in');
/*  ===========  END : Adding New Processor Type  ==================  */

INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (42, 'NETS MPI',6);
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (840,42,'USD');
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 42, 'NETS MPI', '', '');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 42, '-1');
INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position) VALUES (10007, 8, true, 42, 200, 1, null);

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('NETS_3DVERIFICATION', 'true', 10007, 'client');

ALTER TABLE client.cardaccess_tbl ADD psp_type INT DEFAULT 1 NOT NULL;
ALTER TABLE client.cardaccess_tbl
  ADD CONSTRAINT cardaccess_tbl_processortype_tbl_id_fk
FOREIGN KEY (psp_type) REFERENCES system.processortype_tbl (id);
DROP INDEX client.cardaccess_card_country_uq RESTRICT;
UPDATE client.cardaccess_tbl
SET psp_type = psp_tbl.system_type
FROM system.psp_tbl
WHERE psp_tbl.id = cardaccess_tbl.pspid;
CREATE UNIQUE INDEX cardaccess_card_country_uq ON client.cardaccess_tbl (clientid, cardid, countryid, psp_type);
