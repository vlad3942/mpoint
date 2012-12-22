
-- Table: System.CardPrefix_Tbl
-- Data table for all Card Prefixes
CREATE TABLE System.CardPrefix_Tbl
(
	id			SERIAL,
	cardid		INT4 NOT NULL,	-- ID of the card the prefix is for

	min			INT8,
	max			INT8,

	CONSTRAINT CardPrefix_PK PRIMARY KEY (id),
	CONSTRAINT CardPrefix2Card_FK FOREIGN KEY (cardid) REFERENCES System.Card_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
	LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE TRIGGER Update_CardPrefix
BEFORE UPDATE
ON System.CardPrefix_Tbl FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE System.CardPrefix_Tbl TO mpoint;
GRANT SELECT, UPDATE, INSERT ON TABLE System.CardPrefix_Tbl_id_seq TO mpoint;

ALTER TABLE System.Card_Tbl ADD minlength INT4;
ALTER TABLE System.Card_Tbl ADD maxlength INT4;
ALTER TABLE System.Card_Tbl ADD cvclength INT4;