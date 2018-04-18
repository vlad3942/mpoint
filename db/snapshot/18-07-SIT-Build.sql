------ Aggregated Payment Scripts mPoint ----------

ALTER TABLE log.transaction_tbl  DROP constraint transaction_tbl_producttype_tbl_id_fk;
ALTER TABLE Client.producttype_tbl DROP constraint product_fk;
DROP TABLE system.producttype_tbl;

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

/*  ===========  START : Adding producttype to Log.Transaction_Tbl  ==================  */
ALTER TABLE log.transaction_tbl ADD producttype INT DEFAULT 100 NOT NULL;
COMMENT ON COLUMN log.transaction_tbl.producttype IS 'Product type of transaction';
ALTER TABLE log.transaction_tbl
  ADD CONSTRAINT transaction_tbl_producttype_tbl_id_fk
FOREIGN KEY (producttype) REFERENCES system.producttype_tbl (id);
ALTER TABLE client.producttype_tbl ADD CONSTRAINT product_fk FOREIGN KEY (productid)
      REFERENCES system.ProductType_Tbl(id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;
	 
	 
ALTER TABLE system.ProductType_Tbl ADD COLUMN created timestamp without time zone DEFAULT now();
ALTER TABLE system.ProductType_Tbl ADD COLUMN  modified timestamp without time zone DEFAULT now();
ALTER TABLE system.ProductType_Tbl ADD COLUMN  enabled boolean DEFAULT true ; 
	  
	  
	  
	  /* ----------------Adding Configurations for CHUBB PSP - START ------------------------------ */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (48, 'CHUBB', 1);
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (702,48,'SGD');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (840,48,'USD');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (764,48,'THB');

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10018, 48, 'CHUBB', '', '');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100075, 48, '-1');
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (8, 48, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (7, 48, true);
/* ----------------Adding Configurations for CHUBB PSP - END ------------------------------ */