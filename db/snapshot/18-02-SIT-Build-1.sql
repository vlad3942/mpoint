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
/*  ===========  END : Adding new product types in System.ProductType_Tbl ==================  */


/*  ===========  START : Adding producttype to Log.Transaction_Tbl  ==================  */
ALTER TABLE log.transaction_tbl ADD producttype INT DEFAULT 100 NOT NULL;
COMMENT ON COLUMN log.transaction_tbl.producttype IS 'Product type of transaction';
ALTER TABLE log.transaction_tbl
  ADD CONSTRAINT transaction_tbl_producttype_tbl_id_fk
FOREIGN KEY (producttype) REFERENCES system.producttype_tbl (id);

/*  ===========  END : Adding producttype to Log.Transaction_Tbl  ==================  */
