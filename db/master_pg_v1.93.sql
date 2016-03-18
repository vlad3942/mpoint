/* ==================== Client SCHEMA START ==================== */
ALTER TABLE Client.Client_Tbl ADD num_masked_digits INT4 DEFAULT 4;
ALTER TABLE Client.Client_Tbl ADD CONSTRAINT MaskedDigits_Chk CHECK (0 <= num_masked_digits AND num_masked_digits <= 4);
/* ==================== Client SCHEMA END ==================== */

