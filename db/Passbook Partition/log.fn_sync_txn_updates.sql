/*--select * from log.fn_sync_txn_updates('log.txnpassbook_tbl',10018);--,1,10000000,1000000);
#For Client :10018 20043242
select * from log.fn_sync_txn_updates(10018,18625332,20043242); --march batch
select * from log.fn_sync_txn_updates(10018,15964034,18625332); --feb month
select * from log.fn_sync_txn_updates(10018,12773455,15964034); --jan month
select * from log.fn_sync_txn_updates(10018,10127766,12773455); --dec month
select * from log.fn_sync_txn_updates(10018,7611338,10127766);   --nov month
*/
CREATE OR REPLACE FUNCTION log.fn_sync_txn_updates(p_client_id integer,p_id_start bigint,p_id_end bigint)
RETURNS void 
LANGUAGE plpgsql
AS
$function$
/*------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		Procedure : log.fn_sync_txn_updates()
		Version	  : v1.1
		Date		  : 2020-03-10
		Purpose 	  : Syncs updates for the client between the ranges
		Author	      : CPM (SWE/Sarvesh)
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
declare
create_query text;
index_query text;

v_rows_processed bigint;

v_Client_id int4:=p_client_id;
--v_Dim_From bigint:=p_dim_from;
--v_Dim_To bigint:=p_dim_to;
--v_size bigint:= p_size;

v_error_info text;

begin
/*
For each client:  (if required consider 15 days or 7 days batch)
2019-11-01 =>  7611338 (batch 5)
2019-12-01 => 10127766 (batch 4)
2020-01-01 => 12773455 (batch 3)
2020-02-01 => 15964034 (batch 2)
2020-03-01 => 18625332 (batch 1)
		   => 20043242 (select max(id)
						from log.txnpassbook_tbl tp
						where tp.created >= '2020-03-01')
*/

set local temp_buffers='512MB';
set local work_mem='512MB';

with delta_update 
as
(
select tp.id,tp.status,tp.modified from log.txnpassbook_tbl tp
inner join  log.transaction_tbl tt 
on (tp.transactionid=tt.id and tt.clientid=p_client_id) --61271
where 
tp.id between p_id_start and p_id_end
--and tp.id=77818
except
select tpp.id,tpp.status,tpp.modified from log.txnpassbook_tbl_part tpp
where tpp.clientid=p_client_id
and tpp.id between p_id_start and p_id_end
--and tp.id=77818
)
update log.txnpassbook_tbl_part tpp
set (status,modified)=(select status, modified from delta_update where id=tpp.id)
where clientid=p_client_id
and tpp.id between p_id_start and p_id_end
and exists (select 1 from delta_update where id=tpp.id);

GET DIAGNOSTICS v_rows_processed = ROW_COUNT;

			RAISE NOTICE 'p_client_id : %',p_client_id ;
			RAISE NOTICE 'p_id_start : %',p_id_start ;
						RAISE NOTICE 'p_id_end : %',p_id_end ;
						RAISE NOTICE 'v_rows_processed : %',v_rows_processed ;

set local temp_buffers=default;
set local work_mem=default;


EXCEPTION
		   --Catch All Other Errors 
		   WHEN OTHERS THEN
			
				v_error_info := '['||SQLSTATE ||']'||SQLERRM;--'Run Time Exception';

				RAISE WARNING '[log.fn_sync_txn_updates] - UDP ERROR [OTHER] - SQLSTATE: %, SQLERRM: %', SQLSTATE, SQLERRM;
	
END;
$function$
;
