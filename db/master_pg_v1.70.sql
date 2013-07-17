ALTER TABLE Log.Transaction_Tbl ADD points INT4;
ALTER TABLE Log.Transaction_Tbl ADD reward INT4;
ALTER TABLE EndUser.Account_Tbl ADD points INT4 DEFAULT 0;

DROP TRIGGER Modify_Transaction ON EndUser.Transaction_Tbl;
DROP FUNCTION Modify_EndUserTxn_Proc();
-- Trigger function for modifying the Transaction table.
-- The balance of either the End User's e-money account or the End-User's loyalty account
-- will be recalculated and stored in the "balance" or "points" field of EndUser.Account_Tbl
CREATE OR REPLACE FUNCTION Modify_EndUserTxn_Proc() RETURNS opaque AS
$BODY$
DECLARE
	iAccountID INT4;
	iTypeID INT4;
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
		UPDATE EndUser.Account_Tbl
		SET balance = (SELECT (Sum(amount) + Sum(Abs(fee) * -1) )
					   FROM EndUser.Transaction_Tbl
					   WHERE accountid = iAccountID AND 1000 <= typeid AND typeid <= 1003 AND enabled = true AND stateid != 1809)
		WHERE id = iAccountID;
	-- Update available balance on EndUser's loyalty account
	ELSIF 1004 <= iTypeID AND iTypeID <= 1007 THEN
		UPDATE EndUser.Account_Tbl
		SET points = (SELECT (Sum(amount) + Sum(Abs(fee) * -1) )
					   FROM EndUser.Transaction_Tbl
					   WHERE accountid = iAccountID AND 1004 <= typeid AND typeid <= 1007 AND enabled = true AND stateid != 1809)
		WHERE id = iAccountID;
	END IF;
	
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

ALTER TABLE Log.Transaction_Tbl ADD refund INT4 DEFAULT 0;

DROP INDEX EndUser.account_email_uq;
DROP INDEX EndUser.account_mobile_uq;
CREATE INDEX account_email_idx ON EndUser.Account_Tbl (countryid, upper(email), enabled) WHERE enabled = true;
CREATE INDEX account_mobile_idx ON EndUser.Account_Tbl (countryid, mobile, enabled) WHERE enabled = true;