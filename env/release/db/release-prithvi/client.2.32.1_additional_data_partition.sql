/*---------------------------------------------------------------------------
Execution script
Version	    : v1.1
Date 	    : 2021-11-05
Author	    : CPM (SWE/Sarvesh)
----------------------------------------------------------------------------*/

--Start

--1)
select * from log.fn_generate_additional_data_tbl_partitions('log.stg_additional_data_tbl','Passenger','-1','20170101','20181201');
select * from log.fn_generate_additional_data_tbl_partitions('log.stg_additional_data_tbl','Flight','-1','20170101','20181201');
select * from log.fn_generate_additional_data_tbl_partitions('log.stg_additional_data_tbl','Order','-1','20170101','20181201');

--@call
select * from log.fn_rename_additional_data_tbl_partitions('log.stg_additional_data_tbl','Session','Y','20190101','20220401');
select * from log.fn_rename_additional_data_tbl_partitions('log.stg_additional_data_tbl','Passenger','Y','20170101','20220401');
select * from log.fn_rename_additional_data_tbl_partitions('log.stg_additional_data_tbl','Flight','Y','20170101','20220401');
select * from log.fn_rename_additional_data_tbl_partitions('log.stg_additional_data_tbl','Transaction','Y','20190101','20220401');
select * from log.fn_rename_additional_data_tbl_partitions('log.stg_additional_data_tbl','Order','Y','20170101','20220401');

select * from log.fn_permissions_additional_data_tbl_partitions('log.additional_data_tbl','Session','Y','20190101','20220401');
select * from log.fn_permissions_additional_data_tbl_partitions('log.additional_data_tbl','Passenger','Y','20170101','20220401');
select * from log.fn_permissions_additional_data_tbl_partitions('log.additional_data_tbl','Flight','Y','20170101','20220401');
select * from log.fn_permissions_additional_data_tbl_partitions('log.additional_data_tbl','Transaction','Y','20190101','20220401');
select * from log.fn_permissions_additional_data_tbl_partitions('log.additional_data_tbl','Order','Y','20170101','20220401');

--2)
call log.test_sp_migrate_additional_data('Transaction',1000000,'I');
call log.test_sp_migrate_additional_data('Session',1000000,'I');
call log.test_sp_migrate_additional_data('Passenger',1000000,'I');
call log.test_sp_migrate_additional_data('Flight',1000000,'I');
call log.test_sp_migrate_additional_data('Order',10000,'I');

--3)
call log.test_sp_migrate_additional_data('Transaction',1000000,'D');
call log.test_sp_migrate_additional_data('Session',1000000,'D');
call log.test_sp_migrate_additional_data('Passenger',1000000,'D');
call log.test_sp_migrate_additional_data('Flight',1000000,'D');
call log.test_sp_migrate_additional_data('Order',1000000,'D');

--4)
call log.test_sp_migrate_additional_data('Transaction',1000000,'D');
call log.test_sp_migrate_additional_data('Session',1000000,'D');
call log.test_sp_migrate_additional_data('Passenger',1000000,'D');
call log.test_sp_migrate_additional_data('Flight',1000000,'D');
call log.test_sp_migrate_additional_data('Order',1000000,'D');

--5)
SELECT setval('log.stg_additional_data_tbl_id_seq', (SELECT MAX(id) FROM log.additional_data_tbl)+10000);
ALTER PUBLICATION mpoint_log_pub DROP TABLE log.additional_data_tbl;

--6)
alter table log.additional_data_tbl
rename to backup_additional_data_tbl;

--7)
alter table log.stg_additional_data_tbl
rename to additional_data_tbl;

--8)--Pd
call log.test_sp_migrate_additional_data('Transaction',1000000,'H');
call log.test_sp_migrate_additional_data('Session',1000000,'H');
call log.test_sp_migrate_additional_data('Passenger',1000000,'H');
call log.test_sp_migrate_additional_data('Flight',1000000,'H');
call log.test_sp_migrate_additional_data('Order',1000000,'H');

--9)
select * from log.fn_publications_additional_data_tbl_partitions('log.additional_data_tbl','Session','Y','20190101','20220401');
select * from log.fn_publications_additional_data_tbl_partitions('log.additional_data_tbl','Passenger','Y','20170101','20220401');
select * from log.fn_publications_additional_data_tbl_partitions('log.additional_data_tbl','Flight','Y','20170101','20220401');
select * from log.fn_publications_additional_data_tbl_partitions('log.additional_data_tbl','Transaction','Y','20190101','20220401');
select * from log.fn_publications_additional_data_tbl_partitions('log.additional_data_tbl','Order','Y','20170101','20220401');

--10)@dw