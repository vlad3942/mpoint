-- Settlement Improvement
ALTER TABLE system.psp_tbl ADD capture_method int DEFAULT 0;
COMMENT ON COLUMN system.psp_tbl.capture_method IS '0 - manual
2 - bulk capture
3 - bulk refund
6 - bulk capture + bulk  refund';

-- Update AMEX PSP
UPDATE system.psp_tbl SET  capture_method = 6 WHERE id = 45;