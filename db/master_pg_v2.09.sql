ALTER TABLE  client.cardaccess_tbl ADD installment INT DEFAULT 0 NOT NULL;

COMMENT ON COLUMN  client.cardaccess_tbl.installment
IS
'Default 0 - No installment option
1 - Offline Installment';


ALTER TABLE client.client_tbl ADD installment INT DEFAULT 0 NULL;
COMMENT ON COLUMN client.client_tbl.installment IS 'Default to 0 installment not enabled
1 - offline Installments';


ALTER TABLE client.client_tbl ADD max_installments INT DEFAULT 0 NULL;
COMMENT ON COLUMN client.client_tbl.max_installments IS 'Max number of installments allowed,
Usually set by Acq';

ALTER TABLE client.client_tbl ADD installment_frequency INT DEFAULT 0 NULL;
COMMENT ON COLUMN client.client_tbl.installment_frequency IS 'defines the time frame for installment,
like 1- monthly, 3 - quarterly, 6 - semiannual.
For merchant financed is usually monthly ';

ALTER TABLE log.transaction_tbl ADD installment_value INT DEFAULT 0 NULL;
COMMENT ON COLUMN log.transaction_tbl.installment_value IS 'Installment value is the number of installments selected by the user';