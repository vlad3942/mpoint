ALTER TABLE EndUser.Account_Tbl ADD attempts INT4 DEFAULT 0;

DROP INDEX EndUser.account_mobile_uq;
CREATE UNIQUE INDEX account_mobile_uq ON EndUser.Account_tbl (countryid, mobile, enabled) WHERE enabled = true;
CREATE UNIQUE INDEX account_email_uq ON EndUser.Account_tbl (countryid, email, enabled) WHERE enabled = true;

ALTER TABLE EndUser.Card_Tbl ADD name VARCHAR(50);