/*START: Fixing incorrect values for USD in PSPCurrency_tbl in system schema */

update system.pspcurrency_tbl set name = 'USD' where currencyid=840
update system.pspcurrency_tbl set currencyid = 840 where name = 'USD'

/*END: Fixing incorrect values for USD in PSPCurrency_tbl in system schema*/
