/*START: Fixing incorrect values for USD in PSPCurrency_tbl in system schema */

UPDATE system.pspcurrency_tbl
SET name = currency_tbl.code
FROM system.currency_tbl
WHERE currency_tbl.id = pspcurrency_tbl.currencyid;

/*END: Fixing incorrect values for USD in PSPCurrency_tbl in system schema*/
