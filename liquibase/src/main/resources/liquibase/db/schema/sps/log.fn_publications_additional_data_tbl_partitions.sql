Drop function if exists log.fn_publications_additional_data_tbl_partitions 
(p_ods_table character varying,p_context character varying,p_nested character varying,p_period_from character varying,p_period_to character varying);
CREATE OR REPLACE FUNCTION log.fn_publications_additional_data_tbl_partitions
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
		Procedure  : log.fn_publications_additional_data_tbl_partitions()
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

IF p_nested='N' THEN

	create_query := 
			'ALTER PUBLICATION mpoint_log_pub ADD TABLE '||v_p_orig_table||'_'||v_context;

			RAISE NOTICE 'create_query : %',create_query ;
			EXECUTE create_query;
			

ELSIF  p_nested='Y' THEN

			
	FOR create_query IN SELECT
			'ALTER PUBLICATION mpoint_log_pub ADD TABLE  '
			||v_p_orig_table||'_'||v_context||'_'||to_char(d,'YYYYMM')
	FROM	generate_Series(v_period_from::timestamp,v_period_to::timestamp,'1 month'::interval) as d
	LOOP
	
		RAISE NOTICE 'create_query : %',create_query ;
		EXECUTE create_query;
	END LOOP;

	default_query :=
			'ALTER PUBLICATION mpoint_log_pub ADD TABLE '
			||v_p_orig_table||'_'||v_context||'_DEFAULT';

			RAISE NOTICE 'default_query : %',default_query ;
			EXECUTE default_query;


ELSIF  p_nested='-1' THEN

	FOR create_query IN SELECT
			'ALTER PUBLICATION mpoint_log_pub ADD TABLE  '
			||v_p_orig_table||'_'||v_context||'_'||to_char(d,'YYYYMM')
	FROM	generate_Series(v_period_from::timestamp,v_period_to::timestamp,'1 month'::interval) as d
	LOOP
	
		RAISE NOTICE 'create_query : %',create_query ;
		EXECUTE create_query;
	END LOOP;
	
		
END IF;
EXCEPTION
		   --Catch All Other Errors
		   WHEN OTHERS THEN
			
				v_error_info := '['||SQLSTATE ||']'||SQLERRM;--'Run Time Exception';
				RAISE WARNING '[log.fn_publications_additional_data_tbl_partitions] - UDP ERROR [OTHER] - SQLSTATE: %, SQLERRM: %', SQLSTATE, SQLERRM;
	
END;
$function$
;