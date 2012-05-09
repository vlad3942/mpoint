ALTER TABLE Client.MerchantAccount_Tbl ADD username VARCHAR(50);
ALTER TABLE Client.MerchantAccount_Tbl ADD passwd VARCHAR(50);

CREATE INDEX message_transaction_state ON Log.Message_Tbl (txnid, stateid);
CREATE UNIQUE INDEX account_email_uq ON enduser.account_tbl (countryid , Upper(email) , enabled ) WHERE enabled = true;
CREATE INDEX claccess_account ON EndUser.CLAccess_Tbl (accountid);