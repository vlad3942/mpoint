/* Ticket level transaction - Add new column fees in log.order_tbl */
ALTER TABLE Log.order_tbl ADD COLUMN fees integer DEFAULT 0;