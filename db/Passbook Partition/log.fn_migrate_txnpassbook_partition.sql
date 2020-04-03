--truncate table log.txnpassbook_tbl_part
--select * from log.txnpassbook_tbl_part
--select * from log.fn_migrate_txnpassbook_partition(10018,1,1000000);
CREATE OR REPLACE FUNCTION log.fn_migrate_txnpassbook_partition(IN p_clientid bigint, IN p_start bigint, IN p_end bigint)
RETURNS void 
LANGUAGE plpgsql
AS
$function$
/*----------------------------------------------------------------------------------------------
		Procedure : log.fn_migrate_txnpassbook_partition()
		Version	  : v1.0
		Date		  : 2020-03-08
		Purpose 	  : fn_migrate_txnpassbook_partition based on passbookids
		Author	      : CPM (SWE/Sarvesh)
		Accepts     : Client id , Start Passbook id & End Passbook id 
		Flags	      :  1 - Success , 0 - Failure
------------------------------------------------------------------------------------------------*/
declare


	v_rows_processed_txnpassbook_tbl int8;
	
	v_cnt_txnpassbook_tbl int8:= 0;

	v_etl_status_flag int4;
	v_error_info varchar(1000);
	v_error_info1 varchar(1000);
	
	v_start  bigint :=p_start;
	v_end   bigint :=p_end;

	v_clientid bigint := p_clientid;

begin

SET LOCAL TEMP_BUFFERS='786MB';

	 RAISE NOTICE 'v_clientid: %',    v_clientid;
	 	 RAISE NOTICE 'v_start: %',    v_start;
		 	 RAISE NOTICE 'v_end: %',    v_end;

	insert into log.txnpassbook_tbl_part 
		(		
				id,
				clientid,
				transactionid,
				amount,
				currencyid,
				requestedopt,
				performedopt,
				status,
				extref,
				extrefidentifier,
				enabled,
				created,
				modified
		) 
		select
					tp.id,
					tt.clientid,
					tp.transactionid,
					tp.amount,
					tp.currencyid,
					tp.requestedopt,
					tp.performedopt,
					tp.status,
					tp.extref,
					tp.extrefidentifier,
					tp.enabled,
					tp.created,
					tp.modified
		from
					log.txnpassbook_tbl tp
		inner join  log.transaction_tbl tt 
		on (tp.transactionid=tt.id and tt.clientid=v_clientid) --61271
		and tp.id between v_start and v_end;  --61271

    GET DIAGNOSTICS v_rows_processed_txnpassbook_tbl = ROW_COUNT;

	 RAISE NOTICE 'v_rows_processed_txnpassbook_tbl: %',    v_rows_processed_txnpassbook_tbl;

	 				v_etl_status_flag := 1.;
SET LOCAL TEMP_BUFFERS=default;

EXCEPTION

		   --Catch All Other Errors 
		   WHEN OTHERS THEN

						GET STACKED DIAGNOSTICS v_error_info = MESSAGE_TEXT,
                          v_error_info1 = PG_EXCEPTION_DETAIL;
						  			   RAISE NOTICE 'v_error_info: %', v_error_info;
										RAISE NOTICE 'v_error_info1: %', v_error_info1;							
			
				v_cnt_txnpassbook_tbl := -1;
				v_etl_status_flag := 0.;
				v_error_info := '['||SQLSTATE ||']'||SQLERRM;--'Run Time Exception';

				RAISE WARNING '[log.fn_migrate_txnpassbook_partition] - UDP ERROR [OTHER] - SQLSTATE: %, SQLERRM: %', SQLSTATE, SQLERRM;

	
END;
$function$
;

