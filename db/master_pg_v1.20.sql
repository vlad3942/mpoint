-- Table: System.Shipping_Tbl
-- Data table for all available Shipping Companies that can deliver merchandise from a Shop
CREATE TABLE System.Shipping_Tbl
(
	id			SERIAL,

	name		VARCHAR(50),
	logourl		VARCHAR(100),

	CONSTRAINT Shipping_PK PRIMARY KEY (id),
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE UNIQUE INDEX Shipping_UQ ON System.Shipping_Tbl (Upper(name) );

CREATE TRIGGER Update_Shipping
BEFORE UPDATE
ON System.Shipping_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

-- Internal
INSERT INTO System.Shipping_Tbl (id, name, enabled) VALUES (0, 'System Record', false);

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE System.Shipping_Tbl TO mpoint;
GRANT SELECT, UPDATE, INSERT ON TABLE System.Shipping_Tbl_id_seq TO mpoint;


-- Table: Client.Shipping_Tbl
-- Link table identifying which Shipping Companies are available for a Shipping
CREATE TABLE Client.Shipping_Tbl
(
	id			SERIAL,
	Shippingid	INT4 NOT NULL,	-- ID of the Shipping Company that delivers merchandise bought in the Shop
	shopid		INT4 NOT NULL,	-- ID of the Shop

	cost		INT4,			-- Cost of Shipping in Countrys smallest currency
	free_ship	INT4,			-- Min amount for Orders for Free Shipping

	CONSTRAINT Shipping_PK PRIMARY KEY (id),
	CONSTRAINT Shipping2Shipping_FK FOREIGN KEY (shippingid) REFERENCES System.Shipping_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT Shipping2Shop_FK FOREIGN KEY (shopid) REFERENCES Client.Shop_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE TRIGGER Update_Shipping
BEFORE UPDATE
ON Client.Shipping_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

-- Internal
INSERT INTO Client.Shipping_Tbl (id, shippingid, shopid, enabled) VALUES (0, 0, 0, false);

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE Client.Shipping_Tbl TO mpoint;
GRANT SELECT, UPDATE, INSERT ON TABLE Client.Shipping_Tbl_id_seq TO mpoint;

ALTER TABLE Client.Shop_Tbl DROP shipping;
ALTER TABLE Client.Shop_Tbl DROP ship_cost;
ALTER TABLE Client.Shop_Tbl DROP free_ship;