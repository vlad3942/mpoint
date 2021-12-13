Drop function if exists log.fn_rename_additional_data_tbl_partitions 
(p_ods_table character varying,p_context character varying,p_nested character varying,p_period_from character varying,p_period_to character varying);
CREATE OR REPLACE FUNCTION log.fn_rename_additional_data_tbl_partitions
(
p_ods_table character varying,
p_context character varying,
p_nested character varying DEFAULT 'N' ,
p_period_from varchar DEFAULT'20190101',
p_period_to varchar DEFAULT'20200101'
)
RETURNS void
LANGUAGE plpgsql
AS
$function$
/*------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		Procedure  : log.fn_rename_additional_data_tbl_partitions()
		Version	    : v1.1
		Date		    : 2021-10-22
		Purpose 	: Generates publications for Partitions(Basic/Nested) for the configurations
		Author	    : CPM (SWE/Sarvesh)
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
DECLARE

create_query text;
index_query text;
alter_query text;
default_query text;

--v_p_ods_table varchar(50):=p_ods_table;
v_p_orig_table varchar(50):=p_ods_table;
v_context varchar(200):=p_context;
v_period_from varchar(50):=p_period_from;
v_period_to varchar(50):=p_period_to;
v_error_info text;

BEGIN

/*
alter table log.stg_additional_data_tbl_flight_201701
rename to additional_data_tbl_flight_201701;
*/

	FOR create_query IN SELECT
			'ALTER TABLE  ' ||v_p_orig_table||'_'||v_context||'_'||to_char(d,'YYYYMM')
			||' RENAME TO ' ||substr(v_p_orig_table,9) ||'_'||v_context||'_'||to_char(d,'YYYYMM')
	FROM	generate_Series(v_period_from::timestamp,v_period_to::timestamp,'1 month'::interval) as d
	LOOP
	
		RAISE NOTICE 'create_query : %',create_query ;
		EXECUTE create_query;
	END LOOP;
	
	
	create_query :=
			'ALTER TABLE  ' ||v_p_orig_table||'_'||v_context
			||' RENAME TO ' ||substr(v_p_orig_table,9) ||'_'||v_context;

			RAISE NOTICE 'create_query : %',create_query ;
			EXECUTE create_query;
			
	default_query :=
			'ALTER TABLE  ' ||v_p_orig_table||'_'||v_context||'_DEFAULT'
			||' RENAME TO ' ||substr(v_p_orig_table,9) ||'_'||v_context||'_DEFAULT';

			RAISE NOTICE 'default_query : %',default_query ;
			EXECUTE default_query;


EXCEPTION
		   --Catch All Other Errors
		   WHEN OTHERS THEN
			
				v_error_info := '['||SQLSTATE ||']'||SQLERRM;--'Run Time Exception';
				RAISE WARNING '[log.fn_rename_additional_data_tbl_partitions] - UDP ERROR [OTHER] - SQLSTATE: %, SQLERRM: %', SQLSTATE, SQLERRM;
	
END;
$function$
;