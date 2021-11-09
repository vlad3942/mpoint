DROP PROCEDURE if exists log.sp_migrate_additional_data 
(IN p_context varchar, IN p_externalid_from bigint, IN p_externalid_end bigint,IN p_batch_size bigint);
DROP PROCEDURE if exists log.sp_migrate_additional_data 
(IN p_context varchar, IN p_batch_size bigint, IN p_load_type varchar);
CREATE OR REPLACE PROCEDURE log.test_sp_migrate_additional_data
(IN p_context varchar, IN p_batch_size bigint, IN p_load_type varchar)
LANGUAGE plpgsql
AS
$procedure$
/*-----------------------------------------------------------------------------------------------------------------------------------------
		Procedure : log.sp_migrate_additional_data()
		Version	    : v1.1
		Date		    : 2021-10-21
		Purpose 	: migrates additional data to partition model removing duplicates in batch mode
		Author	    : CPD (SWE/Sarvesh)
		Accepts    : Context, External id ranges, Size 
		Flags	    : 1 - Success , 0 - Failure
------------------------------------------------------------------------------------------------------------------------------------------*/
declare

	v_rows_processed int8;
	
	v_etl_status_flag int4;
	v_error_info varchar(1000);
	v_error_info1 varchar(1000);
	
	v_start  bigint ;
	v_end   bigint ;
	v_end_max bigint;

	v_context log.additional_data_ref := p_context;
	
	v_batch_size bigint := p_batch_size;
	
	v_latest bigint;
	v_last    bigint;
	

begin

SET LOCAL TEMP_BUFFERS='786MB';
SET LOCAL WORK_MEM='812MB';

IF p_load_type='I'
THEN

select 
min(id),
max(id) 
into 
v_start, 
v_end
from log.additional_data_tbl adt 
where type=v_context;


v_end_max := v_end;


	 RAISE NOTICE 'load type: %',    p_load_type;
	 RAISE NOTICE 'v_context: %',    v_context;
	 	 RAISE NOTICE 'v_start: %',    v_start;
		 	 RAISE NOTICE 'v_end: %',    v_end;
			 		 	 RAISE NOTICE 'v_end_max: %',    v_end_max;

v_end  := (v_start-1) +p_batch_size;

LOOP

RAISE NOTICE 'Interim v_start : %', v_start;
RAISE NOTICE 'Interim v_end : %', v_end;
RAISE NOTICE 'Interim v_end_max : %', v_end_max;


insert into log.stg_additional_data_tbl 
(id,type, externalid, name,value, created, modified)
select distinct on (type, externalid, name) id, type, 
externalid, name,value, created, modified 
from log.additional_data_tbl
where 
type=v_context
and id between v_start and v_end
on conflict do nothing;


    GET DIAGNOSTICS v_rows_processed = ROW_COUNT;

	 RAISE NOTICE 'v_rows_processed : %',    v_rows_processed;
	 
	 COMMIT;
	 

v_start := v_end+1;
v_end := (v_start-1) +p_batch_size;

exit when v_start >= v_end_max;

end loop;


ELSIF p_load_type='D'
THEN

select 
max(id) +1
into 
v_start
from log.stg_additional_data_tbl adt 
where type=v_context::varchar;

select 
max(id)
into 
v_end
from log.additional_data_tbl adt 
where type=v_context
and id >= v_start -1;


	 RAISE NOTICE 'load type: %',    p_load_type;
	 RAISE NOTICE 'v_context: %',    v_context;
	 	 RAISE NOTICE 'v_start: %',    v_start-1;
		 	 RAISE NOTICE 'v_end: %',    v_end;
			 
insert into log.stg_additional_data_tbl 
(id,type, externalid, name,value, created, modified)
select distinct on (type, externalid, name) id, type, 
externalid, name,value, created, modified 
from log.additional_data_tbl
where 
type=v_context
and id between v_start and v_end
on conflict do nothing;



    GET DIAGNOSTICS v_rows_processed = ROW_COUNT;

	 RAISE NOTICE 'v_rows_processed : %',    v_rows_processed;
	 
	 COMMIT;

ELSIF p_load_type='H'
THEN	 

select 
max(id) +1
into 
v_start
from log.stg_additional_data_tbl adt 
where type=v_context::varchar;

select 
max(id)
into 
v_end
from log.backup_additional_data_tbl adt 
where type=v_context
and id >= v_start -1;


	 RAISE NOTICE 'load type: %',    p_load_type;
	 RAISE NOTICE 'v_context: %',    v_context;
	 	 RAISE NOTICE 'v_start: %',    v_start-1;
		 	 RAISE NOTICE 'v_end: %',    v_end;
			 
insert into log.additional_data_tbl 
(id,type, externalid, name,value, created, modified)
select distinct on (type, externalid, name) id, type, 
externalid, name,value, created, modified 
from log.backup_additional_data_tbl
where 
type=v_context
and id between v_start and v_end
on conflict do nothing;



    GET DIAGNOSTICS v_rows_processed = ROW_COUNT;

	 RAISE NOTICE 'v_rows_processed : %',    v_rows_processed;
	 
	 COMMIT;

END IF;

	 				v_etl_status_flag := 1.;

SET LOCAL TEMP_BUFFERS=default;
SET LOCAL WORK_MEM=default;


/*
EXCEPTION

		   --Catch All Other Errors 
		   WHEN OTHERS THEN


						GET STACKED DIAGNOSTICS v_error_info = MESSAGE_TEXT,
                          v_error_info1 = PG_EXCEPTION_DETAIL;
						  			   RAISE NOTICE 'v_error_info: %', v_error_info;
										RAISE NOTICE 'v_error_info1: %', v_error_info1;							
			

				v_error_info := '['||SQLSTATE ||']'||SQLERRM;--'Run Time Exception';

				RAISE WARNING '[log.sp_migrate_additional_data] - UDP ERROR [OTHER] - SQLSTATE: %, SQLERRM: %', SQLSTATE, SQLERRM;

  ROLLBACK;

*/


	
END;
$procedure$
;
