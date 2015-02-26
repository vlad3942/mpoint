/* ========== MPOINT V1.84 START ========== */
ALTER TABLE Client_Ownr.Client_Tbl ADD identification NUMBER(10,0) DEFAULT 7;

CREATE INDEX account_active_email_idx ON enduser_Ownr.account_tbl (countryid, Upper(email) );
CREATE INDEX account_active_mobile_idx ON enduser_Ownr.account_tbl (countryid, mobile);
CREATE INDEX account_active_externalid_idx ON enduser_Ownr.account_tbl (countryid, externalid);
CREATE INDEX account_valid_externalid_idx ON enduser_Ownr.account_tbl (externalid);

CREATE INDEX merchant_account_idx ON Client_Ownr.MerchantSubAccount_Tbl (accountid, pspid, enabled);
/* ========== MPOINT V1.84 END ========== */

/* ========== MPOINT V1.85 START ========== */
ALTER TABLE Log_Ownr.Transaction_Tbl ADD fee NUMBER(10,0) DEFAULT 0;
/* ========== MPOINT V1.85 END ========== */

/* ========== MPOINT V1.86 START ========== */
ALTER TABLE Log_Ownr.Transaction_Tbl ADD captured NUMBER(19,0) DEFAULT 0;
/* ========== MPOINT V1.86 END ========== */

/* ========== MPOINT V1.87 START ========== */
ALTER TABLE Log_Ownr.Transaction_Tbl MODIFY (amount NUMBER(19,0) );
/* ========== MPOINT V1.87 END ========== */